@extends('layouts.auth')
@section('title', __('Reset Password') )

@section('content')
  <aside class="shadow">
    <div class="changeLang d-flex align-items-center justify-content-start mb-3 mb-md-5">
      @if(App::isLocale('en'))
        <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي" class="d-block">عربي</a>
      @else
        <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
      @endif
    </div><!-- changeLang -->
    <div class="title d-block text-body text-center mb-3 fw-bold">{{ __('Reset Password') }}</div>
    <div class="desc text-center text-body mb-3">
      {{ __('Please use this form to register.') }}
      <br>
      {{ __('If you are a member, please') }} <a href="{{ route('login') }}" title="{{ __('Login') }}">{{ __('Login') }}</a>.
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
      <h1 class="d-block mb-3 fw-normal text-body">{{ __('Reset Password') }}</h1>
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div><!-- alert -->
      @endif
      <form method="POST" action="{{ route('password.update') }}" id="form">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">
        <label for="email"  class="form-group has-float-label mb-4">
          <input  id="email" type="email" class="form-control @error('email') is-invalid @enderror" inputmode="email" value="{{ $email ?? old('email') }}" autofocus disabled="" />
          <span>{{ __('E-Mail Address') }}</span>
          <input type="hidden" name="email"  value="{{ $email ?? old('email') }}">
        </label>
        @error('email')
          <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
          </span>
        @enderror
        <label for="password" class="form-group has-float-label mb-4">
          <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" autocomplete="new-password"/>
          <span>{{ __('Password') }}</span>
        </label>
        @error('password')
          <p class="invalid-feedback" role="alert">{{ $message }}</p>
        @enderror
        <label for="password-confirm" class="form-group has-float-label mb-4">
          <input id="password-confirm" type="password" class="form-control" name="password_confirmation" autocomplete="new-password" />
          <span>{{ __('Confirm Password') }}</span>
        </label>
        <div class="d-flex justify-content-end align-items-center">
          <button class="btn btn-primary btn-lg btn-shadow login_button" type="submit">{{ __('Reset Password') }}</button>
        </div>
      </form>
    </div><!-- topArea -->
  </article>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest', '#form') !!}
@endpush
