@extends('admin.layouts.app')

@section('title_prepend', 'Product')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Product #{{ $data->id }}</h4>
                    <div class="d-flex">
                        <a class="btn btn-secondary btn-flat float-right ml-3"
                            href="{{ route('products.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:160px;">Name</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->name }}</td>
                        </tr>
                        <tr>
                            <th>Model</th>
                            <th>:</th>
                            <td>{{ $data->model }}</td>
                        </tr>
                        <tr>
                            <th>Category</th>
                            <th>:</th>
                            <td>{{ $data->category_id != null ?  $data->category->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Base Unit</th>
                            <th>:</th>
                            <td>{{ $data->baseUnit->name }}</td>
                        </tr>                               
                        <tr>
                            <th>Purchase Price</th>
                            <th>:</th>
                            <td>{{ $data->purchase_price }}</td>
                        </tr>                                
                        <tr>
                            <th>MRP</th>
                            <th>:</th>
                            <td>{{ $data->mrp }}</td>
                        </tr>
                        <tr>
                            <th>Discount Percentage</th>
                            <th>:</th>
                            <td>{{ $data->discount_percentage }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <th>:</th>
                            <td>{{ $data->status }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
