<?php

namespace App\Http\Controllers\Admin\Sale;

use App\Models\Sale;
use App\Models\Unit;
use App\Models\Customer;
use App\Models\SaleItem;
use App\Models\SaleReturn;
use Illuminate\Http\Request;
use App\Models\SaleReturnItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PrintController;

class SaleReturnController extends Controller {

    public function index(Request $request)
    {
        $sql = SaleReturn::with(['customer', 'sale', 'items'])->orderBy('date', 'DESC');

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

        return view('admin.sale-return.index', compact('records','serial','customers'));
    }

    public function create()
    {
        $returnItems = [
            (object)[
                'id' => null,
                'sale_item_id' => null,
                'category_id' => null,
                'product_id' => null,
                'unit_id' => null,
                'category' => (object)[
                    'name' => null,
                ],
                'product' => (object)[
                    'name' => null,
                    'code' => null,
                ],
                'unit' => (object)[
                    'name' => null,
                ],
                'unit_price' => null,
                'sale_quantity' => null,
                'returned_quantity' => null,
                'remain_quantity' => null,
                'quantity' => null,
            ]
        ];
        
        $customers = Customer::where('status', 'Active')->get();

        return view('admin.sale-return.create', compact('returnItems', 'customers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'sale_id' => 'required|integer',
            'date' => 'required|date',
            'sale_item_id' => 'required|array|min:1',
            'category_id' => 'required|array|min:1',
            'product_id' => 'required|array|min:1',
            'unit_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'unit_price' => 'required|array|min:1',
        ]);

        $storeData = [
            'customer_id' => $request->customer_id,
            'sale_id' => $request->sale_id,
            'date' => dbDateFormat($request->date),
        ];

        $data = SaleReturn::create($storeData);

        if ($request->only('sale_item_id')) {
            $itemData = [];
            foreach ($request->sale_item_id as $key => $row) {
                if ($request->quantity[$key] > 0) {
                    $unit = Unit::find($request->unit_id[$key]);

                    $itemData[] = [
                        'sale_return_id' => $data->id,
                        'sale_id' => $data->sale_id,
                        'sale_item_id' => $request->sale_item_id[$key],
                        'category_id' => $request->category_id[$key],
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key],
                        'unit_quantity' => $unit->quantity,
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'amount' => ($request->quantity[$key] * $request->unit_price[$key]),
                        'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                        'created_at' => now(),
                    ];
                }
            }

            SaleReturnItem::insert($itemData);
        }

        if ($data) {
            session()->flash('successMessage', 'Sale Retrun was successfully added.');
        } else {
            session()->flash('errorMessage', 'Sale Return saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {        
        $data = SaleReturn::with(['customer', 'sale', 'items'])->find($id);
        return view('admin.sale-return.show', compact('data'))->with('show', $id);
    }


    public function edit(Request $request, $id)
    {
        $data = SaleReturn::find($id);

        $returnItems = SaleItem::with(['product', 'unit','category'])
        ->select('sale_items.id AS sale_item_id','sale_items.category_id', 'sale_items.product_id', 'sale_items.unit_id', 'sale_items.unit_price', 'sale_items.quantity AS sale_quantity', 'A.returned_quantity', DB::raw('(sale_items.quantity -IFNULL(A.returned_quantity, 0)) AS remain_quantity'), 'B.id', 'B.quantity AS quantity')
        ->leftJoin(DB::raw("(SELECT sale_item_id, SUM(quantity) AS returned_quantity FROM sale_return_items WHERE sale_return_id!=".$id." GROUP BY sale_item_id) AS A"), 'sale_items.id', '=', 'A.sale_item_id')
        ->where('sale_items.sale_id', $data->sale_id)
        ->leftJoin('sale_return_items AS B', function($q) use($id) {
            $q->on('sale_items.id', '=', 'B.sale_item_id');
            $q->where('B.sale_return_id', '=', $id);            
        })
        ->get();
        
        $customers = Customer::where('status', 'Active')->get();
        $sales = Sale::select('id', 'date')->where('customer_id', $data->customer_id)->get();

        return view('admin.sale-return.edit', compact('data', 'returnItems', 'customers', 'sales'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'sale_id' => 'required|integer',
            'date' => 'required|date',
            'sale_item_id' => 'required|array|min:1',
            'category_id' => 'required|array|min:1',
            'product_id' => 'required|array|min:1',
            'unit_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'unit_price' => 'required|array|min:1',
        ]);

        $data = SaleReturn::find($id);

        $storeData = [
            'customer_id' => $request->customer_id,
            'sale_id' => $request->sale_id,
            'date' => dbDateFormat($request->date),
        ];

       $updated = $data->update($storeData);

        if ($request->only('sale_item_id')) {
            SaleReturnItem::where('sale_id', $data->id)->whereNotIn('id', $request->sale_return_item_id)->delete();
            foreach ($request->sale_return_item_id as $key => $row) {
                if ($request->quantity[$key] > 0) {
                    $unit = Unit::find($request->unit_id[$key]);

                    $updateData = [
                        'sale_return_id' => $data->id,
                        'sale_id' => $data->sale_id,
                        'sale_item_id' => $request->sale_item_id[$key],
                        'category_id' => $request->category_id[$key],
                        'product_id' => $request->product_id[$key],
                        'unit_id' => $request->unit_id[$key],
                        'unit_quantity' => $unit->quantity,
                        'quantity' => $request->quantity[$key],
                        'unit_price' => $request->unit_price[$key],
                        'amount' => ($request->quantity[$key] * $request->unit_price[$key]),
                        'actual_quantity' => ($request->quantity[$key] * $unit->quantity),
                    ];

                    if ($row > 0) {
                        SaleReturnItem::where('id', $row)->update($updateData);
                    } else {
                        SaleReturnItem::create($updateData);
                    }
                }
            }
        }

        if ($updated) {
            session()->flash('successMessage', 'Sale Retrun was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Sale Return updating failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function destroy($id)
    {
        $data = SaleReturn::find($id);

        SaleReturnItem::where('sale_id', $id)->delete();
        $data->delete();
        
        session()->flash('successMessage', 'Sale Return was successfully deleted!');

        return redirect()->action([self::class, 'index'], qArray());
    }
    
    public function customerWiseSale(Request $request)
    {
        if ($request->customer_id > 0) {
            $sales = Sale::select('id', 'date')->where('customer_id', $request->customer_id)->get();
            return response()->json(['status' => true, 'sales' => $sales]);
        }
        return response()->json(['status' => false, 'message' => 'Sale In not found!']);
    }

    public function saleItem(Request $request)
    {
        if ($request->sale_id > 0) {           
            $items = SaleItem::with('product', 'unit','category')->select('sale_items.id AS sale_item_id', 'sale_items.product_id', 'sale_items.category_id', 'sale_items.unit_id', 'sale_items.unit_price', 'sale_items.quantity AS sale_quantity', 'A.returned_quantity', DB::raw('(sale_items.quantity -IFNULL(A.returned_quantity, 0)) AS remain_quantity'), DB::raw('null AS id'), DB::raw('null AS quantity'))
            ->leftJoin(DB::raw("(SELECT sale_item_id, SUM(quantity) AS returned_quantity FROM sale_return_items GROUP BY sale_item_id) AS A"), 'sale_items.id', '=', 'A.sale_item_id')
            ->where('sale_items.sale_id', $request->sale_id)
            ->get();

            return response()->json(['status' => true, 'items' => $items]);
        }
        return response()->json(['status' => false, 'message' => 'ID not found!']);
    }
}
