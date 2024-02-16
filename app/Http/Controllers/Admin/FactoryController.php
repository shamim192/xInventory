<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Factory;
use Illuminate\Http\Request;

class FactoryController extends Controller
{
    public function index(Request $request)
    {

        $sql = Factory::orderBy('id', 'DESC');

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
        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.factories.index', compact('serial', 'records'));
    }

    public function create()
    {
        return view('admin.factories.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string',
            'address' => 'nullable|string|max:250',
            'google_map' => 'nullable|string|max:255',
            'email' => 'nullable|unique:factories,email',
            'mobile_no' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive',
        ]);

        // dd($validatedData);
        $data = Factory::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Factory was successfully added.');
        } else {
            session()->flash('errorMessage', 'Factory saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Factory::findOrFail($id);
        return view('admin.factories.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Factory::findOrFail($id);
        return view('admin.factories.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string',
            'address' => 'nullable|string|max:250',
            'google_map' => 'nullable|string|max:255',           
            'email' => 'nullable|unique:factories,email,' . $id,
            'mobile_no' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive',
           
        ]);

        $factory = Factory::findOrFail($id);

        $data = $factory->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Factory was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Factory update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Factory::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Factory was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Factory deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
