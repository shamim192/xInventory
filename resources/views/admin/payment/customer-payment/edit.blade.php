@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="card">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('customer-payments.index').qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Customer Payment List
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('customer-payments.create').qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Customer Payment
                    </a>
                </li>
                        
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('customer-payments.adjustment') . qString() }}">
                        <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                    </a>
                </li>
    
                <li class="nav-item">
                    <a class="nav-link text-dark active" href="javascript:void(0);">
                        <i class="fa fa-edit" aria-hidden="true"></i> Edit Customer Payment
                    </a>
                </li>
            </ul>

            <div class="card-body">
                <form method="POST" action="{{ route('customer-payments.update', $data->id) . qString() }}"
                    id="are_you_sure" class="form-horizontal">
                    @csrf
                    @method('PUT')

                    @if ($data->type == 'Adjustment')
                        @include('admin.payment.customer-payment.form-adjustment')
                    @else
                        @include('admin.payment.customer-payment.form')
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection
