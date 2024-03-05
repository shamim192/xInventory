<?php

namespace App\Http\Controllers\Admin\Payment;

use App\Models\Invest;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Investor;
use App\Models\Transaction;

class InvestController extends Controller {

    public function index(Request $request)
    {
        $sql = Invest::with('bank','investor')->orderBy('date', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('note', 'LIKE', $request->q . '%')
                    ->orWhere('amount', 'LIKE', $request->q . '%');
            });
        }

        if ($request->investor) {
            $sql->where('investor_id', $request->investor);
        }
        if ($request->bank) {
            $sql->where('bank_id', $request->bank);
        }

        if ($request->from) {
            $sql->where('date', '>=', dbDateFormat($request->from));
        }

        if ($request->to) {
            $sql->where('date', '<=', dbDateFormat($request->to));
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        $banks = Bank::where('status', 'Active')->get();
        $investors = Investor::where('status', 'Active')->get();

        return view('admin.payment.invest.index', compact('records','serial', 'banks', 'investors'));
    }

    public function create()
    {
        $banks = Bank::where('status', 'Active')->get();
        $investors = Investor::where('status', 'Active')->get();

        return view('admin.payment.invest.create', compact('banks', 'investors'));
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'investor_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);
        
        $storeData = [
            'investor_id' => $request->investor_id,
            'bank_id' => $request->bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];
        
        $data = Invest::create($storeData);

        if ($data) {
            Transaction::create([
                'type' => 'Received',
                'flag' => 'Invest',
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Invest',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $data->note,
                'amount' => $data->amount,
            ]);
        }

        if ($data) {
            session()->flash('successMessage', 'Invest was successfully added.');
        } else {
            session()->flash('errorMessage', 'Invest saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show($id)
    {
        $data = Invest::with(['bank','investor'])->find($id);
     
        return view('admin.payment.invest.show', compact('data'));
    }

    public function edit($id)
    {
        $data = Invest::find($id);

        $banks = Bank::where('status', 'Active')->get();
        $investors = Investor::where('status', 'Active')->get();

        return view('admin.payment.invest.edit', compact('data', 'banks','investors'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'investor_id' => 'required|integer',
            'bank_id' => 'required|integer',
            'date' => 'required|date',
            'amount' => 'required|numeric',
        ]);

        $data = Invest::find($id);

        $storeData = [
            'investor_id' => $request->investor_id,
            'bank_id' => $request->bank_id,
            'date' => dbDateFormat($request->date),
            'note' => $request->note,
            'amount' => $request->amount,
        ];

       $updated = $data->update($storeData);

        if ($data) {
            Transaction::updateOrCreate([
                'flagable_id' => $data->id,
                'flagable_type' => 'App\Models\Invest',
            ], [
                'type' => 'Received',
                'flag' => 'Invest',
                'bank_id' => $data->bank_id,
                'datetime' => now(),
                'note' => $request->note,
                'amount' => $data->amount,
            ]);
        }

        if ($updated) {
            session()->flash('successMessage', 'Invest was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Invest update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {    
        try {
            $data = Invest::find($id);

            Transaction::where('flagable_id', $data->id)->where('flagable_type', 'App\Models\Invest')->delete();
            $data->delete();
            
            session()->flash('successMessage', 'Invest was successfully deleted!');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Invest deleting failed! Reason: ' . $e->getMessage());
        }
       
        return redirect()->action([self::class, 'index'], qArray());
    }
}
