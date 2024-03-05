<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $sql = User::orderBy('id', 'DESC');

        if ($request->q) {
            $sql->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('email', 'LIKE', '%' . $request->q . '%');
                $q->orWhere('mobile', 'LIKE', '%' . $request->q . '%');
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

        return view('admin.user.index', compact('serial', 'records'));
    }

    public function create()
    {        
        return view('admin.user.create');
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|numeric|unique:users,mobile',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|max:20|min:8|confirmed',
            'status' => 'required|in:Active,Inactive',
        ]);

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'status' => $request->status,
        ];

        $data = User::create($storeData);

        if ($data) {
            session()->flash('successMessage', 'User was successfully added.');
        } else {
            session()->flash('errorMessage', 'User saving failed!');
        }

        return redirect()->action([self::class, 'create'], qArray());
    }

    public function show(Request $request, $id)
    {
        $data = User::findOrFail($id);
        return view('admin.user.show', compact('data'));
    }

    public function edit(Request $request, $id)
    {
        $data = User::findOrFail($id);
        return view('admin.user.edit', compact('data'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required|max:255',
            'mobile' => 'required|numeric|unique:users,mobile,' . $id . ',id',
            'email' => 'required|email|max:255|unique:users,email,' . $id . ',id',
            'status' => 'required|in:Active,Inactive',
        ]);

        $data = User::find($id);      

        $storeData = [
            'name' => $request->name,
            'mobile' => $request->mobile,
            'email' => $request->email,
            'status' => $request->status,
        ];

        if ($request->password != '') {
            $this->validate($request, [
                'password' => 'required|max:20|min:8|confirmed',
            ]);
            $storeData['password'] = Hash::make($request->password);
        }

        $data->update($storeData);

        if ($data) {
            session()->flash('successMessage', 'User was successfully updated.');
        } else {
            session()->flash('errorMessage', 'User update failed!');
        }

        return redirect()->action([self::class, 'index'], qArray());
    }

    public function destroy(Request $request, $id)
    {
        try {
            $data = User::findOrFail($id);
            $data->delete();

            session()->flash('successMessage', 'User was successfully deleted.');
        } catch (\Exception $e) {
            session()->flash('errorMessage', 'User deleting failed! Reason: ' . $e->getMessage());
        }

        return redirect()->action([self::class, 'index'], qArray());
    }
}
