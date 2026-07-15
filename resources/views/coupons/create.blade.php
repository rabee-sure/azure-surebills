@extends('layouts.app')

@section('title', __('Create Coupon'))

<<<<<<< HEAD
@section('content')

  <h4 class="mb-1">{{ __('Create Coupon')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('/coupons') }}" title="{{ __('Coupons') }}">{{ __('Coupons') }}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Create Coupon') }}</li>
    </ol>
  </nav>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  <form method="POST" action="{{ route('coupons.store') }}" id="coupon_form" class="card">
    <div class="card-body">
      @csrf
      @include('coupons.partials.form', ['coupon' => null, 'mechanisms' => $mechanisms])
    </div><!-- card-body -->
    <div class="card-footer d-flex align-items-center justify-content-end gap-3">
      <button type="submit" class="btn btn-primary btn-submit-with-spinner waves-effect waves-light" data-loading-text="{{__('Saving...')}}">
        <span class="btn-spinner d-none me-2" role="status">
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        </span>
        <span class="btn-text">{{__('Save')}}</span>
      </button>
      <a href="{{ route('coupons.index') }}" class="btn btn-label-secondary waves-effect">{{ __('Cancel') }}</a>
    </div><!-- card-footer -->
  </form>

@endsection
=======
@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ route('coupons.index')}}" title="{{ __('Coupons') }}">{{ __('Coupons') }}</a>
    <i>/</i>
    <span>{{ __('Create Coupon')}}</span>
  </div><!-- breadcrump -->

  <section id="couponsCreatePage">

    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Create Coupon')}}</h1>
    </div><!-- title -->

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul class="mb-0">
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="couponFormArea bg-white shadow-sm rounded-3 p-4 mb-3">
      <form method="POST" action="{{ route('coupons.store') }}" id="coupon_form">
        @csrf

        @include('coupons.partials.form', ['coupon' => null, 'mechanisms' => $mechanisms])

        <div class="saveBtn d-flex justify-content-start gap-3 mt-5">
            <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold">{{ __('Create Coupon') }}</button>
            <a href="{{ route('coupons.index') }}" class="btn btn-light rounded-3">{{ __('Cancel') }}</a>
          </div>
      </form>
    </div><!-- couponFormArea -->

  </section><!-- couponsCreatePage -->

@endsection
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
