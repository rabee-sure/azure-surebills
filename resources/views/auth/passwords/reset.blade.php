@extends('layouts.auth')
@section('title', __('Reset Password') )

@section('content')

  <h4 class="mb-1">{{ __('Reset Password') }}</h4>
  <p class="mb-6">{{ __('Please use this form to reset your password.') }}</p>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  <form class="mb-4" method="POST" action="{{ route('password.update') }}" id="form">
    @csrf
    <input type="hidden" name="token" value="{{ $token }}">
    <div class="mb-6">
      <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
      <input
        id="email"
        type="email"
        class="form-control"
        name="email"
        inputmode="email"
        value="{{ $email ?? old('email') }}"
        autocomplete="off"
        placeholder="{{ __('E-Mail Address') }}"
        readonly
        disabled
        autofocus
      />
      @error('email')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-6 custom-form-password-toggle @error('password') is-invalid @enderror">
      <label class="form-label" for="password">{{ __('Password') }}</label>
      <div class="input-group input-group-merge">
        <input
          id="password"
          name="password"
          autocomplete="off"
          type="password"
          class="form-control @error('password') is-invalid @enderror"
          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
          aria-describedby="password"
          />
        <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
      </div>
      @error('password')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-6 custom-form-password-toggle @error('password') is-invalid @enderror">
      <label class="form-label" for="password-confirm">{{ __('Confirm Password') }}</label>
      <div class="input-group input-group-merge">
        <input
          id="password-confirm"
          name="password_confirmation"
          autocomplete="off"
          type="password"
          class="form-control @error('password') is-invalid @enderror"
          placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
          aria-describedby="password-confirm"
          />
        <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
      </div>
      @error('password')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Reset Password') }}</button>

  </form>

  <p class="text-center">
    <span>{{ __('If you are a member, please') }}</span>
    <a href="{{ route('login') }}" title="{{ __('Login') }}">
      <span>{{ __('Login') }}</span>
    </a>
  </p>

@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ResetPasswordRequest', '#form') !!}
  <script>
    // Pure JavaScript - Clean Version
    document.addEventListener('DOMContentLoaded', function() {
      initPasswordToggle();
    });

    function initPasswordToggle() {
      const togglers = document.querySelectorAll('.custom-form-password-toggle i');

      togglers.forEach(icon => {
        icon.addEventListener('click', function(e) {
          e.preventDefault();

          const container = this.closest('.custom-form-password-toggle');
          const input = container.querySelector('input');
          const toggleIcon = container.querySelector('i');

          if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.replace('ti-eye-off', 'ti-eye');
          } else {
            input.type = 'password';
            toggleIcon.classList.replace('ti-eye', 'ti-eye-off');
          }
        });
      });
    }
  </script>
@endpush
