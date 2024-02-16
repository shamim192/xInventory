<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Store;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function index(Request $request)
    {

        $sql = Store::orderBy('id', 'DESC');

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
        if ($request->type) {
            $sql->where('type', $request->type);
        }
        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.stores.index', compact('serial', 'records'));
    }

    public function create()
    {
        return view('admin.stores.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [

            'name' => 'required|string',
            'type' => 'required|in:Warehouse,Outlet',
            'address' => 'nullable|string|max:250',
            'google_map' => 'nullable|string|max:255',
            'email' => 'nullable|unique:stores,email',
            'mobile_no' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive',
        ]);

        // dd($validatedData);
        $data = Store::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Store was successfully added.');
        } else {
            session()->flash('errorMessage', 'Store saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Store::findOrFail($id);
        return view('admin.stores.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Store::findOrFail($id);
        return view('admin.stores.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string',
            'type' => 'required|in:Warehouse,Outlet',
            'address' => 'nullable|string|max:250',
            'google_map' => 'nullable|string|max:255',
            'email' => 'nullable|unique:stores,email,' . $id,
            'mobile_no' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive',

        ]);

        $store = Store::findOrFail($id);

        $data = $store->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Store was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Store update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Store::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Store was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Store deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
