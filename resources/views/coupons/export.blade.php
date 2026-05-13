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

    <div class="title mb-4 d-flex flex-column gap-2">
      <h1 class="d-block fw-bold m-0 fs-5 text-capitalize">{{ __('Export Coupon Codes')}}</h1>
      <small class="text-muted d-block">{{ __('Coupon: :name', ['name' => $coupon->name]) }}</small>
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

    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">

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
          <div class="border shadow-sm rounded-2 p-3 d-flex flex-column gap-2 h-100">
            <span class="d-block fs-6 text-capitalize fw-bold text-center">{{ __('Total Codes') }}</span>
            <span class="d-block fs-4 fw-bold text-info text-center">{{ number_format($totalCodes) }}</span>
          </div><!-- itemBox -->
        </div><!-- col -->
        <div class="col-md-4">
          <div class="border shadow-sm rounded-2 p-3 d-flex flex-column gap-2 h-100">
            <span class="d-block fs-6 text-capitalize fw-bold text-center">{{ __('Available') }}</span>
            <span class="d-block fs-4 fw-bold text-success text-center">{{ number_format($availableCodes) }}</span>
          </div><!-- itemBox -->
        </div><!-- col -->
        <div class="col-md-4">
          <div class="border shadow-sm rounded-2 p-3 d-flex flex-column gap-2 h-100">
            <span class="d-block fs-6 text-capitalize fw-bold text-center">{{ __('Used') }}</span>
            <span class="d-block fs-4 fw-bold text-danger text-center">{{ number_format($usedCodes) }}</span>
          </div><!-- itemBox -->
        </div><!-- col -->
      </div><!-- row -->

      @if($totalCodes === 0)
        <div class="alert alert-warning mb-4">
          {{ __('No codes have been generated for this coupon yet. Please generate codes first.') }}
        </div>
        <div class="saveBtn d-flex justify-content-start gap-3 mt-3">
          <a href="{{ route('coupons.bulk-generate', $coupon->id) }}" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center gap-2 fw-bold px-3"><i class="fal fa-plus-circle"></i>{{ __('Generate Codes') }}</a>
          <a href="{{ route('coupons.show', $coupon->id) }}" class="btn-light rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold px-3">{{ __('Back') }}</a>
        </div>
      @else
        <form method="POST" action="{{ route('coupons.export', $coupon->id) }}" id="export_form" class="mb-4">
          @csrf

          <div class="form-group mb-4">
            <label class="d-block mb-3">{{ __('Export Format') }} <span class="requirement text-danger">*</span></label>
            <div class="d-flex gap-3">

              <div class="checkItem position-relative overflow-hidden">
                <input class="position-absolute top-0 start-0 w-100 h-100 opacity-0 z-index-1" type="radio" name="format" id="format_csv" value="csv" checked>
                <label class="border p-3 d-flex align-items-center justify-content-start gap-3 rounded-3 shadow-sm" for="format_csv">
                  <div class="checkmark rounded-circle bg-white border flex-shrink-0 position-relative d-flex align-items-center justify-content-center fs-6"><i class="fal fa-check text-white"></i></div>
                  <div class="d-flex flex-column gap-1 flex-grow-1">
                    <strong>CSV</strong>
                    <small class="text-muted">{{ __('Comma-separated values') }}</small>
                  </div>
                </label>
              </div>

              <div class="checkItem position-relative overflow-hidden">
                <input class="position-absolute top-0 start-0 w-100 h-100 opacity-0 z-index-1" type="radio" name="format" id="format_excel" value="excel">
                <label class="border p-3 d-flex align-items-center justify-content-start gap-3 rounded-3 shadow-sm" for="format_excel">
                  <div class="checkmark rounded-circle bg-white border flex-shrink-0 position-relative d-flex align-items-center justify-content-center fs-6"><i class="fal fa-check text-white"></i></div>
                  <div class="d-flex flex-column gap-1 flex-grow-1">
                    <strong>Excel</strong>
                    <small class="text-muted">{{ __('Microsoft Excel format (.xlsx)') }}</small>
                  </div>
                </label>
              </div>
          </div>


          @error('format')
              <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror


          <div class="saveBtn d-flex justify-content-start gap-3 mt-4">
            <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center gap-2 fw-bold px-3"><i class="fal fa-download"></i>{{ __('Export Codes') }}</button>
            <a href="{{ route('coupons.show', $coupon->id) }}" class="btn-light rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold px-3">{{ __('Cancel') }}</a>
          </div>
        </form>
      @endif
    </div><!-- exportArea -->

  </section><!-- couponsExportPage -->

@endsection
