<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Models\Bank;
use App\Models\Unit;
use App\Models\Stock;
use App\Models\Product;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\StockItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\SupplierPayment;
use App\Http\Controllers\Controller;
use Sudip\MediaUploader\Facades\MediaUploader;

class StockController extends Controller
{
    public function index(Request $request)
    {
        $sql = Stock::orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where('challan_number', 'LIKE', $request->q . '%');
        }

        if ($request->supplier) {
            $sql->where('supplier_id', $request->supplier);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.stock.index', compact('serial', 'records', 'suppliers'));
    }

    public function create()
    {
        $items = [
            (object)[
                'id' => null,
                'category_id' => null,
                'product_id' => null,
                'unit_id' => null,
                'quantity' => null,
                'unit_price' => null,
                'amount' => null,
            ]
        ];

        $suppliers = Supplier::where('status', 'Active')->get();
        $products = Product::with(['baseUnit.units'])->where('status', 'Active')->get();
        $categories = Category::with('products', 'children.products')->get();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.create', compact('items', 'suppliers', 'products', 'units', 'banks', 'categories'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'date' => 'required|date',
            'challan_number' => 'nullable|max:255',
            'challan_image' => 'nullable|image|mimes:jpeg,jpg,png',
            'challan_date' => 'nullable|date',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'required|integer',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
        ]);

        if ($request->paid_amount > 0) {
            $this->validate($request, [
                'bank_id' => 'required|integer',
            ]);
        }

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'date' => dbDateFormat($request->date),
            'challan_number' => $request->challan_number,
            'challan_date' => $request->challan_date,
            'total_quantity' => $request->total_quantity,
            'subtotal_amount' => $request->subtotal_amount,
            'discount_amount' => $request->discount_amount,
            'total_amount' => $request->total_amount,
        ];

        $data = Stock::create($storeData);

        if ($request->only('stock_item_id')) {
            $sdata = [];
            foreach ($request->stock_item_id as $key => $row) {
                $unit = Unit::find($request->unit_id[$key]);

                $sdata[] = [
                    'stock_id' => $data->id,
                    'category_id' => $request->category_id[$key],
                    'product_id' => $request->product_id[$key],
                    'unit_id' => $request->unit_id[$key],
                    'unit_quantity' => $unit->quantity,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'amount' => $request->amount[$key],
                    'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                    'created_at' => now(),
                ];
            }

            StockItem::insert($sdata);
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'supplier_id' => $data->supplier_id,
                'type' => 'Payment',
                'date' => dbDateFormat($data->date),
                'note' => null,
                'amount' => $request->paid_amount,
                'stock_id' => $data->id,
            ];

            $payment = SupplierPayment::create($paidData);

            if ($payment) {
                Transaction::create([
                    'type' => 'Payment',
                    'flag' => 'Supplier Payment',
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\SupplierPayment',
                    'bank_id' => $request->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        }

        if ($data) {
            session()->flash('successMessage', 'Stock was successfully added.');
        } else {
            session()->flash('errorMessage', 'Stock saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show($id)
    {
        $data = Stock::select('stocks.*', 'transactions.bank_id', 'supplier_payments.amount AS paid_amount')->with('items')

            ->leftJoin('supplier_payments', function ($q) {
                $q->on('supplier_payments.stock_id', '=', 'stocks.id');
            })
            ->leftJoin('transactions', function ($q) {
                $q->on('transactions.flagable_id', '=', 'supplier_payments.id');
                $q->where('transactions.flag', 'Supplier Payment');
            })
            ->findOrFail($id);

        return view('admin.stock.show', compact('data'))->with('show', $id);
    }

    public function edit($id)
    {
        $data = Stock::select('stocks.*', 'transactions.bank_id', 'supplier_payments.amount AS paid_amount')->with('items')
            ->leftJoin('supplier_payments', function ($q) {
                $q->on('supplier_payments.stock_id', '=', 'stocks.id');
            })
            ->leftJoin('transactions', function ($q) {
                $q->on('transactions.flagable_id', '=', 'supplier_payments.id');
                $q->where('transactions.flag', 'Supplier Payment');
            })
            ->findOrFail($id);


        if ($data->items != null) {
            $items = $data->items;
        } else {
            $items = [
                (object)[
                    'id' => null,
                    'category_id' => null,
                    'product_id' => null,
                    'unit_id' => null,
                    'quantity' => null,
                    'unit_price' => null,
                    'amount' => null,
                ]
            ];
        }

        $suppliers = Supplier::where('status', 'Active')->get();
        $products = Product::with(['baseUnit.units'])->where('status', 'Active')->get();
        $categories = Category::with('products', 'children.products')->get();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.stock.edit', compact('data', 'items', 'suppliers', 'products', 'units', 'banks', 'categories'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'date' => 'required|date',
            'challan_number' => 'nullable|max:255',
            'challan_image' => 'nullable|image|mimes:jpeg,jpg,png',
            'challan_date' => 'nullable|date',
            'category_id' => 'required|array|min:1',
            'category_id.*' => 'required|integer',
            'product_id' => 'required|array|min:1',
            'product_id.*' => 'required|integer',
            'unit_id' => 'required|array|min:1',
            'unit_id.*' => 'required|integer',
            'quantity' => 'required|array|min:1',
            'quantity.*' => 'required|numeric',
            'unit_price' => 'required|array|min:1',
            'unit_price.*' => 'required|numeric',
            'amount' => 'required|array|min:1',
            'amount.*' => 'required|numeric',
        ]);

        if ($request->paid_amount > 0) {
            $this->validate($request, [
                'bank_id' => 'required|integer',
            ]);
        }

        $data = Stock::find($id);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'date' => dbDateFormat($request->date),
            'challan_number' => $request->challan_number,
            'challan_date' => $request->challan_date,
            'total_quantity' => $request->total_quantity,
            'subtotal_amount' => $request->subtotal_amount,
            'discount_amount' => $request->discount_amount,
            'total_amount' => $request->total_amount,
        ];

        $updated =  $data->update($storeData);

        if ($request->only('stock_item_id')) {
            StockItem::where('stock_id', $data->id)->whereNotIn('id', $request->stock_item_id)->delete();
            foreach ($request->stock_item_id as $key => $row) {
                $unit = Unit::find($request->unit_id[$key]);

                $updateData = [
                    'stock_id' => $data->id,
                    'category_id' => $request->category_id[$key],
                    'product_id' => $request->product_id[$key],
                    'unit_id' => $request->unit_id[$key],
                    'unit_quantity' => $unit->quantity,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key],
                    'amount' => $request->amount[$key],
                    'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                    'amount' => $request->amount[$key],
                ];

                if ($row > 0) {
                    StockItem::where('id', $row)->update($updateData);
                } else {
                    StockItem::create($updateData);
                }
            }
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'supplier_id' => $data->supplier_id,
                'type' => 'Payment',
                'date' => dbDateFormat($data->date),
                'note' => null,
                'amount' => $request->paid_amount,
                'stock_id' => $data->id,
            ];

            $payment = SupplierPayment::updateOrCreate(['stock_id' => $data->id], $paidData);

            if ($payment) {
                Transaction::updateOrCreate([
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\SupplierPayment',
                ], [
                    'type' => 'Payment',
                    'flag' => 'Supplier Payment',
                    'bank_id' => $request->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        } else {
            $payment = SupplierPayment::where('stock_id', $data->id)->first();
            if (!empty($payment)) {
                Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();
                $payment->delete();
            }
        }

        if ($updated) {
            session()->flash('successMessage', 'Stock was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Stock update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function getProductsByCategory($category)
    {
        $categoryByProduct = Product::with('baseUnit.units')->where('category_id', $category)->get();
        if (!$categoryByProduct) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'products' => $categoryByProduct,
        ]);
    }


    public function destroy($id)
    {
        $data = Stock::find($id);

        $payment = SupplierPayment::where('stock_id', $data->id)->first();

        if (!empty($payment)) {
            Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\SupplierPayment')->delete();
            $payment->delete();
        }

        StockItem::where('stock_id', $id)->delete();
        $data->delete();

        session()->flash('successMessage', 'Stock was successfully deleted!');

        return redirect()->action([self::class, 'index'], qArray());
    }
}
