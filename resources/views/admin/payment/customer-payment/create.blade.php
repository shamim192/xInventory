@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="card">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('customer-payments.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Customer Payment List
                    </a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark {{!isset($adjustment) ? 'active' : '' }}"
                            href="{{ route('customer-payments.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Customer Payment
                        </a>
                    </li>

                    <li class="nav-item mx-2 text-center">
                        <a class="nav-link text-dark {{isset($adjustment) && $adjustment == true ? 'active' : ''}}" href="{{ route('customer-payments.adjustment') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                        </a>
                    </li>
            </ul>
            <div class="card-body">
                <form method="POST" action="{{ route('customer-payments.store') . qString() }}" id="are_you_sure"
                    class="form-horizontal">
                    @csrf

                    @if (isset($adjustment) && $adjustment == true)
                        @include('admin.payment.customer-payment.form-adjustment')
                    @else
                        @include('admin.payment.customer-payment.form')
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection
