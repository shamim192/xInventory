<?php

namespace App\Services;

use App\Models\Supplier;
use Illuminate\Support\Facades\DB;

class SupplierService
{
    public static function due($id)
    {
        $supplier = Supplier::select(DB::raw('((IFNULL(A.stockAmount, 0) - IFNULL(B.returnAmount, 0)) - (IFNULL(D.outAmount, 0) - IFNULL(C.inAmount, 0))) AS due')
        )
            ->leftJoin(DB::raw("(SELECT supplier_id, SUM(total_amount) AS stockAmount FROM `stocks` GROUP BY supplier_id) AS A"), function ($q) {
                $q->on('A.supplier_id', '=', 'suppliers.id');
            })
            ->leftJoin(DB::raw("(SELECT X.supplier_id, SUM(Y.amount) AS returnAmount FROM `stock_returns` AS X INNER JOIN stock_return_items AS Y ON X.id = Y.stock_id GROUP BY supplier_id) AS B"), function ($q) {
                $q->on('B.supplier_id', '=', 'suppliers.id');
            })
            ->leftJoin(DB::raw("(SELECT supplier_id, SUM(amount) AS inAmount FROM `supplier_payments` WHERE type='Received' GROUP BY supplier_id) AS C"), function ($q) {
                $q->on('C.supplier_id', '=', 'suppliers.id');
            })
            ->leftJoin(DB::raw("(SELECT supplier_id, SUM(amount) AS outAmount FROM `supplier_payments` WHERE type !='Received' GROUP BY supplier_id) AS D"), function ($q) {
                $q->on('D.supplier_id', '=', 'suppliers.id');
            })

            ->where('suppliers.id', $id)
            ->first();

        if ($supplier && $supplier->due) {
            return $supplier->due;
        }
        return 0;
    }
}
