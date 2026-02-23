@extends('layouts.app')

@section('title', __('Change Password'))

@section('content')

  <h4 class="mb-1">{{ __('Change Password')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('account') }}" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Change Password') }}</li>
    </ol>
  </nav>

  <form id="form" method="POST" action="{{ route('change.password') }}" class="card">
    @csrf
    <div class="card-body">
      <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 g-6">
        <div class="col">
          <label for="password" class="form-label">{{ __('Current Password') }} <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge custom-form-password-toggle">
            <input
              id="password"
              name="current_password"
              autocomplete="off"
              type="password"
              class="form-control"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              aria-describedby="password"
              />
            <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
          </div>
        </div>
        <div class="col">
          <label for="new_password" class="form-label">{{ __('New Password') }} <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge custom-form-password-toggle">
            <input
              id="new_password"
              name="new_password"
              autocomplete="off"
              type="password"
              class="form-control"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              aria-describedby="new_password"
              />
            <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
          </div>
        </div>
        <div class="col">
          <label for="new_password_confirmation" class="form-label">{{ __('Re-type New Password') }} <span class="text-danger">*</span></label>
          <div class="input-group input-group-merge custom-form-password-toggle">
            <input
              id="new_password_confirmation"
              name="new_password_confirmation"
              autocomplete="off"
              type="password"
              class="form-control"
              placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
              aria-describedby="new_password_confirmation"
              />
            <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
          </div>
        </div><!-- col -->
      </div><!-- row -->
    </div><!-- card-body -->
    <div class="card-footer d-flex align-items-center justify-content-end">
      <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
    </div><!-- card-footer -->
  </form>

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordRequest', '#form') !!}
  <script>
    // Password Toggle
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
