@extends('admin.layouts.app')

@section('title_prepend', 'Factory')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Factory #{{ $data->id }}</h4>
                    <div class="d-flex">
                        <a class="btn btn-secondary btn-flat float-right ml-3"
                            href="{{ route('factories.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
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
                            <th>Address</th>
                            <th>:</th>
                            <td>{{ $data->address ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Google Map</th>
                            <th>:</th>
                            <td>{{ $data->google_map ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Email</th>
                            <th>:</th>
                            <td>{{ $data->email ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Mobile No</th>
                            <th>:</th>
                            <td>{{ $data->mobile_no ?? '-' }}</td>
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
