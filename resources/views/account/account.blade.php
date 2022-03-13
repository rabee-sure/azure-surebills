@extends('layouts.app')

@section('title', __('Settings'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/smart_wizard.min.css') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Settings') }}</span>
  </div><!-- breadcrump -->

  <section id="accountIndexPage">

    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Settings')}}</h1>
    </div><!-- title -->

    @if((!auth()->user()->is_uploaded_business_documents && !auth()->user()->store_main_user_id) || (auth()->user()->mainStoreUser && auth()->user()->hasPermissionTo('update business commercial info')))
      <div role="alert" class="alert mainAlert d-flex align-items-center justify-content-start alert-danger mb-4 w-100">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('No business information sent') }}
      </div><!-- alert -->
    @elseif((!auth()->user()->is_uploaded_bank_documents && !auth()->user()->store_main_user_id) || (auth()->user()->mainStoreUser && auth()->user()->hasPermissionTo('update bank info')))
      <div role="alert" class="alert mainAlert d-flex align-items-center justify-content-start alert-danger mb-4 w-100">
        <i class="fas fa-exclamation-triangle"></i>
        {{ __('Bank account information has not been sent') }}
      </div><!-- alert -->
    @endif

    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4">
      <div class="col">
        <a href="{{ route('account_information') }}" title="{{ __('My Information') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-address-card"></i>
          <span class="d-block mt-3 text-center">{{ __('My Information') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('business_information') }}" title="{{ __('Business Information') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-briefcase"></i>
          <span class="d-block mt-3 text-center">{{ __('Business Information') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('bank_information') }}" title="{{ __('Bank Information') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-landmark"></i>
          <span class="d-block mt-3 text-center">{{ __('Bank Information') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('change_password') }}" title="{{ __('Change Password') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-lock-alt"></i>
          <span class="d-block mt-3 text-center">{{ __('Change Password') }}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('settings') }}" title="{{__('Invoice Settings')}}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-cogs"></i>
          <span class="d-block mt-3 text-center">{{__('Invoice Settings')}}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-boxes"></i>
          <span class="d-block mt-3 text-center">{{__('Products')}}</span>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('integration') }}" title="{{ __('Integration') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
          <i class="fal fa-network-wired"></i>
          <span class="d-block mt-3 text-center">{{__('Integration')}}</span>
        </a>
      </div><!-- col -->
      @if(count(auth()->user()->channels))
        <div class="col">
          <a href="{{ route('channels.index') }}" title="{{ __('Channels') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3">
            <i class="fal fa-chart-network"></i>
            <span class="d-block mt-3 text-center">{{__('Channels')}}</span>
          </a>
<<<<<<< HEAD
        </div><!-- col -->
      @endif
    </div><!-- row -->
=======
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
>>>>>>> dev

    @yield('steps')
    
  </section><!-- accountIndexPage -->

@endsection

<script src="{{ asset('js/jquery.smartWizard.min.js') }}" defer></script>

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
