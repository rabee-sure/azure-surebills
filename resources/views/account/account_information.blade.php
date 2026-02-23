@extends('layouts.app')

@section('title', __('Account Information'))

@section('content')

  <h4 class="mb-1">{{ __('Account Information')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('account') }}" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Account Information') }}</li>
    </ol>
  </nav>

  <form id="form" method="POST" action="{{ route('account.information') }}" class="card">
    @csrf
    <div class="card-body">
      <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 g-4">
        <div class="col">
          <label class="form-label" for="name">{{ __('Full Name')}} <div class="text-danger d-inline-block">*</div></label>
          <input name="name" type="text" class="form-control" id="name" placeholder="{{ __('Full Name')}}" value="{{ $user->name }}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label class="form-label" for="email">{{ __('Email')}} <div class="text-danger d-inline-block">*</div></label>
          <input name="email" type="email" inputmode="email" class="form-control" id="email" placeholder="{{ __('Email')}}" value="{{ $user->email }}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label class="form-label" for="mobile">{{ __('Mobile Number')}}</label>
          <input value="{{ $user->mobile }}" name="mobile" type="tel" class="form-control" id="mobile" placeholder="{{ __('Mobile Number')}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric" disabled="disabled" autocomplete="off">
        </div><!-- col -->
          {{-- <div class="col">
            <label class="form-label" for="gender">{{ __('Gander')}}</label>
            <select name="gender" id="gender" class="form-control select2-single">
              <option value="0" @if ($user->gender == 0)selected="selected"@endif disabled>{{ __('Choose Gender')}}</option>
              <option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
              <option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
            </select>
          </div><!-- col --> --}}
      </div><!-- row -->
    </div><!-- card-body -->
    <div class="card-footer d-flex align-items-center justify-content-end">
      <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
    </div><!-- card-footer -->
  </form><!-- card -->

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
