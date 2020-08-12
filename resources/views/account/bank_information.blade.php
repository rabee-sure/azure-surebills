@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{ __('Bank Information')}}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item"><a href="{{ url('account') }}" title="{{__('Account')}}">{{__('Account')}}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Bank Information')}}</li>
        </ol>
      </nav>
    <div class="separator mb-5"></div>
  </div>

  <div class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        <form id="form" method="POST" action="{{ route('bank.information') }}">
          @csrf 
          <div class="form-row">
            <div class="form-group col-12">
              <label for="inputEmail5">{{__('Bank')}}</label>
              <select name="bank" id="inputEmail5" class="form-control">
              <option value="" disabled selected>{{__('Select your Bank')}}</option>

                @foreach(getBanks() as $bank)
                  <option value="{{$bank['id']}}" @if ($user->bank == $bank['id'])selected="selected"@endif>
                     @if(app()->getLocale() == 'ar')
                     {{$bank['ar']}}
                     @else
                     {{$bank['en']}}
                     @endif
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail7">{{__('IBAN Number')}}</label>
              <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="{{__('IBAN Number')}}">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail9">{{__('Beneficiary Name')}}</label>
              <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control" id="inputEmail9" placeholder="{{__('Beneficiary Name')}}">
            </div>
          </div>
          <button type="submit" class="btn btn-primary d-block mt-2">{{__('Save')}}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}
@endpush