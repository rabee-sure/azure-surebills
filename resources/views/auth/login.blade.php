@extends('layouts.auth')
@section('title', __('Login') )
@section('content')
<div class="row h-100">
  <div class="col-12 col-md-10 mx-auto my-auto">
    <div id="login_page" class="card auth-card">
      <div class="position-relative image-side">
        <p class=" text-white h2">{{ __('Start Sending Bills') }}</p>
        <p class="white mb-0">
          {{ __('Please use this form to register.') }}
          <br>
          {{ __('If you are a member, please') }} <a href="{{ route('register') }}" class="white">{{ __('Register') }}</a>.
        </p>
      </div>
      <div class="form-side">
        <a href="index.html"><span class="logo-single"></span></a>
        <h6 class="mb-4">{{ __('Login') }}</h6>
        <form method="POST" action="{{ route('login') }}">
          @csrf
          <label for="email" class="form-group has-float-label mb-4">
            <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus />
            <span>{{ __('E-Mail Address') }}</span>
            @error('email')
              <p class="invalid-feedback" role="alert">{{ $message }}</p>
            @enderror
          </label>
          <label for="password" class="form-group has-float-label mb-4">
            <input id="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password" type="password" placeholder="" />
            <span>{{ __('Password') }}</span>
            @error('password')
              <p class="invalid-feedback" role="alert">{{ $message }}</p>
            @enderror
          </label>
          <div class="d-flex justify-content-between align-items-center">
            <div class="form-check">
              <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">{{ __('Remember Me') }}</label>
            </div>
          </div>
          <div class="d-flex justify-content-between align-items-center">
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" title="{{ __('Forgot Your Password?') }}">{{ __('Forgot Your Password?') }}</a>
            @endif
            <button class="btn btn-primary btn-lg btn-shadow" type="submit">{{ __('Login') }}</button>
          </div>
        </form>
        <hr>
        <a class="btn btn-primary btn-lg btn-shadow register_now" href="{{ route('register') }}" title="Register a new account">Register a new account</a>
      </div>
    </div>
  </div>
</div>
@endsection
