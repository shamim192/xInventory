@extends('admin.layouts.app')

@section('title_prepend', 'Invest')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Invest #{{ $data->id }}</h4>
                    <div class="d-flex">
                            <a class="btn btn-secondary btn-flat float-right ml-3"
                                href="{{ route('invest.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Investor</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->investor != null ? $data->investor->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Bank</th>
                            <th>:</th>
                            <td>{{ $data->bank != null ? $data->bank->name : '-' }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <th>:</th>
                            <td>{!! nl2br($data->note) !!}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th>:</th>
                            <td>{{ $data->amount }}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
