@extends('layouts.auth')

@section('title', __('Register') )

@section('content')

  <h4 class="mb-1">{{ __('Register a new account') }}</h4>
  <p class="mb-6">{{ __('Please use this form to register.') }}</p>

  <form method="POST" action="{{ route('register') }}" id="register-form" class="mb-6" novalidate>
    @csrf

    <div class="mb-6 form-control-validation @error('business_name_en') is-invalid @enderror">
      <label for="business_name_en" class="form-label mb-2">{{ __('Business Name') }}</label>
      <div class="input-group">
        <input
          type="text"
          class="form-control @error('business_name_en') is-invalid @enderror"
          id="business_name_en"
          name="business_name_en"
          value="{{ old('business_name_en') }}"
          placeholder="{{ __('Business Name') }}"
          autocomplete="off"
          autofocus
          aria-label="{{ __('Business Name') }}"
        />
      </div>
      @error('business_name_en')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-6 form-control-validation @error('name') is-invalid @enderror">
      <label for="name" class="form-label mb-2">{{ __('Full Name') }}</label>
      <div class="input-group">
        <input
          type="text"
          class="form-control @error('name') is-invalid @enderror"
          id="name"
          name="name"
          value="{{ old('name') }}"
          placeholder="{{ __('Full Name') }}"
          autocomplete="off"
          autofocus
          aria-label="{{ __('Full Name') }}"
        />
      </div>
      @error('name')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-6 form-control-validation @error('email') is-invalid @enderror">
      <label for="email" class="form-label mb-2">{{ __('Email') }}</label>
      <div class="input-group">
        <input
          type="email"
          inputmode="email"
          class="form-control @error('email') is-invalid @enderror"
          id="email"
          name="email"
          value="{{ old('email') }}"
          placeholder="{{ __('Email') }}"
          autocomplete="off"
          autofocus
          aria-label="{{ __('Email') }}"
        />
      </div>
      @error('email')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <div class="mb-6 form-control-validation @error('mobile') is-invalid @enderror">
      <label for="mobile" class="form-label mb-2">{{ __('Mobile') }}</label>
      <div class="input-group">
        <input
          type="tel"
          inputmode="numeric"
          class="form-control @error('mobile') is-invalid @enderror text-start"
          id="mobile"
          name="mobile"
          value="{{ old('mobile') }}"
          autocomplete="off"
          autofocus
          aria-label="{{ __('Mobile') }}"
          aria-describedby="basic-addon-Mobile"
          pattern="[0-9]*"
          maxlength="9"
          placeholder="5XXXXXXXX"
        />
      </div>
      @error('mobile')
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

    <div class="mb-6">
      <div class="d-flex justify-content-between">
        <div class="form-check mb-0">
          <input type="checkbox" class="form-check-input @error('terms') is-invalid @enderror" name="terms" id="customCheckThis" value="1" />
          <label class="form-check-label" for="remember">{{ __('I agree to') }} <a id="read_terms" href="#" title="{{ __('Terms & Conditions') }}" data-bs-toggle="modal" data-bs-target="#conditionsModal">{{ __('Terms & Conditions') }}</a></label>
        </div>
      </div>
      @error('terms')
        <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
      @enderror
    </div>

    <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Register') }}</button>
  </form>

  <p class="text-center">
    <span>{{ __('If you are a member, please') }}</span>
    <a href="{{ route('login') }}" title="{{ __('Login') }}">
      <span>{{ __('Login') }}</span>
    </a>
  </p>

@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ForgotPasswordRequest', '#form') !!}
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
