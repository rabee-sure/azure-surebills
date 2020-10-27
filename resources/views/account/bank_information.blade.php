@extends('layouts.app')
@section('title', __('Bank Information'))
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
              <label for="inputEmail5">{{__('Bank')}} <span class="requirement">*</span></label>
              <select name="bank_id" id="inputEmail5" class="form-control">
              <option value="" disabled selected>{{__('Select your Bank')}}</option>

                @foreach(App\Bank::active()->get() as $bank)
                  <option value="{{$bank->id}}" @if($user->bank_id == $bank->id)selected="selected"@endif>
                     {{ $bank->name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail7">{{__('IBAN Number')}} <span class="requirement">*</span></label>
              <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="رقم آيبان مثلاً : SA2720000000000000001212 *">
              <small id="emailHelp" class="form-text text-muted">هذا الحساب سيستخدم لتسوية المدفوعات الواصلة لك عبر أجهزة نقاط البيع</small>
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail9">{{__('Beneficiary Name')}} <span class="requirement">*</span></label>
              <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control" id="inputEmail9" placeholder="{{__('Beneficiary Name')}}">
            </div>
          </div>

          <h5 class="mb-2 mt-2">{{ __('Upload the required documents') }}</h5>
          <p class="">{{ __('Upload a copy of the IBAN card or an account statement showing the IBAN number and the name of the facility') }}</p>

          
          @if(auth()->user()->disable_bank_documents)
            <div class="dropzone">
              @foreach(auth()->user()->bank_documents as $file)
                @include('components.file', ['file' => $file])
              @endforeach
            </div>
          @else
            @include('components.dropzone',[
              'documents' => auth()->user()->bank_documents
            ])
          @endif

          <button type="submit" class="btn btn-primary d-block mt-2">{{__('Save')}}</button>
        </form>
      </div>
    </div>
  </div>
</div>
@endsection



@push('header-css')
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
@endpush

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}
@endpush