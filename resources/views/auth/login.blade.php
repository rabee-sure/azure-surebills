@extends('layouts.auth')
@section('title', __('Login') )
@section('content')

<div class="row h-100">
  <div class="col-12 col-md-10 mx-auto my-auto">
    <div id="login_page" class="card auth-card">
      <div class="position-relative image-side">
        <p class=" text-black h2">{{ __('Start Sending Bills') }}</p>
        <p class="black mb-0">
          {{ __('Please use your credentials to login.') }}
          <br>
          {{ __('If you are not a member') }} , <a href="{{ route('register') }}" title="{{ __('Register a new account2') }}" class="black">{{ __('Register a new account2') }}</a> .
        </p>
        <div class="slide_auth">
          <div class="glide single">
            <div class="glide__track pb-3" data-glide-el="track">
              <div class="glide__slides">
                <div class="glide__slide"><img src="{{ asset('img/login_slide_1.png') }}" alt="login_slide_1"></div>
                <div class="glide__slide"><img src="{{ asset('img/login_slide_2.png') }}" alt="login_slide_2"></div>
                <div class="glide__slide"><img src="{{ asset('img/login_slide_3.png') }}" alt="login_slide_3"></div>
              </div><!-- glide__slides -->
            </div><!-- glide__track -->
          </div><!-- glide -->
        </div><!-- slide_auth -->
      </div>
      <div class="form-side">
        <div class="changeLang">
          @if(App::isLocale('en'))
            <a  href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">عربي</a>
          @else
            <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
          @endif
        </div>
        <a href="{{ url('/') }}"><span class="logo-single"></span></a>
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
            <button class="btn btn-primary btn-lg btn-shadow login_button" type="submit">{{ __('Login') }}</button>
          </div>
        </form>
        <hr>
        <a class="btn btn-lg btn-shadow register_now" href="{{ route('register') }}" title="{{ __('Register a new account') }}">{{ __('Register a new account') }}</a>
      </div>
    </div>
    <div class="copyrights_auth">
      صُنع بـ <i class="heart"></i> في <i class="ksa"></i>
    </div><!-- copyrights_auth -->
  </div>
</div>
@endsection
