@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs">
                    <li class="nav-item active">
                        <a class="nav-link text-dark active" href="{{ route('customer-payments.index') . qString() }}">
                            <i class="fa fa-list" aria-hidden="true"></i> Customer Payment List
                        </a>
                    </li>
                        <li>
                            <a class="nav-link text-dark" href="{{ route('customer-payments.create') . qString() }}">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Customer Payment
                            </a>
                        </li>

                        <li>
                            <a class="nav-link text-dark" href="{{ route('customer-payments.adjustment') . qString() }}">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                            </a>
                        </li>
                </ul>

                <div class="my-4">
                    <form method="GET" action="{{ route('customer-payments.index') }}" class="d-lg-flex justify-content-end">
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
                            <button type="submit" class="btn btn-outline-secondary btn-flat"><i class="fa fa-search"></i>
                                Search</button>
                            <a class="btn btn-outline-secondary btn-flat"
                                href="{{ route('customer-payments.index') }}"><i class="fa fa-times"></i></a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card-body">
                <div class="box-body table-responsive-lg">
                    <table class="table table-bordered table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Date</th>                                
                                <th>Note</th>
                                <th>Type</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $val)
                                <tr>
                                    <td>{{ dateFormat($val->date) }}</td>
                                    <td>{{ $val->note }}</td>
                                    <td>{{ $val->type }}</td>
                                    <td>{{ optional($val->customer)->name }}</td>
                                    <td>{{ $val->amount }}</td>
                                    <td>
                                        <x-sp-components::action-group>
                                                <a class="dropdown-item"
                                                    href="{{ route('customer-payments.show', $val->id) . qString() }}">
                                                    <i class="fa fa-eye"></i> Show
                                                </a>
                                                <a class="dropdown-item" href="{{ route('customer-payments.edit', $val->id) . qString() }}">
                                                    <i class="fa fa-edit"></i> Edit
                                                </a>
                                                <a class="dropdown-item"
                                                    onclick="deleted('{{ route('customer-payments.destroy', $val->id) . qString() }}')">
                                                    <i class="fa fa-trash"></i> Delete
                                                </a>
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
        </div>
    </section>
@endsection
