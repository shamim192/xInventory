@extends('admin.layouts.app')

@section('title_prepend', 'Unit')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2"> Unit #{{ $data->id }}</h4>
                    <div class="d-flex">
                        <a class="btn btn-secondary btn-flat float-right ml-3"
                            href="{{ route('units.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
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
                            <th>Base Unit</th>
                            <th>:</th>
                            <td>{{ $data->baseUnit->name }}</td>
                        </tr>                        
                        <tr>
                            <th>Quantity</th>
                            <th>:</th>
                            <td>{{ $data->quantity }}</td>
                        </tr>                  
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
