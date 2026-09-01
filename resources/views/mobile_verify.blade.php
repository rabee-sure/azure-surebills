@extends('layouts.auth')

@section('title', __('verify your phone number') )

@section('content')

  <h4 class="mb-1">{{ __('verify your phone number') }}</h4>
  <p class="text-start mb-6">{{ __('we sent You SMS Message Contain Apin Code') }}</p>
  <h1 class="d-block mb-3 fw-normal text-body text-center" dir="ltr">{{ $user->mobile }}</h1>

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

      @if ($errors->has('message'))
        <div class="alert alert-warning w-100 mx-auto mb-3" role="alert">
          <i class="fas fa-exclamation-triangle"></i>
          {{ $errors->first('message') }}
        </div>
      @endif
      <p class="mb-0">{{ __('Enter 4-digit OTP') }}</p>

      <div class="row justify-content-center">
        <div class="col-12 col-md-9">

          <form id="twoStepsForm" method="POST" action="{{ route('post.mobile_verify') }}">
            @csrf
            <div class="mb-4 form-control-validation">
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
              </div>
              <input type="hidden" name="otp" id="otp" value="{{ old('otp') }}" />
              @error('otp')
                <div class="invalid-feedback d-block text-danger" role="alert">{{ $message }}</div>
              @enderror
            </div>
            <button id="otp-verify-btn" class="btn btn-primary d-grid w-100 mb-6" type="submit" disabled>{{ __('Verify') }}</button>
          </form>

        </div>
      </div>

  <form method="POST" action="{{ route('resend_code') }}" id="resend-otp-form" class="text-center">
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

@endsection

@push('footer-scripts')
    <script src="{{ asset('assets/v2/js/pages-auth.js') }}"></script>
    <script src="{{ asset('assets/v2/js/pages-auth-two-steps.js') }}"></script>
    @include('auth.partials.otp-form-scripts')
@endpush
