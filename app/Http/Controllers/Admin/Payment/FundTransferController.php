<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Bank;
use App\Models\Transaction;
use App\Models\FundTransfer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FundTransferController extends Controller {

    public function index(Request $request)
    {
        $sql = FundTransfer::with('fromBank', 'toBank')->orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('note', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('date', 'LIKE', '%' . $request->q . '%')
                    ->orWhere('amount', 'LIKE', '%' . $request->q . '%');
            });

            $sql->orwhereHas('fromBank', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('toBank', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
            $sql->orwhereHas('creator', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
            });
        }
     
        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }
        
        $records = $sql->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.payment.fund-transfer.index', compact('serial', 'records'));
    }

    public function create()
    {
        $banks = Bank::select('id', 'name')->where('status', 'Active')->get();
        return view('admin.payment.fund-transfer.create', compact('banks'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'from_bank_id' => 'required|integer',
            'to_bank_id' => 'required|integer|different:from_bank_id',
            'date' => 'required|date',
            'amount' => 'required|numeric',            
        ]);

        $storeData = [
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id' => $request->to_bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

        $data = FundTransfer::create($storeData);

        if ($data) {
            Transaction::insert([
                [
                    'type' => 'Received',
                    'flag' => 'Fund Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->to_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ],
                [
                    'type' => 'Payment',
                    'flag' => 'Fund Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->from_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ]
            ]);
        }
        if ($data) {
            session()->flash('successMessage', 'Fund Transfer was successfully added.');
        } else {
            session()->flash('errorMessage', 'Fund Transfer saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = FundTransfer::with('fromBank', 'toBank')->find($id);       
        return view('admin.payment.fund-transfer.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = FundTransfer::findOrFail($id);
        $banks = Bank::select('id', 'name')->where('status', 'Active')->get();
        return view('admin.payment.fund-transfer.edit', compact('data', 'banks'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'from_bank_id' => 'required|integer',
            'to_bank_id' => 'required|integer|different:from_bank_id',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = FundTransfer::find($id);

        $storeData = [
            'from_bank_id' => $request->from_bank_id,
            'to_bank_id' => $request->to_bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

      $updated =  $data->update($storeData);

        if ($data) {
            Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\FundTransfer')->forceDelete();
            Transaction::insert([
                [
                    'type' => 'Received',
                    'flag' => 'Fund Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->to_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ],
                [
                    'type' => 'Payment',
                    'flag' => 'Fund Transfer',
                    'flagable_id' => $data->id,
                    'flagable_type' => 'App\Models\FundTransfer',
                    'bank_id' => $data->from_bank_id,
                    'datetime' => now(),
                    'note' => $data->note,
                    'amount' => $data->amount,
                    'created_at' => now(),
                ]
            ]);
        }

        if ($updated) {
            session()->flash('successMessage', 'Fund Transfer was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Fund Transfer update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = FundTransfer::findOrFail($id);
            Transaction::where('flagable_id', $data->id)->where('flagable_type', FundTransfer::class)->delete();

            $data->delete();

            session()->flash('successMessage', 'Fund Transfer was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Fund Transfer deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
