@extends('admin.layouts.app')
<style>
    .unit .select2-selection.select2-selection--single {
       min-width: 50px !important;
   }
</style>
@section('title_prepend', 'Sale')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Sale</h4>
                    <div class="d-flex">
                        <a class="btn btn-secondary btn-flat float-right ml-3"
                            href="{{ route('sale.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('sale.store') }}{{ qString() }}"
                    enctype="multipart/form-data" id="are_you_sure" class="needs-validation" novalidate>
                    @csrf

                    @include('admin.sale.form')

                    <div class="text-center">
                        <button type="submit" id="submit" class="btn btn-secondary btn-flat">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary btn-flat">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
