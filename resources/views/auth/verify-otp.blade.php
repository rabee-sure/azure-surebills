@extends('layouts.auth')

@section('title', __('Verify OTP') )

@section('content')

  <h4 class="mb-1">{{ __('Secure Login') }}</h4>
  <p class="text-start mb-6">
    @if(config('app.otp_channel', 'email') == 'email')
      {{ __('We have sent a verification code to your registered email address.') }}
    @elseif(config('app.otp_channel', 'email') == 'sms')
      {{ __('We have sent a verification code to your registered mobile number.') }}
    @elseif(config('app.otp_channel', 'email') == 'both')
      {{ __('We have sent a verification code to your registered email address and mobile number.') }}
    @endif
    <br>
    {{ __('Please enter the code to complete your login.') }}
  </p>
  <h1 class="d-block mb-3 fw-normal text-body"></h1>

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
  <p class="mb-0">{{ __('Enter 6-digit OTP') }}</p>
  <form id="twoStepsForm" method="POST" action="{{ route('otp.verify') }}">
    @csrf
    <div class="mb-6 form-control-validation">
      <div dir="ltr" class="auth-input-wrapper d-flex align-items-center justify-content-between numeral-mask-wrapper">
        <input
          type="tel"
          class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
          maxlength="1"
          autofocus />
        <input
          type="tel"
          class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
          maxlength="1" />
        <input
          type="tel"
          class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
          maxlength="1" />
        <input
          type="tel"
          class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
          maxlength="1" />
        <input
          type="tel"
          class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
          maxlength="1" />
        <input
          type="tel"
          class="form-control auth-input h-px-50 text-center numeral-mask mx-sm-1 my-2"
          maxlength="1" />
      </div>
      <!-- Hidden field: filled by JS from the 6 inputs above -->
      <input type="hidden" name="otp" id="otp" value="{{ old('otp') }}" />
      @error('otp')
        <div class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>
    <button class="btn btn-primary d-grid w-100 mb-6" type="submit">{{ __('Verify') }}</button>
  </form>

  <form method="POST" action="{{ route('otp.resend') }}" id="resend-otp-form" class="text-center">
    @csrf
    {{ __("Didn't receive the code?") }}
    <button type="submit" class="btn btn-link p-1"> {{ __('Resend OTP') }} </button>
  </form>

  <div class="divider my-6">
                <div class="divider-text">or</div>
              </div>

  <a href="{{ route('login') }}" title="{{ __('Back to Login') }}" class="text-center d-block">{{ __('Back to Login') }}</a>

@endsection

@push('footer-scripts')
    <script src="{{ asset('assets/v2/js/pages-auth.js') }}"></script>
    <script src="{{ asset('assets/v2/js/pages-auth-two-steps.js') }}"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      var form = document.getElementById('twoStepsForm');
      var hiddenOtp = document.getElementById('otp');
      var inputs = document.querySelectorAll('.numeral-mask-wrapper .numeral-mask');

      // Pre-fill from old('otp') on validation error
      var oldOtp = hiddenOtp && hiddenOtp.value ? String(hiddenOtp.value).replace(/\D/g, '').slice(0, 6) : '';
      if (oldOtp && inputs.length === 6) {
        for (var i = 0; i < oldOtp.length; i++) {
          inputs[i].value = oldOtp[i];
        }
      }

      // Only allow digits in the 6 inputs
      inputs.forEach(function(inp) {
        inp.addEventListener('keypress', function(e) {
          if (!/^\d$/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
            e.preventDefault();
          }
        });
        inp.addEventListener('input', function() {
          this.value = this.value.replace(/\D/g, '').slice(0, 1);
        });
      });

      // Auto-submit when 6 digits are in (after two-steps.js fills the hidden field)
      var wrapper = document.querySelector('.numeral-mask-wrapper');
      if (wrapper && form) {
        wrapper.addEventListener('keyup', function() {
          if (hiddenOtp && hiddenOtp.value.length === 6) {
            form.submit();
          }
        });
        // Paste: split 6 digits into boxes and update hidden
        wrapper.addEventListener('paste', function(e) {
          e.preventDefault();
          var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, 6);
          for (var j = 0; j < pasted.length && j < inputs.length; j++) {
            inputs[j].value = pasted[j];
          }
          if (hiddenOtp) {
            hiddenOtp.value = pasted;
            if (pasted.length === 6) form.submit();
          }
        });
      }
    });
  </script>
@endpush
