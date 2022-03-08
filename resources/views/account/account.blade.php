@extends('layouts.app')

@section('title', __('My Account'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/smart_wizard.min.css') }}">
@endsection

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>{{ __('My Account') }}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{__('My Account')}}</li>
        </ol>
      </nav>
			<div class="separator mb-5"></div>
		</div>
    <svg xmlns="http://www.w3.org/2000/svg" style="display: none;">
      <symbol id="exclamation-triangle-fill" fill="currentColor" viewBox="0 0 16 16">
        <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
      </symbol>
    </svg>
    @if(!auth()->user()->is_uploaded_business_documents)
        <div role="alert" class="alert alert-danger mb-5 w-100">
          <svg class="bi flex-shrink-0 me-2" width="24" height="24" role="img" aria-label="Danger:"><use xlink:href="#exclamation-triangle-fill"/></svg>
          {{ __('No business information sent') }}
        </div>
    @elseif(!auth()->user()->is_uploaded_bank_documents)
        <div role="alert" class="alert alert-danger mb-5 w-100">
          {{ __('Bank account information has not been sent') }}
        </div>
    @endif

    <div class="col-12">
      <div class="row icon-cards-row mx-n3">
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('account_information') }}" title="{{ __('My Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-id-card"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('My Information') }}</p>
            </div>
          </a>
        </div>

        @can('update business commercial info')
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('business_information') }}" title="{{ __('Business Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-management"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Business Information') }}</p>
            </div>
          </a>
        </div>
        @endcan

        @can('update bank info')
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('bank_information') }}" title="{{ __('Bank Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-bank"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Bank Information') }}</p>
            </div>
          </a>
        </div>
        @endcan

        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('change_password') }}" title="{{ __('Change Password') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-type-pass"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Change Password') }}</p>
            </div>
          </a>
        </div>

        @can('update settings')
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('settings') }}" title="{{__('Settings')}}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon glyph-icon simple-icon-settings"></div>
              <p class="card-text font-weight-semibold mb-0">{{__('Settings')}}</p>
            </div>
          </a>
        </div><!-- col-12 -->
        @endcan

        @can('show products')
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon glyph-icon iconsminds-project"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Products') }}</p>
            </div>
          </a>
        </div><!-- col-12 -->
        @endcan

        @can('show users')
        <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
            <a href="{{ route('users.index') }}" title="{{ __('Users') }}" class="card mb-4">
              <div class="card-body text-center">
                <div class="statistic_icon glyph-icon simple-icon-people"></div>
                <p class="card-text font-weight-semibold mb-0">{{ __('Users') }}</p>
              </div>
            </a>
          </div><!-- col-12 -->
          @endcan
      </div>
    </div>

      @yield('steps')

	</div>
@endsection

  <script src="{{ asset('js/jquery.smartWizard.min.js') }}" defer></script>


@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
