@extends('admin.layouts.app')

@section('title_prepend', 'Bank')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-lg-flex justify-content-between align-items-center">
                    <h4 class="m-0">Banks</h4>
                    <div class="d-lg-flex">
                        <form method="GET" action="{{ route('bank.index') }}"
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
                                <a class="btn btn-outline-secondary btn-flat" href="{{ route('bank.index') }}"><i
                                        class="fa fa-times"></i></a>
                             
                                    <a class="btn btn-secondary btn-flat" href="{{ route('bank.create') . qString() }}"><i
                                            class="fa fa-plus"></i> Add</a>                             
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive-lg">
                    <table class="table table-bordered table-hover myTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Branch</th>
                                <th>Account Number</th>
                                <th>Status</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $val)
                                <tr>
                                    <td>{{ $val->name }}</td>
                                    <td>{{ $val->branch }}</td>
                                    <td>{{ $val->account_number }}</td>
                                    <td>{{ $val->status }}</td>                                                                        
                                    <td>
                                        <x-sp-components::action-group>                                            
                                                <a class="dropdown-item"
                                                    href="{{ route('bank.show', $val->id) . qString() }}"><i
                                                        class="fa fa-eye"></i> Show</a>
                                                <a class="dropdown-item"
                                                    href="{{ route('bank.edit', $val->id) . qString() }}"><i
                                                        class="fa fa-pencil"></i> Edit</a>                                                       
                                                <a class="dropdown-item"
                                                    onclick="deleted('{{ route('bank.destroy', $val->id) . qString() }}')"><i
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