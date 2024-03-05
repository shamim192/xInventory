<?php

namespace App\Services;

use App\Models\Customer;
use Illuminate\Support\Facades\DB;

class CustomerService
{
    public static function due($customerId)
    {        
        $customer = Customer::select(
            DB::raw("((IFNULL(A.amount, 0) + IFNULL(D.outAmount, 0)) - (IFNULL(C.inAmount, 0) + IFNULL(B.amount, 0))) AS due")
        )
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(total_amount) AS amount FROM sales GROUP BY customer_id) AS A"), function($q) {
            $q->on('customers.id', '=', 'A.customer_id');
        })

        ->leftJoin(DB::raw("(SELECT X.customer_id, SUM(Y.amount) AS amount FROM `sale_returns` AS X INNER JOIN sale_return_items AS Y ON X.id = Y.sale_return_id GROUP BY customer_id) AS B"), function ($q) {
            $q->on('B.customer_id', '=', 'customers.id');
        })

        ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS inAmount FROM `customer_payments` WHERE type='Received' GROUP BY customer_id) AS C"), function ($q) {
            $q->on('C.customer_id', '=', 'customers.id');
        })
        ->leftJoin(DB::raw("(SELECT customer_id, SUM(amount) AS outAmount FROM `customer_payments` WHERE type !='Received' GROUP BY customer_id) AS D"), function ($q) {
            $q->on('D.customer_id', '=', 'customers.id');
        })

        ->where('id', $customerId)
        ->first();

        if ($customer && $customer->due) {
            return $customer->due;
        }
        return 0;
        
    }
  
}