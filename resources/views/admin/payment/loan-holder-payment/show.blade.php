@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs">
                    <li class="nav-item">
                        <a class="nav-link text-dark" href="{{ route('loan-holder-payments.index') . qString() }}">
                            <i class="fa fa-list" aria-hidden="true"></i> Loan Holder Payment List
                        </a>
                    </li>
                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('loan-holder-payments.create') . qString() }}">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Loan Holder Payment
                            </a>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link text-dark" href="{{ route('loan-holder-payments.adjustment') . qString() }}">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                            </a>
                        </li>

                    <li class="nav-item">
                        <a class="nav-link text-dark active" href="javascript:void(0);">
                            <i class="fa fa-eye" aria-hidden="true"></i> Loan Holder Payment Details
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th style="width:120px;">Loan Holder</th>
                            <th style="width:10px;">:</th>
                            <td>{{ optional($data->loanHolder)->name }}</td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->date) }}</td>
                        </tr>
                        <tr>
                            <th>Type</th>
                            <th>:</th>
                            <td>{{ $data->type }}</td>
                        </tr>
                        <tr>
                            <th>Amount</th>
                            <th>:</th>
                            <td>{{ $data->total_amount }}</td>
                        </tr>
                        <tr>
                            <th>Note</th>
                            <th>:</th>
                            <td>{!! nl2br($data->note) !!}</td>
                        </tr>
                        <tr>
                            <th>Created At</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->created_at, 1) }}</td>
                        </tr>
                        <tr>
                            <th>Updated At</th>
                            <th>:</th>
                            <td>{{ dateFormat($data->updated_at, 1) }}</td>
                        </tr>
                    </table>
                </div>

                @if ($data->transactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th>SL</th>
                                    <th>Bank</th>
                                    <th style="text-align: right;">Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data->transactions as $key => $item)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>{{ $item->bank->name ?? '-' }}</td>
                                        <td style="text-align: right;">{{ $item->amount }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="2">Total</th>
                                    <th style="text-align: right;">{{ $data->total_amount }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection