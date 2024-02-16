<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use Illuminate\Http\Request;

class CompanyController extends Controller
{
    public function index(Request $request)
    {

        $sql = Company::orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('email', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('address', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('mobile_no', 'LIKE', '%' . $request->q . '%');
            });
        }
        if ($request->from) {
            $sql->where('created_at', '>=', $request->from);
        }

        if ($request->to) {
            $sql->where('updated_at', '<=', $request->to);
        }

        $records = $sql->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.companies.index', compact('serial', 'records'));
    }

    public function create()
    {        
        return view('admin.companies.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string',
            'address' => 'nullable|string|max:250',
            'email' => 'nullable|unique:companies',
            'mobile_no' => 'nullable|integer',
        ]);

        $data = Company::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Company was successfully added.');
        } else {
            session()->flash('errorMessage', 'Company saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Company::findOrFail($id);
        return view('admin.companies.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Company::findOrFail($id);
        return view('admin.companies.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string',
            'address' => 'nullable|string|max:250',
            'email' => 'nullable|unique:companies,email,' . $id,
            'mobile_no' => 'nullable|integer',
        ]);

        $company = Company::findOrFail($id);

        $data = $company->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Company was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Company update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Company::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Company was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Company deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
