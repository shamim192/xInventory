<?php

namespace App\Http\Controllers\Admin;

use App\Models\LoanHolder;
use Illuminate\Http\Request;
use App\Services\LoanHolderService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoanHolderController extends Controller
{
    public function index(Request $request)
    {
        $sql = LoanHolder::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('address', 'LIKE', $request->q.'%')
                ->orWhere('status', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.loan-holder.index', compact('records','serial'));
    }

    public function create()
    {
        return view('admin.loan-holder.create');
    }

    public function store(Request $request)
    {
        $validatedData= $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:loan_holders,mobile',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);
        
        $data = LoanHolder::create($validatedData);

         if ($data) {
            session()->flash('successMessage', 'Loan Holder was successfully added.');
        } else {
            session()->flash('errorMessage', 'Loan Holder saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = LoanHolder::find($id);
        return view('admin.loan-holder.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = LoanHolder::find($id);
        return view('admin.loan-holder.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:loan_holders,mobile,'.$id.',id',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = LoanHolder::find($id);

        $updated =  $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Loan Holder was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Loan Holder update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = LoanHolder::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Loan Holder was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Loan Holder deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
    
    public function due(Request $request)
    {
        $due = LoanHolderService::due($request->id);

        return response()->json(['success' => true, 'due' => $due]);
    }
}
