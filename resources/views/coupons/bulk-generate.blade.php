@extends('layouts.app')

@section('title', __('Generate Coupon Codes'))

@section('content')

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
