<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {

        $sql = Size::orderBy('id', 'DESC');

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

        return view('admin.sizes.index', compact('serial', 'records'));
    }

    public function create()
    {
        return view('admin.sizes.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:sizes,name',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Size::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Size was successfully added.');
        } else {
            session()->flash('errorMessage', 'Size saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Size::findOrFail($id);        
        return view('admin.sizes.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Size::findOrFail($id);      
        return view('admin.sizes.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:sizes,name,'. $id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $sizes = Size::findOrFail($id);

        $data = $sizes->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Size was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Size update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Size::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Size was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Size deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
