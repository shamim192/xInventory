<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Type;
use Illuminate\Http\Request;

class TypeController extends Controller
{
    public function index(Request $request)
    {

        $sql = Type::orderBy('id', 'DESC');

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

        return view('admin.types.index', compact('serial', 'records'));
    }

    public function create()
    {
        return view('admin.types.create');
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:types,name',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Type::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Type was successfully added.');
        } else {
            session()->flash('errorMessage', 'Type saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Type::findOrFail($id);        
        return view('admin.types.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Type::findOrFail($id);      
        return view('admin.types.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:types,name,'. $id,
            'status' => 'required|in:Active,Inactive',
        ]);

        $types = Type::findOrFail($id);

        $data = $types->update($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Type was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Type update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Type::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Type was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Type deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
