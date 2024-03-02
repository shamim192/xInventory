@extends('admin.layouts.app')

@section('title_prepend', 'Sale Return')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Sale Return #{{ $data->id }}</h4>
                    <div class="d-flex">
                        <a class="btn btn-secondary btn-flat float-right ml-3"
                            href="{{ route('sale-return.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:170px;">Customer</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->customer != null ? $data->customer->name : '' }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Sale ID</th>
                            <th>:</th>
                            <td>{{ $data->sale->id }}</td>
                        </tr>
                        <tr>
                            <th>Sale Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->sale->date) }}</td>
                        </tr>
                    </table>

                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Product</th>
                                <th>Unit</th>
                                <th style="text-align: right;">Quantity</th>
                                <th style="text-align: right;">Unit Price</th>
                                <th style="text-align: right;">Total Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->items as $key => $item)
                                <tr>
                                    <td>{{ $item->category != null ? $item->category->name : '-' }}</td>
                                    <td>{{ $item->product != null ? $item->product->name .  ' (' . $item->product->code . ')' : '-' }}</td>
                                    <td>{{ $item->unit != null ? $item->unit->name : '-' }}</td>
                                    <td style="text-align: right;">{{ $item->quantity }}</td>
                                    <td style="text-align: right;">{{ $item->unit_price }}</td>
                                    <td style="text-align: right;">{{ $item->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th style="text-align: right;" colspan="3">Total Quantity :</th>
                                <th style="text-align: right;">{{ $data->items->sum('quantity') }}</th>
                                <th style="text-align: right;">Sub Total Amount:</th>
                                <th style="text-align: right;">{{ numberFormat($data->items->sum('amount')) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
