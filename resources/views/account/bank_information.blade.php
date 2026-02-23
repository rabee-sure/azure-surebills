@extends('layouts.app')

@section('title', __('Bank Information'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('content')

  <h4 class="mb-1">{{ __('Bank Information')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('account') }}" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Bank Information') }}</li>
    </ol>
  </nav>

  <form id="form" method="POST" action="{{ route('bank.information') }}" class="card">
    @csrf
    <div class="card-body">
      <div class="row g-6">
        <div class="col-12 col-md-6">
          <label for="bank_id" class="form-label">{{__('Bank')}} <span class="text-danger">*</span></label>
          <select name="bank_id" id="bank_id" class="form-select select2" @if(auth()->user()->disable_bank_documents) disabled @endif>
            <option value="" disabled selected>{{__('Select your Bank')}}</option>
            @foreach(App\Models\Bank::active()->get() as $bank)
              <option value="{{$bank->id}}" @if($user->bank_id == $bank->id)selected="selected"@endif>{{ $bank->name }}</option>
            @endforeach
          </select>
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <label for="iban_number" class="form-label">{{__('IBAN Number')}} <span class="text-danger">*</span></label>
          <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="iban_number" placeholder="رقم آيبان مثلاً : SA2720000000000000001212 *" aria-describedby="ibanNumberHelp" autocomplete="off" @if(auth()->user()->disable_bank_documents) disabled @endif>
          <div id="ibanNumberHelp" class="form-text">{{__('This account will be used to settle payments received through point-of-sale devices')}}</div>
        </div><!-- col-12 -->
        <div class="col-12">
          <label for="beneficiary_name" class="form-label">{{__('Beneficiary Name')}} <span class="text-danger">*</span></label>
          <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control" id="beneficiary_name" placeholder="{{__('Beneficiary Name')}}" aria-describedby="beneficiaryNameHelp" autocomplete="off" @if(auth()->user()->disable_bank_documents) disabled @endif>
          <div id="beneficiaryNameHelp" class="form-text">{{__('Write the name of the account holder in English as registered with the bank')}}</div>
        </div><!-- col-12 -->
        <div class="col-12">
          <hr class="mb-6 mt-0" />
          <label for="commercial_registry_expiry_date" class="form-label d-flex align-items-start justify-content-start flex-column">
            <span class="d-block fs-5 mb-1">{{ __('Upload the required documents') }}</span>
            <span class="text-muted mb-2">{{ __('Upload a copy of the IBAN card or an account statement showing the IBAN number and the name of the facility') }}</span>
          </label>
          @if(auth()->user()->disable_bank_documents)
            <div class="dropzone">
              @foreach(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->bank_documents : auth()->user()->bank_documents as $file)
                @include('components.file', ['file' => $file])
              @endforeach
            </div>
          @else
            @include('components.dropzone',[
              'documents' => auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->bank_documents->toArray() : auth()->user()->bank_documents->toArray()
            ])
          @endif
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- card-body -->
    @if(!auth()->user()->disable_bank_documents)
      <div class="card-footer d-flex align-items-center justify-content-end">
        <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
      </div><!-- card-footer -->
    @endif
  </form>

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // Select2
      $('.select2').select2();
    });
  </script>
@endpush
