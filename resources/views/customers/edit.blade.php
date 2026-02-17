@extends('layouts.app')

@section('title', __('Customers'))

@section('content')

  <h4 class="mb-1">{{ __('Edit')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('/customers') }}" title="{{ __('Customers') }}">{{ __('Customers')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Edit')}}</li>
    </ol>
  </nav>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif


  <form method="post" action="{{ route('customers.update', $customer->id) }}" id="customers_store" class="card mb-6">
    @method('PATCH')
    @csrf
    <div class="card-body">
      <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2 g-6">
        <div class="col">
            <label for="Name" class="form-label">{{__('Name')}}</label>
            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}"  value="{{ $customer->name }}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
            <label for="Mobile" class="form-label">{{ __('Mobile') }}</label>
            <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}" value="{{ $customer->mobile }}" pattern="[0-9]*" maxlength="9" inputmod="numaric" autocomplete="off">
        </div><!-- col -->
        <div class="col">
            <label for="Email" class="form-label">{{__('Email')}}</label>
            <input name="email" type="email" inputmode="email" class="form-control" id="Email" placeholder="{{__('Email')}}" value="{{ $customer->email }}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
            <label for="Notes" class="form-label">{{__('Customer Notes')}}</label>
            <input name="notes" type="text" class="form-control" id="Notes" placeholder="{{__('Customer Notes')}}" value="{{ $customer->notes }}" autocomplete="off">
        </div><!-- col -->
        @if($user->settings->add_tax_invoice)
          <div class="col">
              <label for="bullding_no" class="form-label">{{__('Building Number')}}</label>
              <input name="bullding_no" type="text" class="form-control" id="bullding_no" placeholder="{{__('Building Number')}}"  value="{{ $customer->bullding_no }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
              <label for="street_name" class="form-label">{{__('Street Name')}}</label>
              <input name="street_name" type="text" class="form-control" id="street_name" placeholder="{{__('Street Name')}}" value="{{ $customer->street_name }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
              <label for="district" class="form-label">{{__('District')}}</label>
              <input name="district" type="text" class="form-control" id="district" placeholder="{{__('District')}}"  value="{{ $customer->district }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
              <label for="city" class="form-label">{{__('City')}}</label>
              <input name="city" type="text" class="form-control" id="city" placeholder="{{__('City')}}"  value="{{ $customer->city }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
              <label for="postal_code" class="form-label">{{__('Postal Code')}}</label>
              <input name="postal_code" type="text" class="form-control" id="postal_code" placeholder="{{__('Postal Code')}}"  value="{{ $customer->postal_code }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
              <label for="additional_no" class="form-label">{{__('Additional Number')}}</label>
              <input name="additional_no" type="text" class="form-control" id="additional_no" placeholder="{{__('Additional Number')}}"  value="{{ $customer->additional_no }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
              <label for="other_buyer_id" class="form-label">{{__('Additional ID')}}</label>
              <input name="other_buyer_id" type="text" class="form-control" id="other_buyer_id" placeholder="{{__('Additional ID')}}"  value="{{ $customer->other_buyer_id }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="vat_registration_number" class="form-label">{{__('VAT Registration Number (optional)')}}</label>
            <input name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{__('VAT Registration Number (optional)')}}"  value="{{ $customer->vat_registration_number }}" autocomplete="off">
          </div><!-- col -->
        @endif
      </div><!-- row -->
    </div>
    <div class="card-footer d-flex align-items-center justify-content-end gap-3">
      <a href="{{ url('customers') }}" title="{{__('Back')}}" class="btn btn-light">{{__('Back')}}</a>
      <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
    </div>
  </form>

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\CustomerUpdateRequest', '#customers_store') !!}
@endpush
