@extends('admin.layouts.app')

@section('title_prepend', 'User')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-lg-flex justify-content-between align-items-center">
                    <h4 class="m-0">Users</h4>
                    <div class="d-lg-flex">
                        <form method="GET" action="{{ route('user.index') }}"
                            class="d-lg-flex justify-content-end">
                            <div class="form-group mb-0">
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">From</span>
                                    </div>
                                    <input type="text" class="form-control" id="datepickerFrom" name="from"
                                        value="{{ Request::get('from') }}">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text">To</span>
                                    </div>
                                    <input type="text" class="form-control" id="datepickerTo" name="to"
                                        value="{{ Request::get('to') }}">
                                </div>
                            </div>

                            <div class="form-group mb-0">
                                <input type="text" class="form-control" name="q" value="{{ Request::get('q') }}"
                                    placeholder="Write your search text...">
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" class="btn btn-outline-secondary btn-flat"><i
                                        class="fa fa-search"></i> Search</button>
                                <a class="btn btn-outline-secondary btn-flat" href="{{ route('user.index') }}"><i
                                        class="fa fa-times"></i></a>
                             
                                    <a class="btn btn-secondary btn-flat" href="{{ route('user.create') . qString() }}"><i
                                            class="fa fa-plus"></i> Add</a>                             
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover myTable">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Mobile No</th>  
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $val)
                                <tr>
                                    <td>{{ $serial++ }}</td>
                                    <td>{{ $val->name }}</td>
                                    <td>{{ $val->email ?? '-' }}</td>
                                    <td>{{ $val->mobile ?? '-' }}</td>
                                                                        
                                    <td>
                                        <x-sp-components::action-group>                                            
                                                <a class="dropdown-item"
                                                    href="{{ route('user.show', $val->id) . qString() }}"><i
                                                        class="fa fa-eye"></i> Show</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('user.edit', $val->id) . qString() }}"><i
                                                        class="fa fa-pencil"></i> Edit</a>                                                       
                                                <a class="dropdown-item"
                                                    onclick="deleted('{{ route('user.destroy', $val->id) . qString() }}')"><i
                                                        class="fa fa-trash"></i> Delete</a>
                                        </x-sp-components::action-group>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <x-sp-components::pagination-row :records="$records" />
            </div>
        </div>       
    </section>
@endsection
