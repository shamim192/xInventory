<?php

namespace App\Http\Controllers\Admin;

use App\Models\Unit;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\BaseUnit;

class UnitController extends Controller
{
    public function index(Request $request)
    {

        $sql = Unit::orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');              
                $q->where('base_unit_id', 'LIKE', '%' . $request->q . '%');              
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

        return view('admin.units.index', compact('serial', 'records'));
    }

    public function create()
    {
        $baseUnits = BaseUnit::get();
        return view('admin.units.create', compact('baseUnits'));
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'base_unit_id' => 'required|exists:base_units,id',            
            'quantity' => 'required|numeric',
        ]);

        $data = Unit::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Unit was successfully added.');
        } else {
            session()->flash('errorMessage', 'Unit saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Unit::findOrFail($id);        
        return view('admin.units.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Unit::findOrFail($id);
        $baseUnits = BaseUnit::get();
        return view('admin.units.edit', compact('data','baseUnits'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'base_unit_id' => 'required|exists:base_units,id',            
            'quantity' => 'required|numeric',
        ]);

        $units = Unit::findOrFail($id);

        $data = $units->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Unit was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Unit update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Unit::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Unit was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Unit deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
