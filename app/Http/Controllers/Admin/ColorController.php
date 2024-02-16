<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request)
    {

        $sql = Color::orderBy('id', 'DESC');

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

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.colors.index', compact('serial', 'records'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:colors,name',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Color::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Color was successfully added.');
        } else {
            session()->flash('errorMessage', 'Color saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Color::findOrFail($id);        
        return view('admin.colors.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Color::findOrFail($id);      
        return view('admin.colors.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:colors,name,'. $id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $colors = Color::findOrFail($id);

        $data = $colors->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Color was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Color update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Color::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Color was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Color deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
