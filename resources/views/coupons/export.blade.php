@extends('layouts.app')

@section('title', __('Export Coupon Codes'))

@section('content')

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

      @php
        $totalCodes = $coupon->codes->count();
        $usedCodes = $coupon->codes->where('is_used', true)->count();
        $availableCodes = $totalCodes - $usedCodes;
      @endphp

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
        </div><!-- col -->
      </div><!-- row -->

      @if($totalCodes === 0)
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