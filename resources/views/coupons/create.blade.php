@extends('layouts.app')

@section('title', __('Create Coupon'))

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
