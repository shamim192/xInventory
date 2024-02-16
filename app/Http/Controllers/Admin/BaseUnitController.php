<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BaseUnit;
use Illuminate\Http\Request;

class BaseUnitController extends Controller
{
    public function index(Request $request)
    {

        $sql = BaseUnit::orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');               
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

        return view('admin.base-units.index', compact('serial', 'records'));
    }

    public function create()
    {

        $base_units = BaseUnit::get();
        return view('admin.base-units.create', compact('base_units'));
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'status' => 'required|in:Active,Inactive',
            
        ]);

        $data = BaseUnit::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Base Unit was successfully added.');
        } else {
            session()->flash('errorMessage', 'Base Unit saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = BaseUnit::findOrFail($id);
        return view('admin.base-units.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = BaseUnit::findOrFail($id);
        return view('admin.base-units.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',            
            'status' => 'required|in:Active,Inactive',          
        ]);

        $base_units = BaseUnit::findOrFail($id);

        $data = $base_units->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Base Unit was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Base Unit update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = BaseUnit::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Base Unit was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Base Unit deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
