@extends('layouts.auth')

@section('title', __('Verify OTP') )

@section('content')

  <h4 class="mb-1">{{ __('Secure Login') }}</h4>
  <p class="text-start mb-6">{{ __('Please enter the code to complete your login.') }}</p>
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
    <button id="otp-verify-btn" class="btn btn-primary d-grid w-100 mb-6" type="submit" disabled>{{ __('Verify') }}</button>
  </form>

  <form method="POST" action="{{ route('otp.resend') }}" id="resend-otp-form" class="text-center">
    @csrf
    {{ __("Didn't receive the code?") }}
    <button
      type="submit"
      id="resend-otp-btn"
      class="btn btn-link p-1"
      @disabled($resendRemainingSeconds > 0)
      data-remaining="{{ $resendRemainingSeconds }}"
      data-idle-label="{{ __('Resend OTP') }}"
      data-wait-label="{{ __('Resend OTP in :time') }}">
      @if ($resendRemainingSeconds > 0)
        {{ __('Resend OTP in :time', ['time' => sprintf('%02d:%02d', intdiv($resendRemainingSeconds, 60), $resendRemainingSeconds % 60)]) }}
      @else
        {{ __('Resend OTP') }}
      @endif
    </button>
  </form>

  <hr class="my-6">

  <a href="{{ route('login') }}" title="{{ __('Back to Login') }}" class="text-center d-block">{{ __('Back to Login') }}</a>

@endsection

@push('footer-scripts')
    <script src="{{ asset('assets/v2/js/pages-auth.js') }}"></script>
    <script src="{{ asset('assets/v2/js/pages-auth-two-steps.js') }}"></script>
    @include('auth.partials.otp-form-scripts')
@endpush
