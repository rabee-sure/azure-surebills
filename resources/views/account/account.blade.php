@extends('layouts.app')

@section('title', __('Settings'))

@section('content')

  <h4 class="mb-6">{{__('Settings')}}</h4>

  @if(auth()->user()->source == 'sure bills')
    @if((!auth()->user()->is_uploaded_business_documents && !auth()->user()->mainStoreUser) || (auth()->user()->mainStoreUser && !auth()->user()->mainStoreUser->is_uploaded_business_documents && auth()->user()->hasPermissionTo('update business commercial info')))
      <div class="alert alert-danger d-flex align-items-center mb-6" role="alert">
        <span class="alert-icon rounded me-3">
          <i class="icon-base ti ti-alert-triangle icon-md"></i>
        </span>
        {{ __('No business information sent') }}
      </div>
    @elseif((!auth()->user()->is_uploaded_bank_documents && !auth()->user()->mainStoreUser) || (auth()->user()->mainStoreUser && !auth()->user()->mainStoreUser->is_uploaded_bank_documents && auth()->user()->hasPermissionTo('update bank info')))
      <div class="alert alert-danger d-flex align-items-center mb-6" role="alert">
        <span class="alert-icon rounded me-3">
          <i class="icon-base ti ti-alert-triangle icon-md"></i>
        </span>
        {{ __('Bank account information has not been sent') }}
      </div>
    @endif
  @endif

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if(session()->has('message'))
    <div class="alert alert-success d-flex align-items-center mb-6" role="alert">
      <span class="alert-icon rounded me-3">
        <i class="icon-base ti ti-check icon-md"></i>
      </span>
      {{ session()->get('message') }}
    </div>
  @endif

  <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4 g-6">
    <div class="col">
      <a href="{{ route('account_information') }}" title="{{ __('My Information') }}" class="card h-100">
        <div class="card-body d-flex align-items-center justify-content-start">
          <div class="badge rounded p-2 bg-label-primary me-3">
            <i class="icon-base ti ti-id icon-lg"></i>
          </div>
          <h6 class="card-title mb-0">{{ __('My Information') }}</h6>
        </div>
      </a>
    </div><!-- col -->
    @can('update business commercial info')
      <div class="col">
        <a href="{{ route('business_information') }}" title="{{ __('Business Information') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-briefcase icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{ __('Business Information') }}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endcan
    @can('update bank info')
      <div class="col">
        <a href="{{ route('bank_information') }}" title="{{ __('Bank Information') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-building-bank icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{ __('Bank Information') }}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endcan
    <div class="col">
      <a href="{{ route('change_password') }}" title="{{ __('Change Password') }}" class="card h-100">
        <div class="card-body d-flex align-items-center justify-content-start">
          <div class="badge rounded p-2 bg-label-primary me-3">
            <i class="icon-base ti ti-lock-password icon-lg"></i>
          </div>
          <h6 class="card-title mb-0">{{ __('Change Password') }}</h6>
        </div>
      </a>
    </div><!-- col -->
    @can('update settings')
      <div class="col">
        <a href="{{ route('settings') }}" title="{{ __('Invoice Settings') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-file-settings icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{ __('Invoice Settings') }}</h6>
          </div>
        </a>
      </div><!-- col -->
      <div class="col">
        <a href="{{ route('tax_invoice.request') }}" title="{{ __('Tax invoice request') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-receipt icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{__('Tax invoice request')}}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endcan
    {{--  @can('show products')
      <div class="col">
        <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-building-store icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{__('Products')}}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endcan --}}
    @can('show applications')
      <div class="col">
        <a href="{{ route('integration') }}" title="{{ __('Integration') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-cloud-network icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{__('Integration')}}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endcan
    @can('show channels')
      @if(count(auth()->user()->channels) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels)))
        <div class="col">
          <a href="{{ route('channels.index') }}" title="{{ __('Channels') }}" class="card h-100">
            <div class="card-body d-flex align-items-center justify-content-start">
              <div class="badge rounded p-2 bg-label-primary me-3">
                <i class="icon-base ti ti-affiliate icon-lg"></i>
              </div>
              <h6 class="card-title mb-0">{{__('Channels')}}</h6>
            </div>
          </a>
        </div><!-- col -->
<<<<<<< HEAD
      @endif
    @endcan
    @can('update settings')
      <div class="col">
        <a href="{{ route('coupons.index') }}" title="{{ __('Coupon Management') }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-ticket icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{__('Coupon Management')}}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endcan
  </div><!-- row -->
=======
      @endcan

      @can('update settings')
        <div class="col">
          <a href="{{ route('coupons.index') }}" title="{{__('Coupon Management')}}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-2 p-md-3">
            <i class="fal fa-ticket-alt"></i>
            <span class="d-block mt-3 text-center">{{__('Coupon Management')}}</span>
          </a>
        </div><!-- col -->
      @endcan

      @can('update settings')
        <div class="col">
          <a href="{{ route('tax_invoice.request') }}" title="{{__('Tax invoice request')}}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-2 p-md-3">
            <i class="fal fa-envelope"></i>
            <span class="d-block mt-3 text-center">{{__('Tax invoice request')}}</span>
          </a>
        </div><!-- col -->
      @endcan

      {{-- @can('show products')
        <div class="col">
          <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-2 p-md-3">
            <i class="fal fa-boxes"></i>
            <span class="d-block mt-3 text-center">{{__('Products')}}</span>
          </a>
        </div><!-- col -->
      @endcan --}}
      @can('show applications')
        <div class="col">
          <a href="{{ route('integration') }}" title="{{ __('Integration') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-2 p-md-3">
            <i class="fal fa-network-wired"></i>
            <span class="d-block mt-3 text-center">{{__('Integration')}}</span>
          </a>
        </div><!-- col -->
      @endcan
      @can('show channels')
        @if(count(auth()->user()->channels) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels)))
          <div class="col">
            <a href="{{ route('channels.index') }}" title="{{ __('Channels') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-2 p-md-3">
              <i class="fal fa-chart-network"></i>
              <span class="d-block mt-3 text-center">{{__('Channels')}}</span>
            </a>
          </div><!-- col -->
        @endif
      @endcan
      @can('show users')
        <div class="col">
          <a href="{{ route('users.index') }}" title="{{ __('Users') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-2 p-md-3">
            <i class="fal fa-users"></i>
            <span class="d-block mt-3 text-center">{{__('Users')}}</span>
          </a>
        </div><!-- col -->
      @endcan
    </div><!-- row -->

    @yield('steps')

  </section><!-- accountIndexPage -->
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

@endsection
