<?php

namespace App\Http\Controllers\Admin\Expense;

use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CategoryController extends Controller {

    public function index(Request $request)
    {
        $sql = ExpenseCategory::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where('name', 'LIKE', $request->q . '%');
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.expense-category.index', compact('records', 'serial'));
    }

    public function create()
    {
        return view('admin.expense-category.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:expense_categories,name',
            'status' => 'required|in:Active,Inactive',
        ]);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
        ];

        $data = ExpenseCategory::create($storeData);

        if ($data) {
            session()->flash('successMessage', 'Category was successfully added!');
        } else {
            session()->flash('errorMessage', 'Category saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = ExpenseCategory::find($id);

        return view('admin.expense-category.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = ExpenseCategory::find($id);

        return view('admin.expense-category.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255|unique:expense_categories,name,' . $id . ',id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = ExpenseCategory::find($id);

        $storeData = [
            'name' => $request->name,
            'status' => $request->status,
        ];

       $updated = $data->update($storeData);

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
            $data = ExpenseCategory::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Category was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Category deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
