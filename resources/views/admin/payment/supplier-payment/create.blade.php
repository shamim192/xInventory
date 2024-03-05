@extends('admin.layouts.app')

@section('content')
    <section class="content">
        <div class="card">
            <ul class="nav nav-tabs">
                <li class="nav-item">
                    <a class="nav-link text-dark" href="{{ route('supplier-payments.index') . qString() }}">
                        <i class="fa fa-list" aria-hidden="true"></i> Supplier Payment List
                    </a>
                </li>
                    <li class="nav-item">
                        <a class="nav-link text-dark {{!isset($adjustment) ? 'active' : '' }}"
                            href="{{ route('supplier-payments.create') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Supplier Payment
                        </a>
                    </li>

                    <li class="nav-item mx-2 text-center">
                        <a class="nav-link text-dark {{isset($adjustment) && $adjustment == true ? 'active' : ''}}" href="{{ route('supplier-payments.adjustment') . qString() }}">
                            <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                        </a>
                    </li>
            </ul>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier-payments.store') . qString() }}" id="are_you_sure"
                    class="form-horizontal">
                    @csrf

                    @if (isset($adjustment) && $adjustment == true)
                        @include('admin.payment.supplier-payment.form-adjustment')
                    @else
                        @include('admin.payment.supplier-payment.form')
                    @endif
                </form>
            </div>
        </div>
    </section>
@endsection
