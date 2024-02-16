@extends('admin.layouts.auth')

@section('content')
<div class="login-box-body">
    <p class="login-box-msg">{{ __('Sign in to start your session') }}</p>
    <form method="POST" action="{{ route('login') }}" class="needs-validation" novalidate>
        @csrf

        <div class="form-group">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autofocus placeholder="{{ __('Email') }}" />

            @error('email')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required placeholder="{{ __('Password') }}" />

            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            <div class="col-8">
                <div class="checkbox icheck">
                    <label> <input type="checkbox" name="remember" {{ old('remember') ? 'checked' : '' }}> {{ __('Remember Me') }}</label>
                </div>
            </div>
            <div class="col-4">
                <button type="submit" class="btn btn-secondary btn-block btn-flat">{{ __('Sign In') }}</button>
            </div>
        </div>
    </form>

    <a class="btn-link" href="{{ route('password.request') }}">{{ __('Forgot Your Password?') }}</a>
</div>
@endsection
