<?php

namespace App\Services;

use App\Models\Supplier;
use App\Models\Transaction;
use App\Services\CodeService;
use App\Models\SupplierPayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

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

    public static function adjustment($data, $amount, $actionableType, $actionableId = null)
    {
        if ($actionableId) {
            //Delete old adjustment...
            SupplierPayment::where('type', 'Adjustment')
                ->where('actionable_type', $actionableType)
                ->where('actionable_id', $actionableId)
                ->delete();
        }

        $code = CodeService::generate(SupplierPayment::class, '', 'serial_number');
        $payData = [
            'supplier_id' => $data->supplier_id,
            'branch_id' => $data->branch_id ?? null,
            'actionable_type' => $actionableType,
            'actionable_id' => $data->id,
            'type' => 'Adjustment',
            'serial_number' => $code,
            'date' => $data->date,
            'amount' => $amount,
            'note' => $data->note,
            'approved_at' => now(),
            'approved_by' => Auth::user()->id,
            'created_by' => Auth::user()->id,
        ];
        SupplierPayment::create($payData);
    }

    public static function payment($data, $selectedBanks, $actionableType, $actionableId = null)
    {
        self::paymentOrReceived('Payment', $data, $selectedBanks, $actionableType, $actionableId);
    }

    public static function receive($data, $selectedBanks, $actionableType, $actionableId = null)
    {
        self::paymentOrReceived('Received', $data, $selectedBanks, $actionableType, $actionableId);
    }

    private static function paymentOrReceived($type, $data, $selectedBanks, $actionableType, $actionableId)
    {
        if ($actionableId) {
            //Delete old payment & transactions...
            $payment = SupplierPayment::where('type', $type)
                ->where('actionable_type', $actionableType)
                ->where('actionable_id', $actionableId)
                ->first();
            if ($payment) {
                $payment->delete();
                Transaction::where('paymentable_type', SupplierPayment::class)->where('paymentable_id', $payment->id)->delete();
            }
        }

        $totalPaidAmount = 0;
        foreach ($selectedBanks as $pay) {
            if ($pay['amount'] > 0 && $pay['bank_id'] > 0) {
                $totalPaidAmount += $pay['amount'];
            }
        }

        if ($totalPaidAmount > 0) {
            $code = CodeService::generate(SupplierPayment::class, '', 'serial_number');
            $payData = [
                'supplier_id' => $data->supplier_id,
                'branch_id' => $data->branch_id ?? null,
                'actionable_type' => $actionableType,
                'actionable_id' => $data->id,
                'type' => $type,
                'serial_number' => $code,
                'date' => $data->date,
                'amount' => $totalPaidAmount,
                'note' => $data->note,
                'approved_at' => now(),
                'approved_by' => Auth::user()->id,
                'created_by' => Auth::user()->id,
            ];
            $payment = SupplierPayment::create($payData);
            if ($payment) {
                foreach ($selectedBanks as $pay) {
                    if ($pay['amount'] > 0 && $pay['bank_id'] > 0) {
                        $transactionData[] = [
                            'branch_id' => $data->branch_id ?? null,
                            'type' => $type,
                            'paymentable_type' => SupplierPayment::class,
                            'paymentable_id' => $payment->id,
                            'bank_id' => $pay['bank_id'],
                            'datetime' => $data->date,
                            'note' => $data->note,
                            'amount' => $pay['amount'],
                            'created_by' => Auth::user()->id,
                            'created_at' => now(),
                        ];
                    }
                }

                if (isset($transactionData)) {
                    Transaction::insert($transactionData);
                }
            }
        }
    }

    public static function delete($actionableType, $actionableId)
    {
        $payments = SupplierPayment::where('actionable_type', $actionableType)
            ->where('actionable_id', $actionableId)
            ->get();

        foreach ($payments as $payment) {
            $payment->delete();
            if ($payment->type != 'Adjustment') {
                Transaction::where('paymentable_type', SupplierPayment::class)->where('paymentable_id', $payment->id)->delete();
            }
        }
    }
}
