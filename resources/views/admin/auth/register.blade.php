@extends('admin.layouts.auth')

@section('content')
<div class="login-box-body">
  <p class="login-box-msg">{{ __('Register') }}</p>
    <form method="POST" action="{{ route('register') }}" class="needs-validation" novalidate>
        @csrf

        <div class="form-group">
            <input type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" placeholder="{{ __('Name') }}" required />

            @error('name')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="{{ __('E-Mail Address') }}" required />

            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="{{ __('Password') }}" required />

            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <input type="password" class="form-control" name="password_confirmation" placeholder="{{ __('Confirm Password') }}" required />
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-secondary btn-block btn-flat">{{ __('Register') }}</button>
            </div>

            <div class="col-12 text-center pt-15">
                <a href="{{ route('login') }}"><i class="fa fa-angle-double-left" aria-hidden="true"></i> {{ __('back to login') }}</a>
            </div>
        </div>
    </form>
</div>
@endsection
