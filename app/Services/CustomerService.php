<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Transaction;
use App\Services\CodeService;
use App\Models\CustomerPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CustomerService
{
    public static function due($customerId, $saleId = null)
    {
        $saleCondition = [
            'id' => null,
            'sale_id' => null,
        ];
        if ($saleId) {
            $saleCondition = [
                'id' => ' WHERE (id != ' . $saleId . ' OR id IS NULL)',
                'sale_id' => ' AND (sale_id != ' . $saleId . ' OR sale_id IS NULL)',
            ];
        }
        
        $customer = Customer::select(
            DB::raw("((IFNULL(A.amount, 0) + IFNULL(B.amount, 0)) - (IFNULL(C.amount, 0) + IFNULL(D.amount, 0))) AS due")
        )
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS amount FROM sales  ". $saleCondition['id'] ." GROUP BY customer_id) AS A"), function($q) {
            $q->on('customers.id', '=', 'A.customer_id');
        })

        ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS amount FROM customer_payments WHERE type = 'Payment' GROUP BY customer_id) AS B"), function($q) {
            $q->on('customers.id', '=', 'B.customer_id');
        })

        ->leftJoin(DB::raw("(SELECT X.customer_id, SUM(Y.amount) AS amount FROM `sale_returns` AS X INNER JOIN sale_return_items AS Y ON X.id = Y.sale_return_id GROUP BY customer_id) AS C"), function ($q) {
            $q->on('C.customer_id', '=', 'customers.id');
        })

        ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS amount FROM customer_payments WHERE type != 'Payment' ". $saleCondition['sale_id'] ." GROUP BY customer_id) AS D"), function($q) {
            $q->on('customers.id', '=', 'D.customer_id');
        })

        ->where('id', $customerId)
        ->first();
        if ($customer && $customer->due) {
            return $customer->due;
        }
        return 0;
        
    }
  
}