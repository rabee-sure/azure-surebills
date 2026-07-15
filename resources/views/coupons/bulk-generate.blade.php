@extends('layouts.app')

@section('title', __('Generate Coupon Codes'))

@section('content')

<<<<<<< HEAD
  <h4 class="mb-1">{{ __('Generate Coupon Codes')}}</h4>

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
      <li class="breadcrumb-item active">{{ __('Generate Codes')}}</li>
    </ol>
  </nav>

  <form method="POST" action="{{ route('coupons.store-bulk-generate', $coupon->id) }}" id="bulk_generate_form" class="card">
    <div class="card-body">

      <h5 class="card-title mb-5">{{ __('Coupon: :name', ['name' => $coupon->name]) }}</h5>

      @if ($errors->any())
        <ul class="list-group mb-5">
          @foreach ($errors->all() as $error)
            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
          @endforeach
        </ul>
      @endif

      @csrf

      <div class="alert alert-info mb-5">
        <strong>{{ __('Note:') }}</strong> {{ __('This will generate unique one-time use codes for this coupon.') }}
      </div>

      <div class="row g-6">
        <div class="col-12 col-md-6">
          <label for="count" class="form-label">{{ __('Number of Codes to Generate') }} <div class="text-danger d-inline-block">*</div></label>
          <input
            name="count"
            type="number"
            min="1"
            max="10000"
            class="form-control"
            id="count"
            placeholder="{{ __('Enter number of codes (1-10000)') }}"
            value="{{ old('count', 100) }}"
            required
          >
          <div class="form-text">{{ __('Maximum 10,000 codes per generation') }}</div>
          @error('count')
            <div class="invalid-feedback">{{ $message }}</div>
          @enderror
        </div><!-- col -->
        <div class="col-12 col-md-6">
            @php
              $patternTooltip = '<div class="d-flex flex-column gap-4 text-start text-nowrap">
                <div>
                  <div class="fs-6 mb-1 fw-bold">' . __('Code Pattern :') . '</div>
                  <p class="text-muted m-0">' . __('Define how your codes should look using placeholders inside { }.') . '</p>
                </div>
                <div>
                  <div class="fs-6 mb-1 fw-bold">' . __('Examples :') . '</div>
                  <ul class="list-unstyled m-0 d-flex flex-column gap-1">
                    <li>' . __('{RANDOM:8} → Generates an 8-character random code (letters and numbers)') . '</li>
                    <li>' . __('{ALPHA:4}-{NUMBER:4} → 4 letters followed by a dash and 4 numbers (e.g., ABCD-1234)') . '</li>
                    <li>' . __('SAVE-{RANDOM:6} → Code starts with "SAVE" followed by 6 random characters') . '</li>
                  </ul>
                </div>
                <div>
                  <div class="fs-6 mb-1 fw-bold">' . __('Available placeholders :') . '</div>
                  <ul class="list-unstyled m-0 d-flex flex-column gap-1">
                    <li>' . __('{RANDOM} → Random letters and numbers') . '</li>
                    <li>' . __('{NUMBER} → Numbers only') . '</li>
                    <li>' . __('{ALPHA} → Letters only') . '</li>
                    <li>' . __('{UUID} → Automatically generated unique identifier') . '</li>
                  </ul>
                </div>
                <div class="fs-6">' . __('Note: You can control the length by adding a number after : like {RANDOM:8}.') . '</div>
              </div>';
            @endphp
            <label for="pattern" class="form-label">
              {{ __('Code Pattern') }}
              <i
                class="ti ti-help-hexagon text-info"
                data-bs-toggle="tooltip"
                data-bs-placement="top"
                data-bs-html="true"
                data-bs-custom-class="bulk-generate-custom-tooltip"
                data-bs-title='{!! str_replace("'", "&#39;", $patternTooltip) !!}'
              >
              </i>
            </label>
            <input
              name="pattern"
              type="text"
              class="form-control"
              id="pattern"
              placeholder="{{ __('Leave empty to use default pattern') }}"
              value="{{ old('pattern', $coupon->code_pattern) }}"
            >
            <small class="form-text text-muted">
              {{ __('Pattern examples: {RANDOM:8}, {ALPHA:4}-{NUMBER:4}, SAVE-{RANDOM:6}') }}<br>
              {{ __('Available placeholders: {RANDOM}, {UUID}, {NUMBER}, {ALPHA}') }}
            </small>
            @error('pattern')
              <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div><!-- col -->
      </div><!-- row -->

    </div><!-- card-body -->

      <div class="card-footer d-flex align-items-center justify-content-end gap-3">
        <button type="submit" class="btn btn-primary btn-submit-with-spinner waves-effect waves-light" data-loading-text="{{__('Generating...')}}">
          <span class="btn-spinner d-none me-2" role="status">
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
          </span>
          <span class="btn-text">{{__('Generate Codes')}}</span>
        </button>
        <a href="{{ route('coupons.show', $coupon->id) }}" class="btn btn-label-secondary waves-effect">{{ __('Cancel') }}</a>
      </div><!-- card-footer -->

  </form><!-- card -->

@endsection
=======
  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ route('coupons.index')}}" title="{{ __('Coupons') }}">{{ __('Coupons') }}</a>
    <i>/</i>
    <a href="{{ route('coupons.show', $coupon->id)}}" title="{{ __('Coupon Details') }}">{{ __('Coupon Details') }}</a>
    <i>/</i>
    <span>{{ __('Generate Codes')}}</span>
  </div><!-- breadcrump -->

  <section id="couponsBulkGeneratePage">

    <div class="title mb-4 d-flex flex-column gap-2">
      <h1 class="d-block fw-bold m-0 fs-5 text-capitalize">{{ __('Generate Coupon Codes')}}</h1>
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

    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
      <form method="POST" action="{{ route('coupons.store-bulk-generate', $coupon->id) }}" id="bulk_generate_form">
        @csrf

        <div class="alert alert-info mb-4">
          <strong>{{ __('Note:') }}</strong> {{ __('This will generate unique one-time use codes for this coupon.') }}
        </div>

        <div class="row mb-4">
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="count" class="d-block mb-2">{{ __('Number of Codes to Generate') }} <span class="requirement text-danger">*</span></label>
              <input
                name="count"
                type="number"
                min="1"
                max="10000"
                class="form-control shadow-none bg-white border w-100 rounded-3 text-body"
                id="count"
                placeholder="{{ __('Enter number of codes (1-10000)') }}"
                value="{{ old('count', 100) }}"
                required
              >
              <small class="text-muted d-block mt-1">{{ __('Maximum 10,000 codes per generation') }}</small>
              @error('count')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="pattern" class="d-block mb-2">{{ __('Code Pattern') }}</label>
              <input
                name="pattern"
                type="text"
                class="form-control shadow-none bg-white border w-100 rounded-3 text-body"
                id="pattern"
                placeholder="{{ __('Leave empty to use default pattern') }}"
                value="{{ old('pattern', $coupon->code_pattern) }}"
              >
              <small class="text-muted d-block mt-1">
                {{ __('Pattern examples: {RANDOM:8}, {ALPHA:4}-{NUMBER:4}, SAVE-{RANDOM:6}') }}<br>
                {{ __('Available placeholders: {RANDOM}, {UUID}, {NUMBER}, {ALPHA}') }}
              </small>
              @error('pattern')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
            </div><!-- form-group -->
          </div><!-- col -->
        </div><!-- row -->

        <div class="saveBtn d-flex justify-content-start gap-3 mt-3">
          <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center gap-2 fw-bold px-3"> <i class="fal fa-plus-circle"></i>{{ __('Generate Codes') }}</button>
          <a href="{{ route('coupons.show', $coupon->id) }}" class="btn-light rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold px-3">{{ __('Cancel') }}</a>
        </div>

      </form>
    </div><!-- bulkGenerateArea -->

  </section><!-- couponsBulkGeneratePage -->

@endsection
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
