@extends('layouts.app')

@section('title', __('Export Coupon Codes'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ route('coupons.index')}}" title="{{ __('Coupons') }}">{{ __('Coupons') }}</a>
    <i>/</i>
    <a href="{{ route('coupons.show', $coupon->id)}}" title="{{ __('Coupon Details') }}">{{ __('Coupon Details') }}</a>
    <i>/</i>
    <span>{{ __('Export Codes')}}</span>
  </div><!-- breadcrump -->

  <section id="couponsExportPage">

    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Export Coupon Codes')}}</h1>
      <small class="text-muted">{{ __('Coupon: :name', ['name' => $coupon->name]) }}</small>
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

    <div class="exportArea bg-white shadow-sm rounded-3 p-4 mb-3">
      <div class="alert alert-info mb-4">
        <strong>{{ __('Note:') }}</strong> {{ __('Export all generated codes for this coupon. The file will include code and usage status.') }}
      </div>

      @php
        $totalCodes = $coupon->codes->count();
        $usedCodes = $coupon->codes->where('is_used', true)->count();
        $availableCodes = $totalCodes - $usedCodes;
      @endphp

      <div class="row g-4 mb-4">
        <div class="col-md-4">
          <div class="card border-0 bg-light">
            <div class="card-body text-center">
              <h6 class="text-muted mb-2">{{ __('Total Codes') }}</h6>
              <h3 class="mb-0">{{ number_format($totalCodes) }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 bg-light">
            <div class="card-body text-center">
              <h6 class="text-muted mb-2">{{ __('Available') }}</h6>
              <h3 class="mb-0 text-success">{{ number_format($availableCodes) }}</h3>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card border-0 bg-light">
            <div class="card-body text-center">
              <h6 class="text-muted mb-2">{{ __('Used') }}</h6>
              <h3 class="mb-0 text-danger">{{ number_format($usedCodes) }}</h3>
            </div>
          </div>
        </div>
      </div>

      @if($totalCodes === 0)
        <div class="alert alert-warning">
          {{ __('No codes have been generated for this coupon yet. Please generate codes first.') }}
        </div>
        <div class="mt-4">
          <a href="{{ route('coupons.bulk-generate', $coupon->id) }}" class="btn btn-primary rounded-3">
            <i class="fal fa-plus-circle me-1"></i>{{ __('Generate Codes') }}
          </a>
          <a href="{{ route('coupons.show', $coupon->id) }}" class="btn btn-light ms-2 rounded-3">{{ __('Back') }}</a>
        </div>
      @else
        <form method="POST" action="{{ route('coupons.export', $coupon->id) }}" id="export_form">
          @csrf

          <div class="form-group mb-4">
            <label class="d-block mb-3">{{ __('Export Format') }} <span class="requirement text-danger">*</span></label>
            <div class="d-flex gap-3">
              <div class="form-check">
                <input class="form-check-input" type="radio" name="format" id="format_csv" value="csv" checked>
                <label class="form-check-label" for="format_csv">
                  <strong>CSV</strong><br>
                  <small class="text-muted">{{ __('Comma-separated values') }}</small>
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="radio" name="format" id="format_excel" value="excel">
                <label class="form-check-label" for="format_excel">
                  <strong>Excel</strong><br>
                  <small class="text-muted">{{ __('Microsoft Excel format (.xlsx)') }}</small>
                </label>
              </div>
            </div>
            @error('format')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
          </div>

          <div class="form-group mt-4 d-flex align-items-center justify-content-end">
            <a href="{{ route('coupons.show', $coupon->id) }}" class="btn btn-light me-2 rounded-3">{{ __('Cancel') }}</a>
            <button type="submit" class="btn btn-primary rounded-3">
              <i class="fal fa-download me-1"></i>{{ __('Export Codes') }}
            </button>
          </div>
        </form>
      @endif
    </div><!-- exportArea -->

  </section><!-- couponsExportPage -->

@endsection
