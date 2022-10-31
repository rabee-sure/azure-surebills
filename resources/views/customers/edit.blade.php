@extends('layouts.app')

@section('title', __('Customers'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('/customers') }}" title="{{ __('Customers') }}">{{ __('Customers')}}</a>
    <i>/</i>
    <span>{{ __('Edit')}}</span>
  </div><!-- breadcrump -->

  <section id="customerEditPage">

    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Edit')}}</h1>
    </div><!-- title -->

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="blockArea rounded-3 shadow-sm overflow-hidden bg-white">
      <form method="post" action="{{ route('customers.update', $customer->id) }}" id="customers_store">
        @method('PATCH')
        @csrf
        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-2">
          <div class="col">
            <div class="form-group mb-3">
              <label for="Name" class="d-block mb-1">{{__('Name')}}</label>
              <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}"  value="{{ $customer->name }}">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col">
            <div class="form-group mb-3">
              <label for="Mobile" class="d-block mb-1">{{ __('Mobile') }}</label>
              <div class="phoneInput overflow-hidden position-relative">
                <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
                <input name="mobile" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Mobile" placeholder="{{__('Mobile')}}" value="{{ $customer->mobile }}" pattern="[0-9]*" maxlength="9" inputmod="numaric">
              </div><!-- phoneInput -->
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col">
            <div class="form-group mb-3">
              <label for="Email" class="d-block mb-1">{{__('Email')}}</label>
              <input name="email" type="email" inputmode="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Email" placeholder="{{__('Email')}}" value="{{ $customer->email }}">
            </div><!-- form-group -->
          </div><!-- col -->
          <div class="col">
            <div class="form-group mb-3">
              <label for="Notes" class="d-block mb-1">{{__('Customer Notes')}}</label>
              <input name="notes" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Notes" placeholder="{{__('Customer Notes')}}" value="{{ $customer->notes }}">
            </div><!-- form-group -->
          </div><!-- col -->
          @if($user->settings->add_tax_invoice)
            <div class="col">
              <div class="form-group mb-3">
                <label for="bullding_no" class="d-block mb-1">{{__('Building Number')}}</label>
                <input name="bullding_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('Building Number')}}"  value="{{ $customer->bullding_no }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="street_name" class="d-block mb-1">{{__('Street Name')}}</label>
                <input name="street_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('Street Name')}}"  value="{{ $customer->street_name }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="district" class="d-block mb-1">{{__('District')}}</label>
                <input name="district" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('District')}}"  value="{{ $customer->district }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="city" class="d-block mb-1">{{__('City')}}</label>
                <input name="city" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('City')}}"  value="{{ $customer->city }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="postal_code" class="d-block mb-1">{{__('Postal Code')}}</label>
                <input name="postal_code" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('Postal Code')}}"  value="{{ $customer->postal_code }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="additional_no" class="d-block mb-1">{{__('Additional Number')}}</label>
                <input name="additional_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('Additional Number')}}"  value="{{ $customer->additional_no }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="other_buyer_id" class="d-block mb-1">{{__('Additional ID')}}</label>
                <input name="other_buyer_id" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('Additional ID')}}"  value="{{ $customer->other_buyer_id }}">
              </div><!-- form-group -->
            </div><!-- col -->
            <div class="col">
              <div class="form-group mb-3">
                <label for="vat_registration_number" class="d-block mb-1">{{__('VAT Registration Number (optional)')}}</label>
                <input name="vat_registration_number" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="vat_registration_number" placeholder="{{__('VAT Registration Number (optional)')}}"  value="{{ $customer->vat_registration_number }}">
              </div><!-- form-group -->
            </div><!-- col -->
          @endif
        </div><!-- row -->
        <div class="buttonsArea mt-5 d-flex align-items-center justify-content-start">
          <button type="submit" class="rounded-3 border-0 shadow-none d-flex align-items-center justify-content-center btn-primary fw-bold formBtn">{{__('Update')}}</button>
          <a href="{{ url('customers') }}" title="{{__('Back')}}" class="rounded-3 border-0 shadow-none d-flex align-items-center fw-bold justify-content-center btn-light m-0">{{__('Back')}}</a>
        </div><!-- buttonsArea -->
      </form>
    </div><!-- blockArea -->

  </section><!-- customerEditPage -->
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\CustomerUpdateRequest', '#customers_store') !!}
@endpush
