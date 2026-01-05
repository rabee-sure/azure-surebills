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
        <form method="POST" action="{{ route('post.settings') }}" class="repeater" id="settings" enctype="multipart/form-data">
          @csrf
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('Taxes') }}</div>
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
                {{-- <input name="add_tax_invoice" class="position-absolute top-0 strat-0 w-100 h-100" id="Tax_Invoice_Values_Checkbox" type="checkbox" @if($user->settings->add_tax_invoice || old('add_tax_invoice') == 'on') checked @endif> --}}
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
                    <label for="bullding_no" class="d-block mb-2">{{__('Building Number')}}</label>
                    <input name="bullding_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('Building Number')}}"  value="@if($errors->any()){{old('bullding_no')}}@else{{ $user->bullding_no }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="street_name" class="d-block mb-2">{{__('Street Name')}}</label>
                    <input name="street_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('Street Name')}}"  value="@if($errors->any()){{old('street_name')}}@else{{ $user->street_name }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="district" class="d-block mb-2">{{__('District')}}</label>
                    <input name="district" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('District')}}"  value="@if($errors->any()){{old('district')}}@else{{ $user->district }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="city" class="d-block mb-2">{{__('City')}}</label>
                    <input name="city" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('City')}}"  value="@if($errors->any()){{old('city')}}@else{{ $user->city }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="postal_code" class="d-block mb-2">{{__('Postal Code')}}</label>
                    <input name="postal_code" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('Postal Code')}}"  value="@if($errors->any()){{old('postal_code')}}@else{{ $user->postal_code }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="additional_no" class="d-block mb-2">{{__('Additional Number')}}</label>
                    <input name="additional_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('Additional Number')}}"  value="@if($errors->any()){{old('additional_no')}}@else{{ $user->additional_no }}@endif">
                  </div><!-- form-group -->
                </div><!-- col-12 -->
                <div class="col-12 col-md-6">
                  <div class="form-group mb-3">
                    <label for="other_buyer_id" class="d-block mb-2">{{__('Additional ID')}}</label>
                    <input name="other_buyer_id" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('Additional ID')}}"  value="@if($errors->any()){{old('other_buyer_id')}}@else{{ $user->other_buyer_id }}@endif">
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
              {{ __('Hide the additional information of the orgianization in the payment URL') }}
            </span>
          </label>

          <hr>
          <div class="name d-block mb-4 fw-bold fs-6">{{ __('Bill UI Customization') }}</div>
          <div class="row mb-3">
            <label for="background_color_body" class="col-sm-2 col-form-label">{{ __('Background Color') }}</label>
            <div class="col-sm-4">
              <input type="color" name="background_color_body" id="background_color_body" class="color_input w-100" value="{{ $user->settings->background_color_body ?? '#fafafa' }}">
            </div>
          </div>
          <div class="row mb-3">
            <label for="background_color_body" class="col-sm-2 col-form-label">{{ __('Background Image') }}</label>
            <div class="col-sm-4">
              <input type="file" name="background_image_file" id="background_image_file" accept="image/*" class="form-control shadow-none bg-white border w-100 rounded-3" accept="image/png, image/jpeg, image/jpg">
              <small class="text-secondary">{{ __('Maximum image size is 1MB') }}</small>
              @if($user->settings->background_image_file)
                <div class="form-group mt-3">
                  <div class="logoImage p-2 border overflow-hidden rounded-3 position-relative d-flex align-items-center justify-content-center">
                    <img src="{{ $user->settings->background_image_file }}" alt="background image" class="logo_image mw-100 mh-100" />
                    <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-2 delete-background-image" style="z-index: 10;" title="{{ __('Delete Image') }}">
                      <i class="fas fa-trash-alt"></i>
                    </button>
                  </div><!-- logoImage -->
                  <input type="hidden" name="delete_background_image" id="delete_background_image" value="0">
                </div><!-- form-group -->
                <!-- Delete Confirmation Modal -->
                <div class="modal fade deleteCustomerModal" id="deleteImageModal" tabindex="-1" aria-labelledby="deleteImageModalLabel" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content border-0 shadow-sm rounded-3">
                      <div class="modal-body d-flex align-items-center justify-content-center flex-column">
                        <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
                          <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
                        </div><!-- closeBtn -->
                        <span class="d-block text-center text-body mb-4 fs-5 text-break text-wrap">{{ __("Are you sure you want to delete this image?") }}</span>
                        <div class="d-flex align-items-center justify-content-center gap-3 form w-100">
                          <button type="button" class="border-0 shadow-none rounded-3 btn-danger formBtn mx-2" id="confirmDeleteBtn">{{__('Delete')}}</button>
                          <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{__('Close')}}</button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Delete Confirmation Modal -->
              @endif
            </div>
          </div>
          <div class="row mb-3">
            <label for="text_color_body" class="col-sm-2 col-form-label">{{ __('Text Color') }}</label>
            <div class="col-sm-4">
              <input type="color" name="text_color_body" id="text_color_body" class="color_input w-100" value="{{ $user->settings->text_color_body ?? '#000000' }}">
            </div>
          </div>
          <div class="row mb-3">
            <label for="background_color_body" class="col-sm-2 col-form-label">{{ __('Payment Button Background Color') }}</label>
            <div class="col-sm-4">
              <input type="color" name="background_color_payment_button" id="background_color_payment_button" class="color_input w-100" value="{{ $user->settings->background_color_payment_button ?? '#00d595' }}">
            </div>
          </div>
          <div class="row mb-3">
            <label for="text_color_payment_button" class="col-sm-2 col-form-label">{{ __('Payment Button Text Color') }}</label>
            <div class="col-sm-4">
              <input type="color" name="text_color_payment_button" id="text_color_payment_button" class="color_input w-100" value="{{ $user->settings->text_color_payment_button ?? '#ffffff' }}">
            </div>
          </div>


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

      // Delete background image
 $('.delete-background-image').click(function() {
  // تخزين العنصر الحالي لاستخدامه لاحقًا
  var currentButton = $(this);
  var formGroup = $(this).closest('.form-group');

  // عرض الـ Modal
  $('#deleteImageModal').modal('show');

  // عند الضغط على زر الحذف في الـ Modal
  $('#confirmDeleteBtn').off('click').on('click', function() {
    // تنفيذ عملية الحذف
    $('#delete_background_image').val('1');
    formGroup.hide();

    // إغلاق الـ Modal
    $('#deleteImageModal').modal('hide');
  });
});

    });
    function toggleLangSelector() {
      if($("#arabic").is(':checked') && $("#english").is(':checked')){
        $("#default_lang").show();
      }else{
        $("#default_lang").hide();  // To hide
      }
    }
    function previewBackgroundImage() {
      const backgroundImage = document.getElementById('background_image_file').value;
      const previewImage = document.getElementById('preview_background_image');
      previewImage.src = backgroundImage;
    }
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\SettingsRequest', '#settings') !!}
@endpush
