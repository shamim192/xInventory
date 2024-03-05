@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="card">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('loan-holder-payments.index').qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Loan Holder Payment List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('loan-holder-payments.create').qString() }}">
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
                        <i class="fa fa-edit" aria-hidden="true"></i> Edit Loan Holder Payment
                    </a>
                </li>
            </ul>

            <div class="card-body">
                <form method="POST" action="{{ route('loan-holder-payments.update', $data->id) . qString() }}"
                    id="are_you_sure" class="form-horizontal">
                    @csrf
                    @method('PUT')

                    @if ($data->type == 'Adjustment')
                        @include('admin.payment.loan-holder-payment.form-adjustment')
                    @else
                        @include('admin.payment.loan-holder-payment.form')
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection
