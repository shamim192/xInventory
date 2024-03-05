<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Customer;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Models\CustomerPayment;
use App\Http\Controllers\Controller;

class CustomerPaymentController extends Controller
{

    public function index(Request $request)
    {
        $sql = CustomerPayment::with('customer');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('serial_number', 'LIKE', $request->q . '%')
                    ->orWhere('note', 'LIKE', $request->q . '%');
            });
        }

        if ($request->from) {
            $sql->where('date', '>=', $request->from);
        }

        if ($request->to) {
            $sql->where('date', '<=', $request->to);
        }

        $records = $sql->orderBy('id', 'DESC')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $customers = Customer::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.customer-payment.index', compact('serial', 'records', 'banks', 'customers'));
    }

    public function create()
    {
        $data = new CustomerPayment();

        $data->setRelations([
            'transactions' => collect([
                new Transaction()
            ])
        ]);

        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $customers = Customer::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.customer-payment.create', compact('banks', 'customers', 'data'));
    }

    public function adjustment()
    {
        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $customers = Customer::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();
        $adjustment = true;
        

        return view('admin.payment.customer-payment.create', compact('banks', 'customers', 'adjustment'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'type' => 'required|in:Received,Payment,Adjustment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);
        
        $storeData = [
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->total_amount,
            'note' => $request->note,
        ];

        $data = CustomerPayment::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            foreach ($request->input("bank_id") as $key => $tranId) {
                $transactionData = [
                    'type' => $data->type,
                    'flag' => 'Customer Payment',
                    'flagable_id' => $data->id,
                    'flagable_type' => CustomerPayment::class,
                    'bank_id' => $request->input("bank_id.$key"),
                    'datetime' => $data->date,
                    'note' => $data->note,
                    'amount' => $request->input("amount.$key"),
                ];
                Transaction::create($transactionData);
            }
        }

        if ($data) {
            session()->flash('successMessage', 'Customer Payment was successfully added.');
        } else {
            session()->flash('errorMessage', 'Customer Payment saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show($id)
    {
        $data = CustomerPayment::with('customer', 'transactions.bank')->findOrFail($id);
        return view('admin.payment.customer-payment.show', compact('data'));
    }

    public function edit($id)
    {
        $data = CustomerPayment::with('transactions')->findOrFail($id);
        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $customers = Customer::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.customer-payment.edit', compact('data', 'banks', 'customers'))->with('edit', 1);
    }


    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'customer_id' => 'required|integer',
            'type' => 'required|in:Received,Payment,Adjustment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = CustomerPayment::findOrFail($id);

        $storeData = [
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->total_amount,
            'note' => $request->note,
        ];

        $data->update($storeData);
        
        Transaction::where('flagable_id', $data->id)->where('flagable_type', CustomerPayment::class)->delete();

        if ($data && $data->type != 'Adjustment') {
            $itemIdArr = [];
            foreach ($request->input('bank_id') as $key => $bankId) {
                $item = [
                        'type' => $data->type,
                        'flag' => 'Customer Payment',
                        'flagable_id' => $data->id,
                        'flagable_type' => CustomerPayment::class,
                        'bank_id' => $bankId,
                        'datetime' => $data->date,
                        'note' => $data->note,
                        'amount' => $request->input("amount.$key"),
                    ];
                    $itemIdArr[] = $data->transactions()->updateOrCreate(['id' => $request->input("id.$key")], $item)->id;
                }
                $data->transactions()->whereNotIn('id', $itemIdArr)->delete();
        }

        session()->flash('successMessage', 'Customer Payment was successfully updated!');
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = CustomerPayment::findOrFail($id);
            $data->transactions()->delete();
            $data->delete();

            session()->flash('successMessage', 'Customer Payment was successfully deleted.');
        } catch ( \Exception $e) {
            session()->flash('errorMessage', 'Customer Payment deleting failed! Reason: ' . $e->getMessage());
        }
        return redirect()->action([self::class, 'index'], qArray());
    }
}
