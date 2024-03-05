@extends('admin.layouts.app')

@section('title_prepend', 'Income')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Income #{{ $data->id }}</h4>
                    <div class="d-flex">
                            <a class="btn btn-secondary btn-flat float-right ml-3"
                                href="{{ route('income.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="box-body table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Income No.</th>
                            <th style="width:10px;">:</th>
                            <td>{{ $data->code }}</td>
                        </tr>
                        <tr>
                            <th>Income Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <th>:</th>
                            <td>{!! nl2br($data->note) !!}</td>
                        </tr>
                    </table>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>SL</th>
                                <th>Category</th>
                                <th>Bank</th>
                                <th>Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($data->items as $key => $item)
                                <tr>
                                    <td>{{$key + 1 }}</td>
                                    <td>{{ optional($item->category)->name }}</td>
                                    <td>{{ optional($item->bank)->name }}</td>
                                    <td style="text-align: right;">{{ $item->amount }}</td>
                                </tr>
                            @endforeach
                        </tbody>

                        <tfoot>
                            <tr>
                                <th style="text-align: right;" colspan="3">Total Amount :</th>
                                <th style="text-align: right;">{{ numberFormat($data->items->sum('amount')) }}</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection
