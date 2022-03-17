@extends('layouts.app')

@section('title', __('Change Password'))

@section('content')
  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Change Password') }}</span>
  </div><!-- breadcrump -->
  <section id="changePasswordPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Change Password')}}</h1>
    </div><!-- title -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
      <form id="form" method="POST" action="{{ route('change.password') }}">
        @csrf
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="password" class="d-block mb-2">{{ __('Current Password') }} <span class="requirement">*</span></label>
              <input id="password" type="password" name="current_password" autocomplete="current-password" class="form-control rounded-3 shadow-none border" placeholder="{{ __('Current Password') }}">
            </div>
            <div class="form-group mb-3">
              <label for="_confirmation" class="d-block mb-2">{{ __('New Password') }} <span class="requirement">*</span></label>
              <input id="new_password" type="password" class="form-control rounded-3 shadow-none border" name="new_password" autocomplete="current-password" placeholder="{{ __('New Password') }}">
            </div>
            <div class="form-group mb-3">
              <label for="new_password_confirmation" class="d-block mb-2">{{ __('Re-type New Password') }} <span class="requirement">*</span></label>
              <input id="new_password_confirmation" type="password" class="form-control rounded-3 shadow-none border" name="new_password_confirmation" autocomplete="current-password" placeholder="{{__('Re-type New Password') }}">
            </div>
          </div><!-- col-12 -->
        </div><!-- row -->
        <div class="saveBtn d-flex justify-content-start mt-3">
          <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold"> {{__('Save')}}</button>
        </div><!-- saveBtn -->
      </form>
    </div><!-- blockArea -->
  </section><!-- changePasswordPage -->
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\ChangePasswordRequest', '#form') !!}
@endpush