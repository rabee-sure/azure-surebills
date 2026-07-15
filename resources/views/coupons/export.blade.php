@extends('layouts.app')

@section('title', __('Export Coupon Codes'))

@section('content')

<<<<<<< HEAD
  <h4 class="mb-1">{{ __('Export Coupon Codes')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="/account" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('coupons.index')}}" title="{{ __('Coupons') }}">{{ __('Coupons')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item">
        <a href="{{ route('coupons.show', $coupon->id)}}" title="{{ __('Coupon Details') }}">{{ __('Coupon Details') }}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Export Codes')}}</li>
    </ol>
  </nav>

  <div class="card">
    <div class="card-body">

      <h5 class="card-title mb-5">{{ __('Coupon: :name', ['name' => $coupon->name]) }}</h5>

      @if ($errors->any())
        <ul class="list-group mb-5">
          @foreach ($errors->all() as $error)
            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
          @endforeach
        </ul>
      @endif

      <div class="alert alert-info mb-5">
        <strong>{{ __('Note:') }}</strong> {{ __('Export all generated codes for this coupon. The file will include code and usage status.') }}
      </div><!-- alert -->
=======
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
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4

      @php
        $totalCodes = $coupon->codes->count();
        $usedCodes = $coupon->codes->where('is_used', true)->count();
        $availableCodes = $totalCodes - $usedCodes;
      @endphp

<<<<<<< HEAD
      <div class="row row-cols-1 row-cols-md-3 g-6 mb-5">
        <div class="col">
          <div class="card shadow-none bg-transparent border border-primary text-primary h-100">
            <div class="card-body">
              <h3 class="card-title text-primary fw-bold lh-1">{{ number_format($totalCodes) }}</h3>
              <h6 class="card-text text-primary m-0">{{ __('Total Codes') }}</h6>
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card shadow-none bg-transparent border border-success text-danger h-100">
            <div class="card-body">
              <h3 class="card-title text-success fw-bold lh-1">{{ number_format($availableCodes) }}</h3>
              <h6 class="card-text text-success m-0">{{ __('Available') }}</h6>
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card shadow-none bg-transparent border border-danger text-danger h-100">
            <div class="card-body">
              <h3 class="card-title text-danger fw-bold lh-1">{{ number_format($usedCodes) }}</h3>
              <h6 class="card-text text-danger m-0">{{ __('Used') }}</h6>
            </div><!-- card-body -->
          </div><!-- card -->
=======
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
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
        </div><!-- col -->
      </div><!-- row -->

      @if($totalCodes === 0)
<<<<<<< HEAD
        <div class="alert alert-warning mb-5">
          {{ __('No codes have been generated for this coupon yet. Please generate codes first.') }}
        </div>
        <div class="d-flex align-items-center justify-content-end gap-3">
          <a href="{{ route('coupons.bulk-generate', $coupon->id) }}" class="btn btn-primary waves-effect waves-light">{{ __('Generate Codes') }}</a>
          <a href="{{ route('coupons.show', $coupon->id) }}" class="btn btn-label-secondary waves-effect">{{ __('Back') }}</a>
        </div>
      @else
        <form method="POST" action="{{ route('coupons.export', $coupon->id) }}" id="export_form">
          <div class="mb-5">
            @csrf
            <label class="form-label">{{ __('Export Format') }} <span class="d-inline-block text-danger">*</span></label>
            <div class="row">
              <div class="col-12 col-md-6">
                <div class="row row-cols-1 row-cols-md-2 g-6">
                  <div class="col">
                    <div class="form-check custom-option custom-option-basic checked">
                      <label class="form-check-label custom-option-content" for="format_csv">
                        <input name="format" class="form-check-input" type="radio" value="csv" id="format_csv" checked>
                        <span class="custom-option-header pb-1">
                          <span class="h6 mb-0">CSV</span>
                        </span>
                        <span class="custom-option-body">
                          <small>{{ __('Comma-separated values') }}</small>
                        </span>
                      </label>
                    </div><!-- custom-option -->
                  </div><!-- col -->
                  <div class="col">
                    <div class="form-check custom-option custom-option-basic checked">
                      <label class="form-check-label custom-option-content" for="format_excel">
                        <input name="format" class="form-check-input" type="radio" value="excel" id="format_excel">
                        <span class="custom-option-header pb-1">
                          <span class="h6 mb-0">Excel</span>
                        </span>
                        <span class="custom-option-body">
                          <small>{{ __('Microsoft Excel format (.xlsx)') }}</small>
                        </span>
                      </label>
                    </div><!-- custom-option -->
                  </div><!-- col -->
                </div><!-- row -->
              </div><!-- col -->
            </div><!-- row -->
            @error('format')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
          </div>

          <div class="d-flex align-items-center justify-content-end gap-3">
            <button type="submit" class="btn btn-primary waves-effect waves-light">{{ __('Export Codes') }}</button>
            <a href="{{ route('coupons.show', $coupon->id) }}" class="btn btn-label-secondary waves-effect">{{ __('Cancel') }}</a>
          </div>

        </form>
      @endif

    </div><!-- card-body -->
  </div><!-- card -->

@endsection
=======
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
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
