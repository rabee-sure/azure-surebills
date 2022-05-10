@extends('layouts.app')
@section('title', __('Invoice Settings'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/jquery-ui/jquery-ui.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Invoice Settings') }}</span>
  </div><!-- breadcrump -->

  <section id="invoiceSettingsPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Invoice Settings')}}</h1>
    </div><!-- title -->
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- alert -->
    @endif
    @if($user->settings)
      <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
        <form method="POST" action="{{ route('post.settings') }}" class="repeater" id="settings">
          @csrf
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('Taxs') }}</div>
          <div class="row">
            <div class="col-12 col-md-6">
              <label for="Tax_Values_Checkbox" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="add_tax" class="position-absolute top-0 strat-0 w-100 h-100" id="Tax_Values_Checkbox" type="checkbox" @if($user->settings->add_tax || old('add_tax') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Add Tax') }}
                </span>
              </label>
              <div class="Tax_Values">
                <div class="row py-3">
                  <div class="col-12 col-md-6">
                    <div class="form-group">
                      <label for="Tax" class="d-block mb-2">{{ __('Tax Value') }} <small class="d-inline-block text-secondary">(%)</small></label>
                      <div class="inputGroup position-relative d-flex align-items-center justify-content-start flex-wrap">
                        <div class="txt align-items-center justify-content-center position-absolute rounded-3" id="percentage"><i class="far fa-percentage"></i></div>
                        <input type="tel" name="tax_value" class="form-control shadow-none bg-white border w-100 rounded-3" min="0" max="100" id="Value" value="{{ old('tax_value') ?? $user->settings->tax_value }}" aria-describedby="basic-addon3">
                      </div><!-- inputGroup -->
                    </div><!-- form-group -->
                  </div><!-- col-12 -->
                </div><!-- row -->
              </div><!-- Tax_Values -->
            </div><!-- col-12 -->
          </div><!-- row -->
          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('Tax Invoice') }}</div>
          <div class="row">
            <div class="col-12">
              <label for="Tax_Invoice_Values_Checkbox" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="add_tax_invoice" class="position-absolute top-0 strat-0 w-100 h-100" id="Tax_Invoice_Values_Checkbox" type="checkbox" @if($user->settings->add_tax_invoice || old('add_tax_invoice') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Activate Tax Invoice') }}
                </span>
              </label>
            </div><!-- col-12 -->
            <div class="col-12 Tax_Invoice_Values">
              <div class="row mt-3">
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="bullding_no" class="d-block mb-2">{{__('bullding_no')}}</label>
                    <input name="bullding_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('bullding_no')}}"  value="@if($errors->any()){{old('bullding_no')}}@else{{ $user->bullding_no }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="street_name" class="d-block mb-2">{{__('street_name')}}</label>
                    <input name="street_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('street_name')}}"  value="@if($errors->any()){{old('street_name')}}@else{{ $user->street_name }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="district" class="d-block mb-2">{{__('district')}}</label>
                    <input name="district" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('district')}}"  value="@if($errors->any()){{old('district')}}@else{{ $user->district }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="city" class="d-block mb-2">{{__('city')}}</label>
                    <input name="city" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('city')}}"  value="@if($errors->any()){{old('city')}}@else{{ $user->city }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="postal_code" class="d-block mb-2">{{__('postal_code')}}</label>
                    <input name="postal_code" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('postal_code')}}"  value="@if($errors->any()){{old('postal_code')}}@else{{ $user->postal_code }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="additional_no" class="d-block mb-2">{{__('additional_no')}}</label>
                    <input name="additional_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('additional_no')}}"  value="@if($errors->any()){{old('additional_no')}}@else{{ $user->additional_no }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="other_buyer_id" class="d-block mb-2">{{__('other_buyer_id')}}</label>
                    <input name="other_buyer_id" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('other_buyer_id')}}"  value="@if($errors->any()){{old('other_buyer_id')}}@else{{ $user->other_buyer_id }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="vat_registration_number" class="d-block mb-2">{{ __('VAT Registration Number') }} ( {{ __('optional') }} )</label>
                    <input value="@if($errors->any()){{old('vat_registration_number')}}@else{{ $user->vat_registration_number }}@endif" name="vat_registration_number" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
              </div><!-- row -->
            </div><!-- col-12 -->
          </div><!-- row -->
          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('Default Language for Bills') }}</div>
          <div class="row">
            <div class="col-12 col-md-6 d-flex align-items-start justify-content-start flex-column">
              <span class="d-block mb-3 fs-6">{{ __('Active Langs') }}</span>
              <label for="arabic" class="checkboxItem position-relative mb-3 d-block">
                <input name="active_lang_ar" class="position-absolute top-0 strat-0 w-100 h-100" id="arabic" type="checkbox" @if($user->settings->active_lang  == 'ar'|| $user->settings->active_lang  == 'all' || old('active_lang_ar') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Arabic') }}
                </span>
              </label>
              <label for="english" class="checkboxItem position-relative mb-3 mb-md-0 d-block">
                <input type="checkbox" name="active_lang_en" class="position-absolute top-0 strat-0 w-100 h-100" id="english" @if($user->settings->active_lang  == 'en'|| $user->settings->active_lang  == 'all' || old('active_lang_en') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('English') }}
                </span>
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-md-6">
              <div class="form-group mb-3" id="default_lang">
                <label for="default_lang" class="d-block mb-2">{{ __('Default Lang') }}</label>
                <select name="default_lang" class="form-control shadow-none bg-white border w-100 rounded-3 text-body select2-single">
                  <option value="ar" @if($user->settings->default_lang  == 'ar' || old('default_lang') == 'ar')selected="selected" @endif>{{ __('Arabic') }}</option>
                  <option value="en" @if($user->settings->default_lang  == 'en' || old('default_lang') == 'en')selected="selected" @endif>{{ __('English') }}</option>
                </select>
              </div><!-- form-group -->
            </div><!-- col-12 -->
          </div><!-- row -->
          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('bills header and footer') }} <small class="d-inline-block text-secondary">( {{ __('optional') }} )</small></div>
          <div class="row">
            <div class="col-12 col-md-6">
              <div class="form-group mb-3">
                <label for="header_bill_ar" class="d-block mb-2">{{ __('Header ar') }}</label>
                <input class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="header_bill_ar" id="header_bill_ar" value="{{ old('header_bill_ar') ?? $user->settings->getTranslation('header_bill', 'ar') }}">
              </div><!-- form-group -->
            </div><!-- col-12 -->
            <div class="col-12 col-md-6">
              <div class="form-group mb-3" >
                <label for="header_bill_en" class="d-block mb-2">{{ __('Header en') }}</label>
                <input class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="header_bill_en" id="header_bill_en" value="{{ old('header_bill_en') ?? $user->settings->getTranslation('header_bill', 'en') }}">
              </div><!-- form-group -->
            </div><!-- col-12 -->
            <div class="col-12 col-md-6">
              <div class="form-group mb-3">
                <label for="footer_bill_ar" class="d-block mb-2">{{ __('Footer ar') }}</label>
                <input class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="footer_bill_ar" id="footer_bill_ar" value="{{ old('footer_bill_ar') ?? $user->settings->getTranslation('footer_bill', 'ar') }}">
              </div><!-- form-group -->
            </div><!-- col-12 -->
            <div class="col-12 col-md-6">
              <div class="form-group mb-3">
                <label for="footer_bill_en" class="d-block mb-2">{{ __('Footer en') }}</label>
                <input class="form-control shadow-none bg-white border w-100 rounded-3 text-body" name="footer_bill_en" id="footer_bill_en" value="{{ old('footer_bill_en') ?? $user->settings->getTranslation('footer_bill', 'en') }}">
              </div><!-- form-group -->
            </div><!-- col-12 -->
          </div><!-- row -->
          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('When Bill Created') }} <small class="d-inline-block text-secondary">( {{ __('Default settings') }} )</small></div>
          <div class="row">
            <div class="col-12 col-md-4">
              <label for="create_send_sms" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="create_send_sms" class="position-absolute top-0 strat-0 w-100 h-100" id="create_send_sms" type="checkbox" @if($user->settings->create_send_sms || old('create_send_sms') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Send a text message to the customer') }}
                </span>
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-md-4">
              <label for="create_send_email" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="create_send_email" class="position-absolute top-0 strat-0 w-100 h-100" id="create_send_email" type="checkbox" @if($user->settings->create_send_email || old('create_send_email') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Send an email to the customer') }}
                </span>
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-md-4">
              <label for="display_customer_details" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="display_customer_details" class="position-absolute top-0 strat-0 w-100 h-100" id="display_customer_details" type="checkbox" @if($user->settings->display_customer_details || old('display_customer_details') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Display Customer Details') }}
                </span>
              </label>
            </div><!-- col-12 -->
          </div><!-- row -->
          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('When Bill Paid') }}</div>
          <div class="row">
            <div class="col-12 col-md-6">
              <label for="paid_send_sms" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="paid_send_sms" class="position-absolute top-0 strat-0 w-100 h-100" id="paid_send_sms" type="checkbox" @if($user->settings->paid_send_sms || old('paid_send_sms') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Send me a text message') }}
                </span>
              </label>
            </div><!-- col-12 -->
            <div class="col-12 col-md-6">
              <label for="paid_send_email" class="checkboxItem position-relative mb-3 mb-md-0">
                <input name="paid_send_email" class="position-absolute top-0 strat-0 w-100 h-100" id="paid_send_email" type="checkbox" @if($user->settings->paid_send_email || old('paid_send_email') == 'on') checked @endif>
                <span class="d-flex align-items-center justify-content-start">
                  <i class="d-block rounded-pill position-relative"></i>
                  {{ __('Send an email to me') }}
                </span>
              </label>
            </div><!-- col-12 -->
          </div><!-- row -->
          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('Simple Style for API') }}</div>
          <label for="api_bill_style" class="checkboxItem position-relative mb-3 mb-md-0">
            <input name="api_bill_style" class="position-absolute top-0 strat-0 w-100 h-100" id="api_bill_style" type="checkbox" @if($user->settings->api_bill_style || old('api_bill_style') == 'on') checked @endif>
            <span class="d-flex align-items-center justify-content-start">
              <i class="d-block rounded-pill position-relative"></i>
              {{ __('Activate simple style for API bills?') }}
            </span>
          </label>
          <div class="saveBtn d-flex justify-content-start mt-5">
            <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold"> {{__('Save')}}</button>
          </div><!-- saveBtn -->
        </form>
      </div><!-- blockArea -->
    @endif
  </section><!-- invoiceSettingsPage -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/jquery-ui/jquery-ui.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script>
    $(document).ready(function () {

      if($("#Tax_Values_Checkbox").is(':checked')){
        $(".Tax_Values").show();
      }else{
        $(".Tax_Values").hide();
      }
      $('#Tax_Values_Checkbox').change(function() {
        $('.Tax_Values').slideToggle();
      });

      if($("#Tax_Invoice_Values_Checkbox").is(':checked')){
        $(".Tax_Invoice_Values").show();
      }else{
        $(".Tax_Invoice_Values").hide();
      }
      $('#Tax_Invoice_Values_Checkbox').change(function() {
        $('.Tax_Invoice_Values').slideToggle();
      });

      $('#arabic, #english').click(function() {
        toggleLangSelector()
      });
      toggleLangSelector()
    });
    function toggleLangSelector() {
      if($("#arabic").is(':checked') && $("#english").is(':checked')){
        $("#default_lang").show();
      }else{
        $("#default_lang").hide();  // To hide
      }
    }
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\SettingsRequest', '#settings') !!}
@endpush
