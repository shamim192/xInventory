@extends('admin.layouts.app')

@section('title_prepend', 'Supplier')

@section('content')
    <section class="content">
        <div class="card">
            <div class="card-header">
                <div class="d-flex justify-content-between">
                    <h4 class="m-0 mt-2">Supplier</h4>
                    <div class="d-flex">
                        <a class="btn btn-secondary btn-flat float-right ml-3"
                            href="{{ route('supplier.index') . qString() }}"><i class="fa fa-arrow-left"></i> Back</a>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier.store') }}{{ qString() }}"
                    enctype="multipart/form-data" id="are_you_sure" class="needs-validation" novalidate>
                    @csrf

                    @include('admin.supplier.form')

                    <div class="text-center">
                        <button type="submit" id="submit" class="btn btn-secondary btn-flat">Submit</button>
                        <button type="reset" class="btn btn-outline-secondary btn-flat">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
@endsection
