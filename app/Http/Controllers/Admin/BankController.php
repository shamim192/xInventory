<?php

namespace App\Http\Controllers\Admin;

use App\Models\Bank;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BankController extends Controller
{
    public function index(Request $request)
    {
        $sql = Bank::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('branch', 'LIKE', $request->q.'%')
                ->orWhere('account_number', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.bank.index', compact('serial', 'records'));
    }

    public function create()
    {    

        return view('admin.bank.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255|unique:banks,name',
            'branch' => 'required|max:255',
            'account_number' => 'required|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Bank::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Bank was successfully added.');
        } else {
            session()->flash('errorMessage', 'Bank saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Bank::findOrFail($id);
        return view('admin.bank.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Bank::findOrFail($id);
        return view('admin.bank.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255|unique:banks,name,'.$id.',id',
            'branch' => 'required|max:255',
            'account_number' => 'required|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Bank::findOrFail($id);

        $updated = $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Bank was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Bank update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = Bank::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Bank was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Bank deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
    public function due(Request $request)
    {
        $receivedAmount = Transaction::where('type', 'Received')->where('bank_id', $request->id)->sum('amount');
        $paymentAmount = Transaction::where('type', 'Payment')->where('bank_id', $request->id)->sum('amount');
        $due = ($receivedAmount - $paymentAmount);

        return response()->json(['success' => true, 'due' => $due]);
    }
}
