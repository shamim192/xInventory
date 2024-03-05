<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Investor;

class InvestorController extends Controller {

    public function index(Request $request)
    {
        $sql = Investor::orderBy('name', 'ASC');

        if ($request->q) {
            $sql->where(function($q) use($request) {
                $q->where('name', 'LIKE', $request->q.'%')
                ->orWhere('mobile', 'LIKE', $request->q.'%')
                ->orWhere('address', 'LIKE', $request->q.'%')
                ->orWhere('status', 'LIKE', $request->q.'%');
            });
        }

        if ($request->status) {
            $sql->where('status', $request->status);
        }

        $records = $sql->latest('id')->paginate(paginateLimit());
        $serial = pagiSerial($records);

        return view('admin.investor.index', compact('records','serial'))->with('list', 1);
    }

    public function create()
    {
        return view('admin.investor.create');
    }

    public function store(Request $request)
    {
       $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:investors,mobile',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data =  Investor::create($validatedData);

        if ($data) {
            session()->flash('successMessage', 'Investor was successfully added.');
        } else {
            session()->flash('errorMessage', 'Investor saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {

        $data = Investor::find($id);
        return view('admin.investor.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = Investor::find($id);
        return view('admin.investor.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $validatedData = $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'nullable|max:255|unique:investors,mobile,'.$id.',id',
            'address' => 'nullable|max:255',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = Investor::find($id);

        $updated =  $data->update($validatedData);

        if ($updated) {
            session()->flash('successMessage', 'Investor was successfully updated.');
        } else {
            session()->flash('errorMessage', 'Investor update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy($id)
    {
        try {
            $data = Investor::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'Investor was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'Investor deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
