@extends('admin.layouts.auth')

@section('content')
<div class="login-box-body">
    <p class="login-box-msg">{{ __('Confirm Password') }}</p>
    <p>{{ __('Please confirm your password before continuing.') }}</p>

    <form method="POST" action="{{ route('password.confirm') }}" class="needs-validation" novalidate>
        @csrf

        <div class="form-group">
            <label for="password">{{ __('Password') }}</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" />

            @error('password')
                <span class="invalid-feedback">{{ $message }}</span>
            @enderror
        </div>

        <div class="row">
            <div class="col-12">
                <button type="submit" class="btn btn-secondary btn-block btn-flat">
                    {{ __('Confirm Password') }}
                </button>
            </div>

            <div class="col-12 text-center pt-15">
                @if (Route::has('password.request'))
                    <a class="btn btn-link" href="{{ route('password.request') }}">
                        {{ __('Forgot Your Password?') }}
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>
@endsection
