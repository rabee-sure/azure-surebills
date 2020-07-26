@extends('layouts.app')

@section('title', __('My Account'))

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>{{ __('My Account') }}</h1>
			<div class="separator mb-5"></div>
		</div>
    <div class="col-12">
      <div class="row icon-cards-row mx-n3">
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('account_information') }}" title="{{ __('My Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-id-card"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('My Information') }}</p>
            </div>
          </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('business_information') }}" title="{{ __('Business Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-management"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Business Information') }}</p>
            </div>
          </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('bank_information') }}" title="{{ __('Bank Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-bank"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Bank Information') }}</p>
            </div>
          </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('change_password') }}" title="{{ __('Change Password') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-type-pass"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Change Password') }}</p>
            </div>
          </a>
        </div>
      </div>
    </div>
	</div>
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endsection
