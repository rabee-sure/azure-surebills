@extends('account.account_complete')

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('steps')
  <div class="bs-stepper wizard-modern wizard-modern-example">
    <div class="bs-stepper-header gap-0 gap-lg-8 px-0 justify-content-between">
      <div class="step crossed" data-target="#my-information">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle m-0">1</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title fs-5">{{ __('My Information') }}</span>
          </span>
        </button>
      </div>
      <div class="line">
        <i class="icon-base ti ti-chevron-right"></i>
      </div>
      <div class="step crossed" data-target="#business-information">
        <button type="button" class="step-trigger">
          <span class="bs-stepper-circle m-0">2</span>
          <span class="bs-stepper-label">
            <span class="bs-stepper-title fs-5">{{ __('Business Information') }}</span>
          </span>
        </button>
      </div>
      @if(auth()->user()->source == 'sure bills')
        <div class="line">
          <i class="icon-base ti ti-chevron-right"></i>
        </div>
        <div class="step active" data-target="#bank-information">
          <button type="button" class="step-trigger">
            <span class="bs-stepper-circle m-0">3</span>
            <span class="bs-stepper-label">
              <span class="bs-stepper-title fs-5">{{ __('Bank Information') }}</span>
            </span>
          </button>
        </div>
      @endif
    </div>

    <div class="bs-stepper-content">

      @if ($errors->any())
        <ul class="list-group mb-6">
          @foreach ($errors->all() as $error)
            <li class="list-group-item list-group-item-danger">{{ $error }}</li>
          @endforeach
        </ul>
      @endif

      <form id="form" role="form" method="POST" action="{{ route('bank.information', ['redirectHome' => true]) }}">
        @csrf
        <!-- Bank Information -->
        <div id="bank-information" class="content active dstepper-block">
          <div class="row g-6">
            <div class="col-sm-6">
              <label class="form-label" for="bank_id">{{__('Bank')}} <div class="text-danger d-inline-block">*</div></label>
              <select name="bank_id" id="bank_id" class="form-control select2">
                <option value="" disabled selected>{{__('Select your Bank')}}</option>
                @foreach(App\Models\Bank::active()->get() as $bank)
                  <option value="{{$bank->id}}" @if (old('bank_id') == $bank->id)selected="selected"@endif>{{$bank->name}}</option>
                @endforeach
              </select>
            </div><!-- col -->

            <div class="col-sm-6">
              <label class="form-label" for="iban_number">{{__('IBAN Number')}} <div class="text-danger d-inline-block">*</div></label>
              <input value="{{ old('iban_number') }}"  name="iban_number" type="text" class="form-control" id="iban_number" placeholder="{{__('For Example IBAN Number')}} : SA2720000000000000001212 *">
              <small id="emailHelp" class="form-text mt-1 d-block text-muted">{{__('This account will be used to settle payments received through point-of-sale devices')}}</small>
            </div><!-- col -->

            <div class="col-sm-6">
              <label class="form-label" for="beneficiary_name">{{__('Beneficiary Name')}} <div class="text-danger d-inline-block">*</div></label>
              <input value="{{ old('beneficiary_name') }}" name="beneficiary_name" type="text" class="form-control" id="beneficiary_name" placeholder="{{__('Beneficiary Name')}}" autocomplete="off">
              <small id="beneficiary_name_help" class="form-text d-block mt-1 text-muted">{{__('Write the name of the account holder in English as registered with the bank')}}</small>
            </div><!-- col -->

            <div class="col-12">
              <hr class="mb-6 mt-0" />
              <span class="d-block fw-bold fs-6 text-body mb-1">{{ __('Upload the required documents') }}</span>
              <p class="d-block mb-3 text-secondary">{{ __('Upload a copy of the IBAN card or an account statement showing the IBAN number and the name of the facility') }}</p>
              @include('components.dropzone',['documents' => (auth()->user()->mainStoreUser ?? auth()->user())->bank_documents->toArray()])
            </div><!-- col -->

            <div class="col-12 d-flex justify-content-between">
              <a href="/account?previous=2" class="btn btn-label-secondary btn-prev" id="previous">
                <i class="icon-base ti ti-arrow-left icon-xs me-sm-2 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">{{__('Previous')}}</span>
              </a>
              <button type="submit" class="btn btn-primary btn-next">
                <span class="align-middle d-sm-inline-block d-none me-sm-2">{{__('Finish')}}</span>
                <i class="icon-base ti ti-arrow-right icon-xs"></i>
              </button>
            </div><!-- col -->
          </div><!-- row -->
        </div><!-- business-information -->
      </form>
    </div>

  </div>
@endsection

@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      $('.select2').select2();
    });
  </script>

  {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}

  @if(auth()->user()->source == 'pos')
    <script>
        window.location.href = '/account?previous=2';
    </script>
  @endif
@endpush
