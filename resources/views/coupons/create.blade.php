@extends('layouts.app')

@section('title', __('Create Coupon'))

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
