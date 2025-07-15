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
      <form method="POST" action="{{ route('password.update') }}" id="form" class="w-100 mx-auto">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form_group mb-3">
          <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('email') is-invalid @enderror">
            <span class="d-flex align-items-center justify-content-center h-100 fal fa-envelope"></span>
            <input id="email" type="email" class=" border-0 h-100 flex-grow-1 text-body" name="email" inputmode="email" autocomplete="off" value="{{ $email ?? old('email') }}" autofocus disabled="" />
          </div><!-- inputIcon -->
          @error('email')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </div><!-- form_group -->


        <div class="form_group mb-3">
          <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('password') is-invalid @enderror">
            <span class="d-flex align-items-center justify-content-center h-100 fal fa-lock-alt"></span>
            <input id="password" class="bg-white border-0 h-100 flex-grow-1 text-body" name="password" autocomplete="off" type="password" placeholder="{{ __('Password') }}" />
          </div><!-- inputIcon -->
          @error('password')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </div><!-- form_group -->


        <div class="form_group mb-3">
          <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('password') is-invalid @enderror">
            <span class="d-flex align-items-center justify-content-center h-100 fal fa-lock-alt"></span>
            <input id="password-confirm" class="bg-white border-0 h-100 flex-grow-1 text-body" name="password_confirmation" autocomplete="off" type="password" placeholder="{{ __('Confirm Password') }}" />
          </div><!-- inputIcon -->
        </div><!-- form_group -->

          <button class="login_button rounded border-0 fw-bold d-flex align-items-center justify-content-center text-white p-0 w-100" type="submit">{{ __('Reset Password') }}</button>

      </form>
    </div><!-- topArea -->
  </article>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest', '#form') !!}
@endpush
