@extends('admin.layouts.app')

@section('title_prepend', 'Fund Transfer')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Fund Transfer #{{ $data->id }}</h4>
                    <div class="d-flex">
                            <a class="btn btn-secondary btn-flat float-right ml-3"
                                href="{{ route('fund-transfer.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Bank (From)</th>
                            <th style="width:10px;">:</th>
                            <td>{{ optional($data->fromBank)->name }}</td>
                        </tr>
                        <tr>
                            <th>Bank (To)</th>
                            <th>:</th>
                            <td>{{ optional($data->toBank)->name }}</td>
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
