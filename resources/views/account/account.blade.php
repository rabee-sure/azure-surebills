@extends('layouts.app')

@section('title', __('My Account'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/smart_wizard.min.css') }}">
@endsection


<link rel="stylesheet" href="css/vendor/" />

@section('content')
	<div class="row">
		<div class="col-12">
			<h1>{{ __('My Account') }}</h1>
			<div class="separator mb-5"></div>
		</div>
    <div class="col-12">
      <div class="row icon-cards-row mx-n3">
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('account_information') }}" title="{{ __('My Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-id-card"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('My Information') }}</p>
            </div>
          </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('business_information') }}" title="{{ __('Business Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-management"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Business Information') }}</p>
            </div>
          </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('bank_information') }}" title="{{ __('Bank Information') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-bank"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Bank Information') }}</p>
            </div>
          </a>
        </div>
        <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
          <a href="{{ route('change_password') }}" title="{{ __('Change Password') }}" class="card mb-4">
            <div class="card-body text-center">
              <div class="statistic_icon iconsminds-type-pass"></div>
              <p class="card-text font-weight-semibold mb-0">{{ __('Change Password') }}</p>
            </div>
          </a>
        </div>
      </div>
    </div>
    <div class="col-12">
      <div class="card">
        <div id="smartWizardValidation">
          <ul class="card-header">
            <li><a href="#step-0">Step 1<br /><small>{{ __('My Information') }}</small></a></li>
            <li><a href="#step-1">Step 2<br /><small>{{ __('Business Information') }}</small></a></li>
            <li><a href="#step-2">Step 3<br /><small>{{ __('Bank Information') }}</small></a></li>
          </ul>
          <div class="card-body">
            <div id="step-0">
              <form id="form" method="POST" action="#">
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="inputEmail1">{{ __('Full Name')}}</label>
                    <input name="name" type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Full Name')}}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputEmail2">{{ __('Email')}}</label>
                    <input name="email" type="email" class="form-control" id="inputEmail2" placeholder="{{ __('Email')}}">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="inputEmail3">{{ __('Mobile Number')}}</label>
                    <input name="mobile" type="tel" class="form-control" id="inputEmail3" placeholder="{{ __('Mobile Number')}}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputEmail4">{{ __('Gander')}}</label>
                    <select name="gender" id="inputEmail4" class="form-control">
                      <option value="0">{{ __('Choose...')}}</option>
                      <option value="1">{{ __('Male')}}</option>
                      <option value="2">{{ __('female')}}</option>
                    </select>
                  </div>
                </div>
              </form>
            </div><!-- step-0 -->
            <div id="step-1">
              <form id="form" method="POST" action="#" enctype="multipart/form-data"> 
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label name="license_type" for="inputEmail3">{{ __('License type') }}</label>
                    <select name="license_type" class="form-control">
                      <option value="Commercial Record">{{ __('Commercial Record') }}</option>
                      <option value="Freelance">{{ __('Freelance') }}</option>
                    </select>
                  </div>
                  <div class="form-group col-md-6">
                    <label for="vat_registration_number">{{ __('VAT Registration Number') }}</label>
                    <input name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="inputEmail1">{{ __('Business Name') }}</label>
                    <input name="business_name" type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Business Name') }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputEmail2">{{ __('Sector') }}</label>
                    <input name="sector" type="text" class="form-control" id="inputEmail2" placeholder="{{ __('Sector') }}">
                  </div>
                </div>                  
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="business_address">{{ __('Address') }}</label>
                    <input name="business_address" type="text" class="form-control" id="business_address" placeholder="{{ __('Address') }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="business_mobile">{{ __('Mobile') }}</label>
                    <input name="business_mobile" type="tel" class="form-control" id="business_mobile" placeholder="{{ __('Mobile') }}">
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="inputEmail3">{{ __('Website') }}</label>
                    <input name="website"  type="text" class="form-control" id="inputEmail3" placeholder="{{ __('Website') }}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputEmail8">{{ __('Logo') }}</label>
                    <div class="custom-file">
                      <input name="logo" type="file" class="custom-file-input" id="inputEmail8">
                      <input type="hidden" name="hidden_logo" />
                      <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
                    </div>
                  </div>
                </div>
              </form>
            </div><!-- step-1 -->
            <div id="step-2">
              <form id="form" method="POST" action="#">
                <div class="form-row">
                  <div class="form-group col-12">
                    <label for="inputEmail5">{{__('Bank')}}</label>
                    <select name="bank" id="inputEmail5" class="form-control">
                      <option value="" disabled selected>{{__('Select your Bank')}}</option>
                      @foreach(getBanks() as $bank)
                        <option value="{{$bank['id']}}">{{$bank['en']}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-row">
                  <div class="form-group col-md-6">
                    <label for="inputEmail7">{{__('IBAN Number')}}</label>
                    <input  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="{{__('IBAN Number')}}">
                  </div>
                  <div class="form-group col-md-6">
                    <label for="inputEmail9">{{__('Beneficiary Name')}}</label>
                    <input name="beneficiary_name" type="text" class="form-control" id="inputEmail9" placeholder="{{__('Beneficiary Name')}}">
                  </div>
                </div>
              </form>
            </div><!-- step-2 -->
          </div><!-- card-body -->
          <div class="btn-toolbar custom-toolbar text-center card-body pt-0">
            <button class="btn btn-primary prev-btn mx-2" type="button">{{__('Previous')}}</button>
            <button class="btn btn-primary next-btn mx-2" type="button">{{__('Next')}}</button>
            <button class="btn btn-primary finish-btn mx-2" type="submit">{{__('Finish')}}</button>
          </div>
        </div>
      </div>
    </div>



	</div>
@endsection

  <script src="{{ asset('js/jquery.smartWizard.min.js') }}" defer></script>


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endsection
