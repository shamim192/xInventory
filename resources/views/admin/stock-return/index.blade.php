@extends('admin.layouts.app')

@section('title_prepend', 'Stock Return')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-lg-flex justify-content-between align-items-center">
                    <h4 class="m-0">Stock Returns</h4>
                    <div class="d-lg-flex">
                        <form method="GET" action="{{ route('stock-return.index') }}" class="d-lg-flex justify-content-end">
                            <div class="form-group mb-0">
                                <div class="input-group">
                                    <select class="form-control" name="supplier">
                                        <option value="">Any Supplier</option>
                                        @foreach ($suppliers as $supplier)
                                            <option value="{{ $supplier->id }}"
                                                {{ Request::get('supplier') == $supplier->id ? 'selected' : '' }}>
                                                {{ $supplier->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
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
                                <a class="btn btn-outline-secondary btn-flat" href="{{ route('stock-return.index') }}"><i
                                        class="fa fa-times"></i></a>

                                <a class="btn btn-secondary btn-flat" href="{{ route('stock-return.create') . qString() }}"><i
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
                                <th>Supplier</th>
                                <th>Date</th>
                                <th>Challan Number</th>
                                <th>Challan Date</th>
                                <th class="text-right">Quantity</th>
                                <th class="text-right">Amount</th>
                                <th class="col-action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($records as $val)
                                <tr>
                                    <td>{{ isset($val->supplier) ? $val->supplier->name : '' }}</td>
                                    <td>{{ dateFormat($val->date) }}</td>
                                    <td>{{ $val->stock->challan_number ?? 'N/A' }}</td>
                                    <td>{{ dateFormat($val->stock->challan_date) }}</td>
                                    <td class="text-right">{{ $val->items->sum('quantity') }} </td>
                                    <td class="text-right">{{ $val->items->sum('amount') }} </td>

                                    <td>
                                        <x-sp-components::action-group>
                                            <a class="dropdown-item"
                                                href="{{ route('stock-return.show', $val->id) . qString() }}"><i
                                                    class="fa fa-eye"></i> Show</a>
                                            <a class="dropdown-item"
                                                href="{{ route('stock-return.edit', $val->id) . qString() }}"><i
                                                    class="fa fa-pencil"></i> Edit</a>
                                            <a class="dropdown-item"
                                                onclick="deleted('{{ route('stock-return.destroy', $val->id) . qString() }}')"><i
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
