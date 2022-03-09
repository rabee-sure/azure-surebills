@extends('layouts.app')

@section('title', __('My Account'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/smart_wizard.min.css') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('My Account') }}</span>
  </div><!-- breadcrump -->

  <section id="accountIndexPage">

    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0">{{__('My Account')}}</h1>
    </div><!-- title -->

    @if(!auth()->user()->is_uploaded_business_documents)
      <div role="alert" class="alert mainAlert d-flex align-items-center justify-content-start alert-danger mb-4 w-100">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('No business information sent') }}
      </div><!-- alert -->
    @elseif(!auth()->user()->is_uploaded_bank_documents)
      <div role="alert" class="alert mainAlert d-flex align-items-center justify-content-start alert-danger mb-4 w-100">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('Bank account information has not been sent') }}
      </div><!-- alert -->
    @endif

    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4">
      <div class="col">
        <a href="{{ route('account_information') }}" title="{{ __('My Information') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
          <i class="fal fa-address-card"></i>
          <span class="d-block mt-3 text-center">{{ __('My Information') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('business_information') }}" title="{{ __('Business Information') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
          <i class="fal fa-briefcase"></i>
          <span class="d-block mt-3 text-center">{{ __('Business Information') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('bank_information') }}" title="{{ __('Bank Information') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
          <i class="fal fa-landmark"></i>
          <span class="d-block mt-3 text-center">{{ __('Bank Information') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('change_password') }}" title="{{ __('Change Password') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
          <i class="fal fa-lock-alt"></i>
          <span class="d-block mt-3 text-center">{{ __('Change Password') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('settings') }}" title="{{__('Settings')}}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
          <i class="fal fa-cogs"></i>
          <span class="d-block mt-3 text-center">{{__('Settings')}}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
          <i class="fal fa-boxes"></i>
          <span class="d-block mt-3 text-center">{{__('Products')}}</span>
        </a>
      </div><!-- col -->
    </div><!-- row -->

    @yield('steps')
    
  </section><!-- accountIndexPage -->

@endsection

<script src="{{ asset('js/jquery.smartWizard.min.js') }}" defer></script>

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
