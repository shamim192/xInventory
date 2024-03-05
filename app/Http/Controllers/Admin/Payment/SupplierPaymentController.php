<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Models\SupplierPayment;

class SupplierPaymentController extends Controller
{

    public function index(Request $request)
    {
        $sql = SupplierPayment::with('supplier');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('serial_number', 'LIKE', $request->q . '%')
                    ->orWhere('note', 'LIKE', $request->q . '%');
            });
        }

        if ($request->bank) {
            $sql->whereHas('transactions', function ($q) use ($request) {
                $q->where('bank_id', $request->bank);
            });
        }

        if ($request->supplier) {
            $sql->where('supplier_id', $request->supplier);
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
        $suppliers = Supplier::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.supplier-payment.index', compact('serial', 'records', 'banks', 'suppliers'));
    }

    public function create()
    {
        $data = new SupplierPayment();

        $data->setRelations([
            'transactions' => collect([
                new Transaction()
            ])
        ]);

        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $suppliers = Supplier::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.supplier-payment.create', compact('banks', 'suppliers', 'data'));
    }

    public function adjustment()
    {
        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $suppliers = Supplier::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();
        $adjustment = true;        

        return view('admin.payment.supplier-payment.create', compact('banks', 'suppliers', 'adjustment'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'type' => 'required|in:Received,Payment,Adjustment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->total_amount,
            'note' => $request->note,
        ];

        $data = SupplierPayment::create($storeData);

        if ($data && $data->type != 'Adjustment') {
            foreach ($request->input("bank_id") as $key => $bankId) {
                $transactionData = [
                    'type' => $data->type == 'Payment' ? 'Received' : 'Payment',
                    'flag' => 'Supplier Payment',
                    'flagable_id' => $data->id,
                    'flagable_type' => SupplierPayment::class,
                    'bank_id' => $bankId,
                    'datetime' => $data->date,
                    'note' => $data->note,
                    'amount' => $request->input("amount.$key"),
                ];
                Transaction::create($transactionData);
            }
        }

        session()->flash('successMessage', 'Supplier Payment was successfully added!');
        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = SupplierPayment::with('supplier', 'transactions.bank')->findOrFail($id);
        return view('admin.payment.supplier-payment.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = SupplierPayment::with('transactions')->findOrFail($id);
        $banks = Bank::select(['id', 'name'])->where('status', 'Active')->get();
        $suppliers = Supplier::select(['id', 'name', 'mobile', 'address'])->where('status', 'Active')->get();

        return view('admin.payment.supplier-payment.edit', compact('data', 'banks', 'suppliers'))->with('edit', $id);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'supplier_id' => 'required|integer',
            'type' => 'required|in:Received,Payment,Adjustment',
            'date' => 'required|date',
            'total_amount' => 'required|numeric',
            'note' => 'nullable|string',
        ]);

        $data = SupplierPayment::findOrFail($id);

        $storeData = [
            'supplier_id' => $request->supplier_id,
            'type' => $request->type,
            'date' => $request->date,
            'amount' => $request->total_amount,
            'note' => $request->note,
        ];

        $data->update($storeData);
        
        Transaction::where('flagable_id', $data->id)->where('flagable_type', SupplierPayment::class)->delete();

        if ($data && $data->type != 'Adjustment') {
            $itemIdArr = [];
            foreach ($request->input('bank_id') as $key => $bankId) {
                $item = [
                        'type' => $data->type == 'Payment' ? 'Received' : 'Payment',
                        'flag' => 'Supplier Payment',
                        'flagable_id' => $data->id,
                        'flagable_type' => SupplierPayment::class,
                        'bank_id' => $bankId,
                        'datetime' => $data->date,
                        'note' => $data->note,
                        'amount' => $request->input("amount.$key"),
                    ];
                    $itemIdArr[] = $data->transactions()->updateOrCreate(['id' => $request->input("id.$key")], $item)->id;
                }
                $data->transactions()->whereNotIn('id', $itemIdArr)->delete();
        }

        session()->flash('successMessage', 'Supplier Payment was successfully updated!');
        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = SupplierPayment::findOrFail($id);
            Transaction::where('flagable_id', $data->id)->where('flagable_type', SupplierPayment::class)->delete();
            $data->delete();

            session()->flash('successMessage', 'Supplier Payment was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Supplier Payment deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
