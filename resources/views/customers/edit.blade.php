@extends('layouts.app')

@section('title', __('Customers'))

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Customers')}}</h1>
        <div class="top-right-button-container">
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>          
          <li class="breadcrumb-item">
            <a href="{{ url('/customers') }}">{{ __('Customers')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Edit')}}</li>
        </ol>
      </nav>
      </div>
    </div>
    <div class="separator mb-5"></div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('customers.update', $customer->id) }}" id="customers_store">
                <div class="modal-body">
                        @method('PATCH') 
                        @csrf
                        <div class="form-group">
                            <label for="Name">{{__('Name')}}</label>
                            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}"  value="{{ $customer->name }}">
                        </div>
                        <div class="form-group">
                            <label for="Mobile">{{ __('Mobile') }}</label>
                            <div class="input-group phone_inputs">
                              <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">+966</span>
                              </div>
                              <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}" value="{{ $customer->mobile }}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Email">{{__('Email')}}</label>
                            <input  name="email" type="email" class="form-control" id="Email" placeholder="{{__('Email')}}" value="{{ $customer->email }}">
                        </div>
                        <div class="form-group">
                            <label for="Notes">{{__('Notes')}}</label>
                            <input name="notes" type="text" class="form-control" id="Notes" placeholder="{{__('Notes')}}" value="{{ $customer->notes }}">
                        </div>

                        <div class="form-group">
                            <label for="bullding_no">{{__('bullding_no')}}</label>
                            <input name="bullding_no" type="text" class="form-control" id="bullding_no" placeholder="{{__('bullding_no')}}"  value="{{ $customer->bullding_no }}">
                        </div> 
                        <div class="form-group">
                            <label for="street_name">{{__('street_name')}}</label>
                            <input name="street_name" type="text" class="form-control" id="street_name" placeholder="{{__('street_name')}}"  value="{{ $customer->street_name }}">
                        </div>
                        <div class="form-group">
                            <label for="district">{{__('district')}}</label>
                            <input name="district" type="text" class="form-control" id="district" placeholder="{{__('district')}}"  value="{{ $customer->district }}">
                        </div>
                        <div class="form-group">
                            <label for="city">{{__('city')}}</label>
                            <input name="city" type="text" class="form-control" id="city" placeholder="{{__('city')}}"  value="{{ $customer->city }}">
                        </div>
                        <div class="form-group">
                            <label for="postal_code">{{__('postal_code')}}</label>
                            <input name="postal_code" type="text" class="form-control" id="postal_code" placeholder="{{__('postal_code')}}"  value="{{ $customer->postal_code }}">
                        </div>
                        <div class="form-group">
                            <label for="additional_no">{{__('additional_no')}}</label>
                            <input name="additional_no" type="text" class="form-control" id="additional_no" placeholder="{{__('additional_no')}}"  value="{{ $customer->additional_no }}">
                        </div>
                        <div class="form-group">
                            <label for="other_buyer_id">{{__('other_buyer_id')}}</label>
                            <input name="other_buyer_id" type="text" class="form-control" id="other_buyer_id" placeholder="{{__('other_buyer_id')}}"  value="{{ $customer->other_buyer_id }}">
                        </div> 
                        <div class="form-group">
                            <label for="vat_registration_number">{{__('vat_registration_number')}}</label>
                            <input name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{__('vat_registration_number')}}"  value="{{ $customer->vat_registration_number }}">
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary login_button mr-3">{{__('Update')}}</button>
                    <a href="{{ url('customers') }}" class="btn btn-secondary m-0" data-dismiss="modal">{{__('Back')}}</a>
                </div>
            </form>
      </div>
    </div>
  </div>
  </div>
@endsection


@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\CustomerUpdateRequest', '#customers_store') !!}
@endpush
