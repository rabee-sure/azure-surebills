@extends('layouts.auth')

@section('title', __('Verify OTP') )

@section('content')
  <aside class="shadow align-self-stretch">
    <div class="changeLang d-flex align-items-center justify-content-start mb-3 mb-md-5">
      @if(App::isLocale('en'))
        <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي" class="d-block">عربي</a>
      @else
        <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
      @endif
    </div><!-- changeLang -->
    <div class="title d-block text-body text-center mb-3 fw-bold">{{ __('Secure Login') }}</div>
    <div class="desc text-center text-body mb-3">
      @if(config('app.otp_channel', 'email') == 'email')
        {{ __('We have sent a verification code to your registered email address.') }}
      @elseif(config('app.otp_channel', 'email') == 'sms')
        {{ __('We have sent a verification code to your registered mobile number.') }}
      @elseif(config('app.otp_channel', 'email') == 'both')
        {{ __('We have sent a verification code to your registered email address and mobile number.') }}
      @endif
      <br>
      {{ __('Please enter the code to complete your login.') }}
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
      <h1 class="d-block mb-3 fw-normal text-body">{{ __('Verify OTP') }}</h1>

      @if (session('status'))
        <div class="alert alert-success w-100 mx-auto mb-3" role="alert">
          <i class="fas fa-check-circle"></i> {{ session('status') }}
        </div>
      @endif

      @if (session('error'))
        <div class="alert alert-danger w-100 mx-auto mb-3" role="alert">
          <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        </div>
      @endif

      @if ($errors->has('email') || $errors->has('message'))
        <div class="alert alert-warning w-100 mx-auto mb-3" role="alert">
          <i class="fas fa-exclamation-triangle"></i> 
          @if ($errors->has('email'))
            {{ $errors->first('email') }}
          @elseif ($errors->has('message'))
            {{ $errors->first('message') }}
          @endif
        </div>
      @endif

      <form method="POST" action="{{ route('otp.verify') }}" id="verify-otp-form" class="w-100 mx-auto">
        @csrf
        <div class="form_group mb-3">
          <div class="inputIcon d-flex align-items-center justify-content-center rounded overflow-hidden border @error('otp') is-invalid @enderror">
            <span class="d-flex align-items-center justify-content-center h-100 fal fa-key"></span>
            <input id="otp" type="text" class="bg-white border-0 h-100 flex-grow-1 text-body" name="otp" inputmode="numeric" value="{{ old('otp') }}" autocomplete="off" placeholder="{{ __('Enter 6-digit OTP') }}" maxlength="6" pattern="[0-9]{6}" autofocus />
          </div><!-- inputIcon -->
          @error('otp')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </div><!-- form_group -->

        <div class="d-flex align-items-center justify-content-center mb-3">
          <button class="login_button rounded border-0 fw-bold d-flex align-items-center justify-content-center text-white p-0" type="submit">{{ __('Verify') }}</button>
        </div><!-- d-flex -->
      </form>

      <form method="POST" action="{{ route('otp.resend') }}" id="resend-otp-form" class="w-100 mx-auto">
        @csrf
        <div class="d-flex align-items-center justify-content-center">
          <span class="text-body">{{ __("Didn't receive the code?") }}</span>
          <button type="submit" class="btn btn-link p-1">{{ __('Resend OTP') }}</button>
        </div>
      </form>
    </div><!-- topArea -->
    <div class="bottotmArea d-flex align-items-center justify-content-center flex-column border-top w-100">
      <a href="{{ route('login') }}" title="{{ __('Back to Login') }}" class="d-block">
        <i class="fal fa-arrow-left"></i> {{ __('Back to Login') }}
      </a>
    </div><!-- bottotmArea -->
  </article>

  <style>
    #otp {
        letter-spacing: .5rem;
        font-size: 1.2rem;
        text-align: center;
    }
    #otp::placeholder {
        letter-spacing: normal;
        font-size: 1rem;
    }
  </style>
@endsection

@push('footer-scripts')
  <script>
    // Auto-submit form when 6 digits are entered
    document.getElementById('otp').addEventListener('input', function(e) {
      if (this.value.length === 6) {
        document.getElementById('verify-otp-form').submit();
      }
    });

    // Only allow numeric input
    document.getElementById('otp').addEventListener('keypress', function(e) {
      if (!/[0-9]/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
        e.preventDefault();
      }
    });
  </script>
@endpush
