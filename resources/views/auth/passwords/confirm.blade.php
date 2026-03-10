@extends('layouts.auth')

@section('title', __('Confirm Password') )

@section('content')

  <h4 class="mb-1">{{ __('Confirm Password') }}</h4>
  <p class="mb-6">{{ __('Please confirm your password before continuing.') }}</p>

  <form class="mb-4" method="POST" action="{{ route('password.confirm') }}">
    @csrf
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

    <div class="mb-6">
      <div class="d-flex justify-content-end">
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" title="{{ __('Forgot Your Password?') }}">
            <p class="mb-0">{{ __('Forgot Your Password?') }}</p>
          </a>
        @endif
      </div>
    </div>

    <div class="mb-6">
      <button class="btn btn-primary d-grid w-100" type="submit">{{ __('Confirm Password') }}</button>
    </div>
  </form>

@endsection

@push('footer-scripts')
  <script>
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
