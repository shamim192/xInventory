<?php

namespace App\Http\Controllers\Admin\Stock;

use App\Models\Unit;
use App\Models\Stock;
use App\Models\Supplier;
use App\Models\StockItem;
use App\Models\StockReturn;
use Illuminate\Http\Request;
use App\Models\StockReturnItem;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class StockReturnController extends Controller
{

    public function index(Request $request)
    {
        $sql = StockReturn::with(['supplier', 'stock', 'items'])->orderBy('id', 'DESC');

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

        return view('admin.stock-return.index', compact('serial', 'records', 'suppliers'));
    }

    public function create()
    {
        $returnItems = [
            (object)[
                'id' => null,
                'stock_item_id' => null,
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
                'stock_quantity' => null,
                'returned_quantity' => null,
                'remain_quantity' => null,
                'quantity' => null,
            ]
        ];

        $suppliers = Supplier::where('status', 'Active')->get();

        return view('admin.stock-return.create', compact('returnItems', 'suppliers'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'stock_id' => 'required|integer',
            'date' => 'required|date',
            'stock_item_id' => 'required|array|min:1',
            'category_id' => 'required|array|min:1',
            'product_id' => 'required|array|min:1',
            'unit_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'unit_price' => 'required|array|min:1',
        ]);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'stock_id' => $request->stock_id,
            'date' => dbDateFormat($request->date),
        ];

        $data = StockReturn::create($storeData);

        if ($request->only('stock_item_id')) {
            $return = [];
            foreach ($request->stock_item_id as $key => $row) {
                if ($request->quantity[$key] > 0) {
                    $unit = Unit::find($request->unit_id[$key]);

                    $return[] = [
                        'stock_return_id' => $data->id,
                        'stock_id' => $data->stock_id,
                        'stock_item_id' => $request->stock_item_id[$key],
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

            StockReturnItem::insert($return);
        }

        if ($data) {
            session()->flash('successMessage', 'Stock Retrun was successfully added.');
        } else {
            session()->flash('errorMessage', 'Stock Return saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = StockReturn::with(['supplier', 'stock', 'items'])->find($id);
        return view('admin.stock-return.show', compact('data'));
    }


    public function edit(Request $request, $id)
    {
        $data = StockReturn::find($id);

        $returnItems = StockItem::with(['product', 'unit', 'category'])
            ->select('stock_items.id AS stock_item_id', 'stock_items.category_id', 'stock_items.product_id', 'stock_items.unit_id', 'stock_items.unit_price', 'stock_items.quantity AS stock_quantity', 'A.returned_quantity', DB::raw('(stock_items.quantity -IFNULL(A.returned_quantity, 0)) AS remain_quantity'), 'B.id', 'B.quantity AS quantity')
            ->leftJoin(DB::raw("(SELECT stock_item_id, SUM(quantity) AS returned_quantity FROM stock_return_items WHERE stock_return_id!=" . $id . " GROUP BY stock_item_id) AS A"), 'stock_items.id', '=', 'A.stock_item_id')
            ->where('stock_items.stock_id', $data->stock_id)
            ->leftJoin('stock_return_items AS B', function ($q) use ($id) {
                $q->on('stock_items.id', '=', 'B.stock_item_id');
                $q->where('B.stock_return_id', '=', $id);
            })
            ->get();

        $suppliers = Supplier::where('status', 'Active')->get();
        $stocks = Stock::select('id', 'date', 'challan_number')->where('supplier_id', $data->supplier_id)->get();

        return view('admin.stock-return.edit', compact('data', 'returnItems', 'suppliers', 'stocks'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'stock_id' => 'required|integer',
            'date' => 'required|date',
            'stock_item_id' => 'required|array|min:1',
            'category_id' => 'required|array|min:1',
            'product_id' => 'required|array|min:1',
            'unit_id' => 'required|array|min:1',
            'quantity' => 'required|array|min:1',
            'unit_price' => 'required|array|min:1',
        ]);

        $data = StockReturn::find($id);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'stock_id' => $request->stock_id,
            'date' => dbDateFormat($request->date),
        ];

       $updated = $data->update($storeData);
       
       if ($request->only('stock_item_id')) {
        StockReturnItem::where('stock_id', $data->id)->whereNotIn('id', $request->stock_return_item_id)->delete();
        foreach ($request->stock_return_item_id as $key => $row) {
            if ($request->quantity[$key] > 0) {
                $unit = Unit::find($request->unit_id[$key]);

                $updateData = [
                    'stock_return_id' => $data->id,
                    'stock_id' => $data->stock_id,
                    'stock_item_id' => $request->stock_item_id[$key],
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
                    StockReturnItem::where('id', $row)->update($updateData);
                } else {
                    StockReturnItem::create($updateData);
                }
            }
        }
    }        
        if ($updated) {
            session()->flash('successMessage', 'Stock Retrun was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Stock Return updating failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function destroy($id)
    {
        $data = StockReturn::find($id);

        StockReturnItem::where('stock_id', $id)->delete();
        $data->delete();

        session()->flash('successMessage', 'Stock Return was successfully deleted!');

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function supplierWiseStock(Request $request)
    {
        if ($request->supplier_id > 0) {
            $stocks = Stock::select('id', 'date', 'challan_number')->where('supplier_id', $request->supplier_id)->get();
            return response()->json(['status' => true, 'stocks' => $stocks]);
        }
        return response()->json(['status' => false, 'message' => 'Stock In not found!']);
    }

    public function stockItem(Request $request)
    {
        if ($request->stock_id > 0) {
            $items = StockItem::with('product', 'unit', 'category')->select('stock_items.id AS stock_item_id', 'stock_items.category_id', 'stock_items.product_id', 'stock_items.unit_id', 'stock_items.unit_price', 'stock_items.quantity AS stock_quantity', 'A.returned_quantity', DB::raw('(stock_items.quantity -IFNULL(A.returned_quantity, 0)) AS remain_quantity'), DB::raw('null AS id'), DB::raw('null AS quantity'))
                ->leftJoin(DB::raw("(SELECT stock_item_id, SUM(quantity) AS returned_quantity FROM stock_return_items GROUP BY stock_item_id) AS A"), 'stock_items.id', '=', 'A.stock_item_id')
                ->where('stock_items.stock_id', $request->stock_id)
                ->get();
            return response()->json(['status' => true, 'items' => $items]);
        }
        return response()->json(['status' => false, 'message' => 'ID not found!']);
    }
}
