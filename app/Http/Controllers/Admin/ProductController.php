<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\BaseUnit;
use Illuminate\Http\Request;
use App\Services\CategoryService;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $sql = Product::orderBy('name', 'ASC');

       
        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%'. $request->q . '%')
                    ->orWhere('code', 'LIKE', '%'. $request->q . '%')
                    ->orWhere('model', 'LIKE', '%'. $request->q . '%')
                    ->orWhere('purchase_price', 'LIKE', '%'. $request->q . '%')
                    ->orWhere('mrp', 'LIKE', '%'. $request->q . '%')
                    ->orWhere('discount_percentage', 'LIKE', '%'. $request->q . '%');
            });
            $sql->orwhereHas('baseUnit', function($q) use($request) {
                $q->where('name', 'LIKE','%'. $request->q . '%');
            });
            $sql->orwhereHas('category', function($q) use($request) {
                $q->where('name', 'LIKE','%'. $request->q . '%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.product.index', compact('serial', 'records'));
    }

    public function create()
    {  
        $categories = CategoryService::get(true);
        $baseUnits = BaseUnit::get();
        
        return view('admin.product.create',compact('categories','baseUnits'));
    }

    public function store(Request $request)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'code' => 'required|string|unique:products,code',
            'model' => 'nullable|max:255',
            'category_id' => 'nullable|integer',
            'base_unit_id' => 'required|exists:base_units,id',
            'purchase_price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Product::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Product was successfully added.');
        } else {
            session()->flash('errorMessage', 'Product saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Product::findOrFail($id);
        return view('admin.product.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {

        $data = Product::findOrFail($id);
        $baseUnits = BaseUnit::get();        
        $categories = CategoryService::get(true);
        return view('admin.product.edit', compact('data','baseUnits','categories'));
    }

    public function update(Request $request, $id)
    {

        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'code' => 'required|string|unique:products,code,' . $id,
            'model' => 'nullable|max:255',
            'category_id' => 'nullable|integer',
            'base_unit_id' => 'required|exists:base_units,id',
            'purchase_price' => 'required|numeric|min:0',
            'mrp' => 'nullable|numeric|min:0',
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Product::findOrFail($id);

        $updated = $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Product was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Product update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {

        try {
            $data = Product::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Product was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Product deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
