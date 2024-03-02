<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Models\Bank;
use App\Models\Sale;
use App\Models\Unit;
use App\Models\Product;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Services\CodeService;
use App\Models\CustomerPayment;
use App\Http\Requests\SaleRequest;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class SaleController extends Controller
{

    public function index(Request $request)
    {
        $sql = Sale::orderBy('date', 'DESC')->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('invoice_no', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('total_quantity', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('total_amount', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('customer', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('items.category', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('items.product', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }

        if ($request->customer) {
            $sql->where('customer_id', $request->customer);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        $customers = Customer::where('status', 'Active')->get();

        return view('admin.sale.index', compact('records', 'serial', 'customers'));
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
                'discount_percentage' => null,
                'discount_amount' => null,
                'amount' => null,
            ]
        ];

        $customers = Customer::where('status', 'Active')->get();
        $products = Product::where('status', 'Active')->get();
        $categories = Category::with('products', 'children.products')->where('status', 'Active')->get();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.create', compact('items', 'customers', 'products', 'categories', 'units', 'banks'));
    }

    public function store(SaleRequest $request)
    {

        if ($request->paid_amount > 0) {
            $this->validate($request, [
                'bank_id' => 'required|integer',
            ]);
        }
        try {
            DB::beginTransaction();

            $code = CodeService::generate(Sale::class, '', 'invoice_no');

            $storeData = [
                'invoice_no' => $code,
                'customer_id' => $request->customer_id,
                'date' => dbDateFormat($request->date),
                'total_quantity' => $request->total_quantity ?? 0,
                'subtotal_amount' => $request->subtotal_amount ?? 0,
                'vat_percent' => env('VAT_PERCENT') ?? 0,
                'vat_amount' => $request->vat_amount ?? 0,
                'flat_discount_percent' => $request->flat_discount_percentage ?? 0,
                'flat_discount_amount' => $request->flat_discount_amount ?? 0,
                'total_amount' => $request->total_amount ?? 0,
            ];

            $data = Sale::create($storeData);

            if ($request->only('sale_item_id')) {
                $itemData = [];
                foreach ($request->sale_item_id as $key => $row) {
                    $discountAmount = ($request->discount_amount[$key] / $request->quantity[$key]);
                    $price = (($request->unit_price[$key] - $discountAmount) * $request->quantity[$key]);
                    $flatDiscountAmt = ($request->flat_discount_percentage * $price) / 100;
                    $netPrice = ($price - $flatDiscountAmt);
                    $netUnitPrice = ($netPrice / $request->quantity[$key]);
                    $unit = Unit::find($request->unit_id[$key]);
                    $itemData[] = [
                        'sale_id' => $data->id,
                        'category_id' => $request->category_id[$key],
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key],
                        'unit_quantity' => $unit->quantity,
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'discount_percentage' => $request->discount_percentage[$key] ?? 0,
                        'discount_amount' => $discountAmount ?? 0,
                        'flat_discount_amount' => $flatDiscountAmt ?? 0,
                        'net_unit_price' => $netUnitPrice ?? 0,
                        'net_price' => $netPrice ?? 0,
                        'amount' => $price ?? 0,
                        'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                        'created_at' => now(),
                    ];
                }

                SaleItem::insert($itemData);
            }

            if ($request->paid_amount > 0) {
                $paidData = [
                    'customer_id' => $data->customer_id,
                    'type' => 'Received',
                    'date' => dbDateFormat($data->date),
                    'note' => null,
                    'amount' => $request->paid_amount,
                    'sale_id' => $data->id,
                ];
                $payment = CustomerPayment::create($paidData);

                if ($payment) {
                    Transaction::create([
                        'type' => 'Received',
                        'flag' => 'Customer Payment',
                        'flagable_id' => $payment->id,
                        'flagable_type' => 'App\Models\CustomerPayment',
                        'bank_id' => $request->bank_id,
                        'datetime' => now(),
                        'note' => $payment->note,
                        'amount' => $payment->amount,
                    ]);
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 200);
        }

        if ($data) {
            session()->flash('successMessage', 'Sale was successfully added.');
        } else {
            session()->flash('errorMessage', 'Sale saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }
    public function ajaxStore(Request $request)
    {
        $this->authorize('create customer');

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:customers,mobile',
            'address' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'shop_name' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        Customer::create($validatedData);

        return response()->json(['success' => true, 'successMessage' => 'Customer was successfully added!']);
    }

    public function show($id)
    {

        $data = Sale::select('sales.*', 'transactions.bank_id', 'customer_payments.amount AS paid_amount')->with('items')
            ->leftJoin('customer_payments', function ($q) {
                $q->on('customer_payments.sale_id', '=', 'sales.id');
            })
            ->leftJoin('transactions', function ($q) {
                $q->on('transactions.flagable_id', '=', 'customer_payments.id');
                $q->where('transactions.flag', 'Customer Payment');
            })
            ->find($id);

        return view('admin.sale.show', compact('data'));
    }

    public function edit($id)
    {
        $data = Sale::select('sales.*', 'transactions.bank_id', 'customer_payments.amount AS paid_amount')->with('items')
            ->leftJoin('customer_payments', function ($q) {
                $q->on('customer_payments.sale_id', '=', 'sales.id');
            })
            ->leftJoin('transactions', function ($q) {
                $q->on('transactions.flagable_id', '=', 'customer_payments.id');
                $q->where('transactions.flag', 'Customer Payment');
            })
            ->find($id);
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
                    'discount_percentage' => null,
                    'discount_amount' => null,
                    'amount' => null,
                ]
            ];
        }

        $customers = Customer::where('status', 'Active')->get();
        $categories = Category::with('products', 'children.products')->where('status', 'Active')->get();
        $products = Product::where('status', 'Active')->get();
        $units = Unit::where('status', 'Active')->get();
        $banks = Bank::where('status', 'Active')->get();

        return view('admin.sale.edit', compact('data', 'items', 'customers', 'categories', 'products', 'units', 'banks'))->with('edit', $id);
    }

    public function update(SaleRequest $request, $id)
    {
        if ($request->paid_amount > 0) {
            $this->validate($request, [
                'bank_id' => 'required|integer',
            ]);
        }

        $data = Sale::find($id);

        $storeData = [
            'customer_id' => $request->customer_id,
            'transport_id' => $request->transport_id,
            'date' => dbDateFormat($request->date),
            'total_quantity' => $request->total_quantity ?? 0,
            'subtotal_amount' => $request->subtotal_amount ?? 0,
            'vat_percent' => env('VAT_PERCENT') ?? 0,
            'vat_amount' => $request->vat_amount ?? 0,
            'flat_discount_percent' => $request->flat_discount_percentage ?? 0,
            'flat_discount_amount' => $request->flat_discount_amount ?? 0,
            'total_amount' => $request->total_amount ?? 0,
        ];

        $updated =  $data->update($storeData);

        if ($request->only('sale_item_id')) {
            SaleItem::where('sale_id', $data->id)->whereNotIn('id', $request->sale_item_id)->delete();
            foreach ($request->sale_item_id as $key => $row) {
                $discountAmount = ($request->discount_amount[$key] / $request->quantity[$key]);
                $price = (($request->unit_price[$key] - $discountAmount) * $request->quantity[$key]);
                $flatDiscountAmt = ($request->flat_discount_percentage * $price) / 100;
                $netPrice = ($price - $flatDiscountAmt);
                $netUnitPrice = ($netPrice / $request->quantity[$key]);
                $unit = Unit::find($request->unit_id[$key]);
                $updateData = [
                    'sale_id' => $data->id,
                    'category_id' => $request->category_id[$key],
                    'product_id' => $request->product_id[$key],
                    'unit_id' => $request->unit_id[$key],
                    'unit_quantity' => $unit->quantity,
                    'quantity' => $request->quantity[$key],
                    'unit_price' => $request->unit_price[$key] ?? 0,
                    'discount_percentage' => $request->discount_percentage[$key] ?? 0,
                    'discount_amount' => $discountAmount ?? 0,
                    'flat_discount_amount' => $flatDiscountAmt ?? 0,
                    'net_unit_price' => $netUnitPrice ?? 0,
                    'net_price' => $netPrice ?? 0,
                    'amount' => $price ?? 0,
                    'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                ];

                if ($row > 0) {
                    SaleItem::where('id', $row)->update($updateData);
                } else {
                    SaleItem::create($updateData);
                }
            }
        }

        if ($request->paid_amount > 0) {
            $paidData = [
                'customer_id' => $data->customer_id,
                'type' => 'Received',
                'date' => dbDateFormat($data->date),
                'note' => null,
                'amount' => $request->paid_amount,
                'sale_id' => $data->id,
            ];
            $payment = CustomerPayment::updateOrCreate(['sale_id' => $data->id], $paidData);
            if ($payment) {
                Transaction::updateOrCreate([
                    'flagable_id' => $payment->id,
                    'flagable_type' => 'App\Models\CustomerPayment',
                ], [
                    'type' => 'Received',
                    'flag' => 'Customer Payment',
                    'bank_id' =>  $request->bank_id,
                    'datetime' => now(),
                    'note' => $payment->note,
                    'amount' => $payment->amount,
                ]);
            }
        } else {
            $payment = CustomerPayment::where('sale_id', $data->id)->first();
            if (!empty($payment)) {
                Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\CustomerPayment')->delete();
                $payment->delete();
            }
        }

        if ($updated) {
            session()->flash('successMessage', 'Sale was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Sale update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        $data = Sale::find($id);

        $payment = CustomerPayment::where('sale_id', $id)->first();

        if (!empty($payment)) {
            Transaction::where('flagable_id', $payment->id)->where('flagable_type', 'App\Models\CustomerPayment')->delete();
            $payment->delete();
        }

        SaleItem::where('sale_id', $id)->delete();

        $data->delete();

        session()->flash('successMessage', 'Sale was successfully deleted!');

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function customerLastDiscount(Request $request)
    {
        $sales = Sale::select('sale_items.discount_percentage', 'sale_items.discount_amount')
            ->join('sale_items', 'sales.id', '=', 'sale_items.sale_id')
            ->where('sales.customer_id', $request->customer_id)
            ->where('sale_items.product_id', $request->product_id)
            ->orderBy('sales.id', 'desc')
            ->first();
        if ($sales) {
            return response()->json(['success' => true, 'data' => $sales]);
        } else {
            return response()->json(['success' => false, 'data' => 'No data found!']);
        }
    }

    public function getProductsByCategory($category)
    {

        $product = Product::with(['baseUnit.units'])->select('products.*', DB::raw("((IFNULL(A.inQty, 0) + IFNULL(D.inQty, 0)) - (IFNULL(B.outQty, 0) + IFNULL(C.outQty, 0))) AS stockQty"))
            ->join(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM stock_items GROUP BY product_id) AS A"), function ($q) {
                $q->on('A.product_id', '=', 'products.id');
            })
            ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS outQty FROM stock_return_items GROUP BY product_id) AS B"), function ($q) {
                $q->on('B.product_id', '=', 'products.id');
            })
            ->leftJoin(DB::raw("(SELECT product_id, category_id, SUM(actual_quantity) AS outQty FROM sale_items GROUP BY product_id,category_id) AS C"), function ($q) {
                $q->on('C.product_id', '=', 'products.id');
            })
            ->leftJoin(DB::raw("(SELECT product_id, SUM(actual_quantity) AS inQty FROM sale_return_items GROUP BY product_id) AS D"), function ($q) {
                $q->on('D.product_id', '=', 'products.id');
            })
            ->where('products.category_id', $category)
            ->where('status', 'Active')
            ->having('stockQty', '>', 0)
            ->get();

        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        return response()->json([
            'products' => $product,
        ]);
    }
}
