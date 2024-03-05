<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Supplier;
use App\Services\SupplierService;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $sql = Supplier::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('shop_name', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.supplier.index', compact('serial', 'records'));
    }

    public function create()
    {    

        return view('admin.supplier.create');
    }

    public function store(Request $request)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:suppliers,mobile',
            'address' => 'nullable',
            'shop_name' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Supplier::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Supplier was successfully added.');
        } else {
            session()->flash('errorMessage', 'Supplier saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = Supplier::findOrFail($id);
        return view('admin.supplier.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = Supplier::findOrFail($id);
        return view('admin.supplier.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|max:255|unique:suppliers,mobile,'.$id.',id',
            'address' => 'nullable',
            'shop_name' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Supplier::findOrFail($id);

        $updated = $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Supplier was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Supplier update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = Supplier::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Supplier was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Supplier deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function due(Request $request)
    {
        $due = SupplierService::due($request->id);

        return response()->json(['success' => true, 'due' => $due]);
    }
}
