@extends('layouts.auth')

@section('title', __('Confirm Password') )

@section('content')
  <aside class="shadow align-self-stretch">
    <div class="changeLang d-flex align-items-center justify-content-start mb-3 mb-md-5">
      @if(App::isLocale('en'))
        <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي" class="d-block">عربي</a>
      @else
        <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
      @endif
    </div><!-- changeLang -->
    <div class="title d-block text-body text-center mb-3 fw-bold">{{ __('Confirm Password') }}</div>
    <div class="desc text-center text-body mb-3">
      {{ __('Please confirm your password before continuing.') }}
    </div><!-- desc -->
    <div class="authSlider">
      <div class="item d-flex align-items-center justify-content-center">
        <img data-lazy="{{ asset('new/images/authSlideImg_1.webp') }}" alt="login_slide_1" class="mw-100">
      </div><!-- item -->
      <div class="item d-flex align-items-center justify-content-center">
        <img data-lazy="{{ asset('new/images/authSlideImg_2.webp') }}" alt="login_slide_2" class="mw-100">
      </div><!-- item -->
      <div class="item d-flex align-items-center justify-content-center">
        <img data-lazy="{{ asset('new/images/authSlideImg_3.webp') }}" alt="login_slide_2" class="mw-100">
      </div><!-- item -->
    </div><!-- authSlider -->
  </aside>
  <article class="flex-grow-1 d-flex align-items-center justify-content-center flex-column align-self-stretch">
    <div class="topArea w-100 py-4 flex-grow-1 d-flex align-items-center justify-content-center flex-column">
      <div class="logo d-flex align-items-center justify-content-center mb-3 mb-md-5">
        <a href="{{ url('/') }}" title="SureBills">
          <img src="{{ asset('new/images/logo.webp') }}" alt="SureBills" loading="lazy" width="586px" height="187px" class="mw-100 w-auto h-auto">
        </a>
      </div><!-- logo -->
      <h1 class="d-block mb-3 fw-normal text-body">{{ __('Confirm Password') }}</h1>
      <form method="POST" action="{{ route('password.confirm') }}" class="w-100 mx-auto">
        @csrf
        <div class="form_group mb-3">
          <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('password') is-invalid @enderror">
            <span class="d-flex align-items-center justify-content-center h-100 fal fa-lock-alt"></span>
            <input id="password" class="bg-white border-0 h-100 flex-grow-1 text-body" name="password" autocomplete="current-password" type="password" placeholder="{{ __('Password') }}" placeholder="" />
          </div><!-- inputIcon -->
          @error('password')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </div><!-- form_group -->
        <div class="d-flex align-items-center justify-content-between flex-wrap">
          @if (Route::has('password.request'))
            <a id="forgot_password" href="{{ route('password.request') }}" title="{{ __('Forgot Your Password?') }}">{{ __('Forgot Your Password?') }}</a>
          @endif
          <button class="login_button rounded border-0 fw-bold d-flex align-items-center justify-content-center text-white p-0" type="submit"> {{ __('Confirm Password') }}</button>
        </div><!-- d-flex -->
      </form>
    </div><!-- topArea -->
  </article>
@endsection