@extends('layouts.app')

@section('title', __('Bank Information'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')
  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Bank Information') }}</span>
  </div><!-- breadcrump -->
  <section id="bankInformationPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Bank Information')}}</h1>
    </div><!-- title -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
      <form id="form" method="POST" action="{{ route('bank.information') }}">
        @csrf
        <div class="row">
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="bank_id" class="d-block mb-2">{{__('Bank')}} <span class="text-danger">*</span></label>
              <select name="bank_id" id="bank_id" class="form-control rounded-3 shadow-none border select2-single" @if(auth()->user()->disable_bank_documents) disabled @endif>
                <option value="" disabled selected>{{__('Select your Bank')}}</option>
                @foreach(App\Models\Bank::active()->get() as $bank)
                  <option value="{{$bank->id}}" @if($user->bank_id == $bank->id)selected="selected"@endif>{{ $bank->name }}</option>
                @endforeach
              </select>
            </div>
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <label for="iban_number" class="d-block mb-2">{{__('IBAN Number')}} <span class="text-danger">*</span></label>
              <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control rounded-3 shadow-none border" id="iban_number" placeholder="رقم آيبان مثلاً : SA2720000000000000001212 *" autocomplete="off" @if(auth()->user()->disable_bank_documents) disabled @endif>
              <small id="emailHelp" class="form-text mt-1 d-block text-muted">{{__('This account will be used to settle payments received through point-of-sale devices')}}</small>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12">
            <div class="form-group mb-3">
              <label for="beneficiary_name" class="d-block mb-2">{{__('Beneficiary Name')}} <span class="text-danger">*</span></label>
              <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control rounded-3 shadow-none border onlyEng" id="beneficiary_name" placeholder="{{__('Beneficiary Name')}}" autocomplete="off" @if(auth()->user()->disable_bank_documents) disabled @endif>
              <small id="emailHelp" class="form-text d-block mt-1 text-muted">{{__('Write the name of the account holder in English as registered with the bank')}}</small>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12">
            <span class="d-block fw-bold fs-6 text-body mb-1">{{ __('Upload the required documents') }}</span>
            <p class="d-block mb-3 text-secondary">{{ __('Upload a copy of the IBAN card or an account statement showing the IBAN number and the name of the facility') }}</p>
            @if(auth()->user()->disable_bank_documents)
              <div class="dropzone">
                @foreach(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->bank_documents : auth()->user()->bank_documents as $file)
                  @include('components.file', ['file' => $file])
                @endforeach
              </div>
            @else
              @include('components.dropzone',[
                'documents' => $documents
              ])
            @endif
          </div><!-- col-12 -->
        </div><!-- row -->
        @if(!auth()->user()->disable_bank_documents)
        <div class="saveBtn d-flex justify-content-start mt-3">
          <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold"> {{__('Save')}}</button>
        </div><!-- saveBtn -->
        @endif
      </form>
    </div><!-- blockArea -->
  </section><!-- bankInformationPage -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}
@endpush
