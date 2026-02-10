@extends('layouts.auth')

@section('title', __('Login') )

@section('content')

  <h4 class="mb-1">{{ __('Start Sending Bills') }}</h4>
  <p class="mb-6">{{ __('Please use your credentials to login.') }}</p>

  <form id="login-form" class="mb-4" method="POST" action="{{ route('login') }}">
    @csrf
    <div class="mb-6 @error('email') is-invalid @enderror">
      <label for="email" class="form-label">{{ __('E-Mail Address') }}</label>
      <input
        id="email"
        type="email"
        class="form-control @error('email') is-invalid @enderror"
        name="email"
        inputmode="email"
        value="{{ old('email') }}"
        autocomplete="off"
        placeholder="{{ __('E-Mail Address') }}"
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
    <div class="my-8">
      <div class="d-flex justify-content-between">
        <div class="form-check mb-0 ms-2">
          <input type="checkbox" class="form-check-input" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }} />
          <label class="form-check-label" for="remember-me">{{ __('Remember Me') }}</label>
        </div>
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" title="{{ __('Forgot Your Password?') }}">
            <p class="mb-0">{{ __('Forgot Your Password?') }}</p>
          </a>
        @endif
      </div>
    </div>
    <div class="mb-6">
      <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Login') }}</button>
    </div>
  </form>

  <p class="text-center">
    <span>{{ __('If you are not a member') }} ,</span>
    <a href="{{ route('register') }}" title="{{ __('Register a new account2') }}">
      <span>{{ __('Register a new account2') }}</span>
    </a>
  </p>

@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\LoginRequest', '#login-form') !!}
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
