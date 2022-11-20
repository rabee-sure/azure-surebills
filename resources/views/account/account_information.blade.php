@extends('layouts.app')

@section('title', __('Account Information'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Account Information') }}</span>
  </div><!-- breadcrump -->

  <section id="accountInformationPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Account Information')}}</h1>
    </div><!-- title -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
      <form id="form" method="POST" action="{{ route('account.information') }}">
        @csrf
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="name" class="d-block mb-2">{{ __('Full Name')}} <span class="requirement text-danger">*</span></label>
              <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="name" placeholder="{{ __('Full Name')}}" value="{{ $user->name }}">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="email" class="d-block mb-2">{{ __('Email')}} <span class="requirement text-danger">*</span></label>
              <input name="email" type="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="email" placeholder="{{ __('Email')}}" value="{{ $user->email }}" >
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="mobile" class="d-block mb-2">{{ __('Mobile Number')}}</label>
              <div class="phoneInput overflow-hidden position-relative bg-light">
                <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
                <input value="{{ $user->mobile }}" name="mobile" type="tel" class="form-control shadow-none border w-100 rounded-3 text-body" id="mobile" placeholder="{{ __('Mobile Number')}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric" disabled="">
              </div><!-- phoneInput -->
            </div><!-- form-group -->
          </div><!-- col-12 -->
          {{-- <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="gender" class="d-block mb-2">{{ __('Gander')}}</label>
              <select name="gender" id="gender" class="form-control shadow-none bg-white border w-100 rounded-3 text-body select2-single">
                <option value="0" @if ($user->gender == 0)selected="selected"@endif disabled>{{ __('Choose Gender')}}</option>
                <option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
                <option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
              </select>
            </div><!-- form-group -->
          </div><!-- col-12 --> --}}
        </div><!-- row -->
        <div class="saveBtn d-flex justify-content-start mt-3">
          <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold"> {{__('Save')}}</button>
        </div><!-- saveBtn -->
      </form>
    </div><!-- blockArea -->
  </section><!-- accountInformationPage -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
@endpush

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush