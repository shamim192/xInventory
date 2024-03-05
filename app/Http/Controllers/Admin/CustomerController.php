<?php

namespace App\Http\Controllers\Admin;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\CustomerService;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $sql = Customer::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('shop_name', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.customer.index', compact('serial', 'records'));
    }

    public function create()
    {    

        return view('admin.customer.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:customers,mobile',
            'address' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'shop_name' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Customer::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Customer was successfully added.');
        } else {
            session()->flash('errorMessage', 'Customer saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Customer::findOrFail($id);
        return view('admin.customer.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Customer::findOrFail($id);
        return view('admin.customer.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:customers,mobile,'.$id.',id',
            'address' => 'nullable',
            'date_of_birth' => 'nullable|date',
            'shop_name' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Customer::findOrFail($id);

        $updated = $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Customer was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Customer update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Customer::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Customer was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Customer deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function due(Request $request)
    {
        $due = CustomerService::due($request->id);

        return response()->json(['success' => true, 'due' => $due]);
    }
}
