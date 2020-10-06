@extends('layouts.app')

@section('title', __('Business Information'))

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
@endpush

@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{ __('Business Information') }}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item"><a href="{{ url('account') }}" title="{{__('Account')}}">{{__('Account')}}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Business Information')}}</li>
        </ol>
      </nav>
    <div class="separator mb-5"></div>
  </div>

  <div class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data">
          @csrf 
          <div class="form-row">
            <div class="form-group col-md-6">
              <label name="license_type" for="inputEmail3">{{ __('License type') }}</label>
              <select name="license_type" class="form-control">
                <option value="Commercial Record" @if ($user->license_type == 'Commercial Record')selected="selected"@endif>{{ __('Commercial Record') }}</option>
                <option value="Freelance" @if ($user->license_type == 'Freelance')selected="selected"@endif>{{ __('Freelance') }}</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="vat_registration_number">{{ __('VAT Registration Number') }}</label>
              <input value="{{ $user->vat_registration_number }}" name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail1">{{ __('Business Name') }}</label>
              <input value="{{ $user->business_name }}" name="business_name" type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Business Name') }}">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail2">{{ __('Sector') }}</label>
              <input value="{{ $user->sector }}" name="sector" type="text" class="form-control" id="inputEmail2" placeholder="{{ __('Sector') }}">
            </div>
          </div>                  
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="business_address">{{ __('Address') }}</label>
              <input value="{{ $user->business_address }}" name="business_address" type="text" class="form-control" id="business_address" placeholder="{{ __('Address') }}">
            </div>
            <div class="form-group col-md-6">
              <label for="business_mobile">{{ __('Mobile') }}</label>
              <input value="{{ $user->business_mobile }}" name="business_mobile" type="tel" pattern="[0-9]{9}" class="form-control" id="business_mobile" placeholder="{{ __('Mobile') }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail3">{{ __('Website') }}</label>
              <input value="{{ $user->website }}" name="website"  type="text" class="form-control" id="inputEmail3" placeholder="{{ __('Website') }}">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail8">{{ __('Logo') }}</label>
              <div class="custom-file">
                <input name="logo" type="file" class="custom-file-input" id="inputEmail8">
                  @if(auth()->user()->logo)
                    <img  src="{{ url(auth()->user()->logo)  }}" class="img-thumbnail" width="100" />
                  @endif
                    <input type="hidden" name="hidden_logo" value="{{ auth()->user()->logo }}" />
                <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
              </div>
            </div>
          </div> 

          <h5 class="mb-2 mt-2">{{ __('Upload the required documents') }}</h5>
          <p class="">{{ __('Commercial registry, self-employment document, ID card ..etc') }}</p>

          @include('components.dropzone',[
            'documents' => auth()->user()->business_documents
          ])
          <button type="submit" class="btn btn-primary d-block mt-2">{{ __('Save') }}</button>


        </form>
      </div>
    </div>
  </div>
</div>
@endsection
