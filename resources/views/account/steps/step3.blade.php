@extends('account.account_complete')

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('steps')
  <div class="stepsArea d-flex align-items-start justify-content-center position-relative mb-5">
    <div class="item d-flex align-items-center justify-content-center flex-column done">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm"><i class="fal fa-check"></i></span>
      <p class="d-block text-center mb-0 mt-2">{{ __('My Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column done">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm"><i class="fal fa-check"></i></span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Business Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column active">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">3</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Bank Information') }}</p>
    </div><!-- item -->
  </div><!-- stepsArea -->
  <div class="blockStep3 bg-white rounded-3 shadow-sm p-3">
    <form id="form" role="form" method="POST" action="{{ route('bank.information', ['redirectHome' => true]) }}" class="m-0">
      @csrf
      @if ($errors->any())
        <div class="alert alert-danger">
          <ul>
            @foreach ($errors->all() as $error)
              <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
      @endif
      <div class="row">
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="bank_id" class="d-block mb-2">{{__('Bank')}} <span class="text-danger">*</span></label>
            <select name="bank_id" id="bank_id" class="form-control rounded-3 shadow-none border select2-single">
              <option value="" disabled selected>{{__('Select your Bank')}}</option>
              @foreach(App\Models\Bank::active()->get() as $bank)
                <option value="{{$bank->id}}" @if (old('bank_id') == $bank->id)selected="selected"@endif>{{$bank->name}}</option>
              @endforeach
            </select>
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="iban_number" class="d-block mb-2">{{__('IBAN Number')}} <span class="text-danger">*</span></label>
            <input value="{{ old('iban_number') }}"  name="iban_number" type="text" class="form-control rounded-3 shadow-none border" id="iban_number" placeholder="رقم آيبان مثلاً : SA2720000000000000001212 *">
            <small id="emailHelp" class="form-text mt-1 d-block text-muted">{{__('This account will be used to settle payments received through point-of-sale devices')}}</small>
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12">
          <div class="form-group mb-3">
            <label for="beneficiary_name" class="d-block mb-2">{{__('Beneficiary Name')}} <span class="text-danger">*</span></label>
            <input value="{{ old('beneficiary_name') }}" name="beneficiary_name" type="text" class="form-control rounded-3 shadow-none border onlyEng" id="beneficiary_name" placeholder="{{__('Beneficiary Name')}}" autocomplete="off">
            <small id="emailHelp" class="form-text d-block mt-1 text-muted">{{__('Write the name of the account holder in English as registered with the bank')}}</small>
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12">
          <span class="d-block fw-bold fs-6 text-body mb-1">{{ __('Upload the required documents') }}</span>
          <p class="d-block mb-3 text-secondary">{{ __('Upload a copy of the IBAN card or an account statement showing the IBAN number and the name of the facility') }}</p>
          @include('components.dropzone',['documents' => auth()->user()->bank_documents->toArray()])
        </div><!-- col-12 -->
      </div><!-- row -->
      <div class="btnsArea d-flex align-items-center justify-content-between gap-3 flex-wrap border-top pt-3">
        <a  id="previous" class="d-flex align-items-center justify-content-center btn-light rounded-3 shadow-none fw-bold border-0 px-5" href="/account?previous=2">{{__('Previous')}}</a>
        <button class="d-flex align-items-center justify-content-center btn-primary rounded-3 shadow-none fw-bold border-0 px-5" type="submit">{{__('Finish')}}</button>
      </div><!-- btnsArea -->
    </form>
  </div><!-- blockStep3 -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}

    @if(auth()->user()->source == 'pos')
    <script>
        window.location.href = '/account?previous=2';
    </script>
    @endif

@endpush
