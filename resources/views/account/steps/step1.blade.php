@extends('account.account_complete')

@section('steps')

  <div class="bs-stepper wizard-modern wizard-modern-example">
    <div class="bs-stepper-header gap-0 gap-lg-8 px-0 justify-content-between">
      <div class="step active" data-target="#account-details-modern">
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
      <div class="step" data-target="#personal-info-modern">
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
        <div class="step" data-target="#social-links-modern">
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
      <form id="form" method="POST" action="{{ route('account.information') }}">
        @csrf
        <!-- Account Details -->
        <div id="account-details-modern" class="content active dstepper-block">
          <div class="row g-6">
            <div class="col-sm-6">
              <label class="form-label" for="name">{{ __('Full Name')}} <div class="text-danger d-inline-block">*</div></label>
              <input type="text" id="name" name="name" class="form-control @error('name') is-invalid @enderror" placeholder="{{ __('Full Name')}}" value="{{ $user->name }}" autocomplete="off" />
              @error('name')
                <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-sm-6">
              <label class="form-label" for="email">{{ __('Email')}} <div class="text-danger d-inline-block">*</div></label>
              <input
                type="email"
                inputmode="email"
                id="email"
                class="form-control @error('email') is-invalid @enderror"
                name="email"
                placeholder="{{ __('Email')}}"
                value="{{ $user->email }}"
                autocomplete="off"
              />
              @error('email')
                <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
              @enderror
            </div>
            <div class="col-sm-6">
              <label class="form-label" for="mobile">{{ __('Mobile Number')}}</label>
              <input
                type="tel"
                inputmode="numeric"
                id="mobile"
                class="form-control"
                name="mobile"
                placeholder="5XXXXXXXX"
                disabled="disabled"
                pattern="[0-9]*"
                maxlength="9"
                readonly="readonly"
                value="{{ $user->mobile }}"
                autocomplete="off"
              />
            </div>
            {{-- <div class="col-sm-6">
              <label class="form-label" for="gender">{{ __('Gander')}}</label>
              <select name="gender" id="gender" class="form-control">
                <option value="0" @if ($user->gender == 0)selected="selected"@endif>{{ __('Choose Gender')}}</option>
                <option value="1" @if ($user->gender == 1)selected="selected"@endif>{{ __('Male')}}</option>
                <option value="2" @if ($user->gender == 2)selected="selected"@endif>{{ __('female')}}</option>
              </select>
            </div> --}}
            <div class="col-12 d-flex justify-content-between">
              <div></div>
              <button type="submit" class="btn btn-primary btn-next">
                <span class="align-middle d-sm-inline-block d-none me-sm-2">{{__('Next')}}</span>
                <i class="icon-base ti ti-arrow-right icon-xs"></i>
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>

  </div>
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
