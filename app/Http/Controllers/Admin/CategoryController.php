<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {

        $sql =Category::with('parent')->orderBy('id', 'DESC');

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
        $parents = Category::with('children')->whereNull('parent_id')->orderBy('name', 'ASC')->get();

        return view('admin.categories.index', compact('serial', 'records','parents'));
    }

    public function create()
    {
        $parents = Category::with('children')->whereNull('parent_id')->orderBy('name', 'ASC')->get();
        return view('admin.categories.create', compact('parents'));
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:categories,name',
            'parent_id' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Category::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Category was successfully added.');
        } else {
            session()->flash('errorMessage', 'Category saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Category::with('parent')->findOrFail($id);        
        return view('admin.categories.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = Category::findOrFail($id); 
        $parents = Category::with('children')->whereNull('parent_id')->orderBy('name', 'ASC')->get();    
        return view('admin.categories.edit', compact('data','parents'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|string|max:255|unique:categories,name,'. $id,            
            'parent_id' => 'nullable|integer',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Category::findOrFail($id);

        $updated = $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Category was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Category update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = Category::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Category was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Category deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
