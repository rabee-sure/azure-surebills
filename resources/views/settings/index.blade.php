@extends('layouts.app')
@section('title', __('Invoice Settings'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('content')

  <h4 class="mb-1">{{ __('Invoice Settings')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('account') }}" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Invoice Settings') }}</li>
    </ol>
  </nav>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if($user->settings)
    <form method="POST" action="{{ route('post.settings') }}" id="settings" enctype="multipart/form-data" class="card">
      @csrf
      <div class="card-body">

        <h5 class="card-title mb-5">{{ __('Taxes') }}</h5>
        <div class="row g-4">
          <div class="col-12">
            <label for="Tax_Values_Checkbox" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="add_tax" id="Tax_Values_Checkbox" @if($user->settings->add_tax || old('add_tax') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Add Tax') }}</span>
            </label>
          </div><!-- col-12 -->
          <div class="col-12 Tax_Values" style="display: none;">
            <div class="row">
              <div class="col-12 col-md-6">
                <label for="Tax" class="form-label">{{ __('Tax Value') }}</label>
                <div class="input-group input-group-merge" dir="ltr">
                  <span class="input-group-text" id="percentage"><i class="icon-base ti ti-percentage"></i></span>
                  <input type="number" inputmode="numeric" name="tax_value" class="form-control" min="0" max="100" id="Value" value="{{ old('tax_value') ?? $user->settings->tax_value }}" aria-describedby="percentage">
                </div><!-- input-group -->
              </div><!-- col -->
            </div><!-- row -->
          </div><!-- col-12 -->
        </div><!-- row -->

        {{-- <hr class="my-5">

        <h5 class="card-title mb-5">{{ __('Tax Invoice') }}</h5>
        <div class="row g-4">
          <div class="col-12">
            <label for="Tax_Invoice_Values_Checkbox" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="add_tax_invoice" id="Tax_Invoice_Values_Checkbox" @if($user->settings->add_tax_invoice || old('add_tax_invoice') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Activate Tax Invoice') }}</span>
            </label>
          </div><!-- col-12 -->
          <div class="col-12 Tax_Invoice_Values">
            <div class="row row-cols-1 row-cols-md-2 g-4">
              <div class="col">
                <label for="bullding_no" class="form-label">{{__('Building Number')}}</label>
                <input name="bullding_no" type="text" class="form-control" id="bullding_no" placeholder="{{__('Building Number')}}"  value="@if($errors->any()){{old('bullding_no')}}@else{{ $user->bullding_no }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="street_name" class="form-label">{{__('Street Name')}}</label>
                <input name="street_name" type="text" class="form-control" id="street_name" placeholder="{{__('Street Name')}}"  value="@if($errors->any()){{old('street_name')}}@else{{ $user->street_name }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="district" class="form-label">{{__('District')}}</label>
                <input name="district" type="text" class="form-control" id="district" placeholder="{{__('District')}}"  value="@if($errors->any()){{old('district')}}@else{{ $user->district }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="city" class="form-label">{{__('City')}}</label>
                <input name="city" type="text" class="form-control" id="city" placeholder="{{__('City')}}"  value="@if($errors->any()){{old('city')}}@else{{ $user->city }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="postal_code" class="form-label">{{__('Postal Code')}}</label>
                <input name="postal_code" type="text" class="form-control" id="postal_code" placeholder="{{__('Postal Code')}}"  value="@if($errors->any()){{old('postal_code')}}@else{{ $user->postal_code }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="additional_no" class="form-label">{{__('Additional Number')}}</label>
                <input name="additional_no" type="text" class="form-control" id="additional_no" placeholder="{{__('Additional Number')}}"  value="@if($errors->any()){{old('additional_no')}}@else{{ $user->additional_no }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="other_buyer_id" class="form-label">{{__('Additional ID')}}</label>
                <input name="other_buyer_id" type="text" class="form-control" id="other_buyer_id" placeholder="{{__('Additional ID')}}"  value="@if($errors->any()){{old('other_buyer_id')}}@else{{ $user->other_buyer_id }}@endif">
              </div><!-- col -->
              <div class="col">
                <label for="vat_registration_number" class="form-label">{{ __('VAT Registration Number') }} ( {{ __('optional') }} )</label>
                <input value="@if($errors->any()){{old('vat_registration_number')}}@else{{ $user->vat_registration_number }}@endif" name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
              </div><!-- col -->
            </div><!-- row -->
          </div><!-- col-12 -->
        </div><!-- row --> --}}

        <hr class="my-5">

        <h5 class="card-title mb-5">{{ __('Default Language for Bills') }}</h5>
        <div class="row g-4 g-md-6">
          <div class="col-12 col-md-6">
            <label for="arabic" class="form-label mb-4">{{ __('Active Langs') }}</label>
            <div class="row row-cols-1 row-cols-md-2 g-4">
              <div class="col">
                <div class="form-check m-0">
                  <input class="form-check-input" type="checkbox" name="active_lang_ar" id="arabic" @if($user->settings->active_lang  == 'ar'|| $user->settings->active_lang  == 'all' || old('active_lang_ar') == 'on') checked @endif>
                  <label class="form-check-label" for="arabic">{{ __('Arabic') }}</label>
                </div>
              </div><!-- col -->
              <div class="col">
                <div class="form-check m-0">
                  <input class="form-check-input" type="checkbox" name="active_lang_en" id="english" @if($user->settings->active_lang  == 'en'|| $user->settings->active_lang  == 'all' || old('active_lang_en') == 'on') checked @endif>
                  <label class="form-check-label" for="english">{{ __('English') }}</label>
                </div>
              </div><!-- col -->
            </div><!-- row -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6" id="default_lang">
            <label for="default_lang" class="form-label">{{ __('Default Lang') }}</label>
            <select name="default_lang" id="default_lang" class="form-select select2" data-allow-clear="false" data-minimum-results-for-search="Infinity">
              <option value="ar" @if($user->settings->default_lang  == 'ar' || old('default_lang') == 'ar')selected="selected" @endif>{{ __('Arabic') }}</option>
              <option value="en" @if($user->settings->default_lang  == 'en' || old('default_lang') == 'en')selected="selected" @endif>{{ __('English') }}</option>
            </select>
          </div><!-- col-12 -->
        </div><!-- row -->

        <hr class="my-5">

        <h5 class="card-title mb-5 text-capitalize">{{ __('bills header and footer') }} <small class="d-inline-block text-secondary">( {{ __('optional') }} )</small></h5>
        <div class="row g-4 g-md-6">
          <div class="col-12 col-md-6">
            <label for="header_bill_ar" class="form-label">{{ __('Header ar') }}</label>
            <input class="form-control" name="header_bill_ar" id="header_bill_ar" value="{{ old('header_bill_ar') ?? $user->settings->getTranslation('header_bill', 'ar') }}">
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <label for="header_bill_en" class="form-label">{{ __('Header en') }}</label>
            <input class="form-control" name="header_bill_en" id="header_bill_en" value="{{ old('header_bill_en') ?? $user->settings->getTranslation('header_bill', 'en') }}">
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <label for="footer_bill_ar" class="form-label">{{ __('Footer ar') }}</label>
            <input class="form-control" name="footer_bill_ar" id="footer_bill_ar" value="{{ old('footer_bill_ar') ?? $user->settings->getTranslation('footer_bill', 'ar') }}">
          </div><!-- col-12 -->
          <div class="col-12 col-md-6">
            <label for="footer_bill_en" class="form-label">{{ __('Footer en') }}</label>
            <input class="form-control" name="footer_bill_en" id="footer_bill_en" value="{{ old('footer_bill_en') ?? $user->settings->getTranslation('footer_bill', 'en') }}">
          </div><!-- col-12 -->
        </div><!-- row -->

        <hr class="my-5">

        <h5 class="card-title mb-5">{{ __('When Bill Created') }} <small class="d-inline-block text-secondary">( {{ __('Default settings') }} )</small></h5>
        <div class="row g-4 g-md-6">
          <div class="col-12 col-md-4">
            <label for="create_send_sms" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="create_send_sms" id="create_send_sms" @if($user->settings->create_send_sms || old('create_send_sms') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Send a text message to the customer') }}</span>
            </label>
          </div><!-- col-12 -->
          <div class="col-12 col-md-4">
            <label for="create_send_email" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="create_send_email" id="create_send_email" @if($user->settings->create_send_email || old('create_send_email') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Send an email to the customer') }}</span>
            </label>
          </div><!-- col-12 -->
          <div class="col-12 col-md-4">
            <label for="display_customer_details" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="display_customer_details" id="display_customer_details" @if($user->settings->display_customer_details || old('display_customer_details') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Display Customer Details') }}</span>
            </label>
          </div><!-- col-12 -->
        </div><!-- row -->

        <hr class="my-5">

        <h5 class="card-title mb-5">{{ __('When Bill Paid') }}</h5>
        <div class="row g-4 g-md-6">
          <div class="col-12 col-md-4">
            <label for="paid_send_sms" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="paid_send_sms" id="paid_send_sms" @if($user->settings->paid_send_sms || old('paid_send_sms') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Send me a text message') }}</span>
            </label>
          </div><!-- col-12 -->
          <div class="col-12 col-md-4">
            <label for="paid_send_email" class="switch switch-success switch-lg m-0">
              <input type="checkbox" class="switch-input" name="paid_send_email" id="paid_send_email" @if($user->settings->paid_send_email || old('paid_send_email') == 'on') checked @endif>
              <span class="switch-toggle-slider">
                <span class="switch-on">
                </span>
                <span class="switch-off">
                </span>
              </span>
              <span class="switch-label">{{ __('Send an email to me') }}</span>
            </label>
          </div><!-- col-12 -->
        </div><!-- row -->

        <hr class="my-5">

        <h5 class="card-title mb-5">{{ __('Simple Style for API') }}</h5>
        <label for="api_bill_style" class="switch switch-success switch-lg m-0">
          <input type="checkbox" class="switch-input" name="api_bill_style" id="api_bill_style" @if($user->settings->api_bill_style || old('api_bill_style') == 'on') checked @endif>
          <span class="switch-toggle-slider">
            <span class="switch-on">
            </span>
            <span class="switch-off">
            </span>
          </span>
          <span class="switch-label">{{ __('Hide the additional information of the orgianization in the payment URL') }}</span>
        </label>

        <hr class="my-5">

        <h5 class="card-title mb-5">{{ __('Bill UI Customization') }}</h5>
        <div class="row">
          <div class="col-12 col-md-6">

            <div class="mb-4">
              <label for="background_color_body" class="form-label">{{ __('Background Color') }}</label>
              <input type="color" name="background_color_body" id="background_color_body" class="form-control input-color" value="{{ $user->settings->background_color_body ?? '#fafafa' }}">
            </div><!-- mb-4 -->

            <div class="mb-4">
              <label for="background_image_file" class="form-label">{{ __('Background Image') }}</label>
                <div class="uploadFiledArea">
                  <div class="uploadInput">
                    <div class="fileName"></div>
                    <div class="fileBtn">{{ __('Choose file') }}</div>
                  </div><!-- uploadInput -->
                  <input name="background_image_file" type="file" id="background_image_file" class="position-absolute top-0 start-0 w-100 h-100 opacity-0 z- z-10" accept="image/png, image/jpeg, image/jpg">
                  <input type="hidden" name="hidden_background_image_file" class="d-none" value="{{ $user->settings->background_image_file }}" />
                </div><!-- uploadFiledArea -->
                <small class="text-secondary">{{ __('Maximum image size is 1MB') }}</small>
                @if($user->settings->background_image_file)
                  <div class="form-group mt-3">
                    <div class="logoImage card h-100 relative">
                      <img src="{{ bill_background_image_url($user->settings->background_image_file) }}" alt="background image" class="logo_image card-img-top rounded-3" />
                      <button type="button" class="btn btn-icon btn-danger waves-effect waves-light position-absolute top-0 end-0 m-2 delete-background-image" style="z-index: 10;" title="{{ __('Delete Image') }}">
                        <span class="icon-base ti ti-trash icon-22px"></span>
                      </button>
                    </div><!-- logoImage -->
                    <input type="hidden" name="delete_background_image" id="delete_background_image" value="0">
                  </div><!-- form-group -->
                  <!-- Delete Confirmation Modal -->
                  <div class="modal fade" id="deleteImageModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog" role="document">
                      <div class="modal-content">
                        <div class="modal-header">
                          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div><!-- modal-header -->
                        <div class="modal-body">
                          <div class="d-flex align-items-center justify-content-center text-warning mb-3">
                            <i class="icon-base ti ti-info-triangle icon-50px"></i>
                          </div>
                          <h5 class="m-0 text-center">{{ __("Are you sure you want to delete this image?") }}</h5>
                        </div><!-- modal-body -->
                        <div class="modal-footer">
                          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
                          <button type="submit" class="btn btn-danger btn-submit-with-spinner" id="confirmDeleteBtn" data-loading-text="{{ __('Deleting...') }}">
                            <span class="btn-spinner d-none me-2" role="status">
                              <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
                            </span>
                            <span class="btn-text">{{__('Delete')}}</span>
                          </button>
                        </div><!-- modal-footer -->
                      </div><!-- modal-content -->
                    </div>
                  </div><!-- modal -->
                  <!-- Delete Confirmation Modal -->
                @endif
            </div><!-- mb-4 -->

            <div class="mb-4">
              <label for="text_color_body" class="form-label">{{ __('Text Color') }}</label>
              <input type="color" name="text_color_body" id="text_color_body" class="form-control input-color" value="{{ $user->settings->text_color_body ?? '#000000' }}">
            </div>

            <div class="mb-4">
              <label for="background_color_payment_button" class="form-label">{{ __('Payment Button Background Color') }}</label>
              <input type="color" name="background_color_payment_button" id="background_color_payment_button" class="form-control input-color" value="{{ $user->settings->background_color_payment_button ?? '#00d595' }}">
            </div><!-- mb-4 -->

            <div>
              <label for="text_color_payment_button" class="form-label">{{ __('Payment Button Text Color') }}</label>
              <input type="color" name="text_color_payment_button" id="text_color_payment_button" class="form-control input-color" value="{{ $user->settings->text_color_payment_button ?? '#ffffff' }}">
            </div><!-- mb-4 -->


          </div><!-- col-12 -->
        </div><!-- row -->

      </div><!-- card-body -->
      <div class="card-footer d-flex align-items-center justify-content-end">
        <button type="submit" class="btn btn-primary btn-submit-with-spinner" data-loading-text="{{ __('Saving...') }}">
          <span class="btn-spinner d-none me-2" role="status">
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
          </span>
          <span class="btn-text">{{__('Save')}}</span>
        </button>
      </div><!-- card-footer -->

    </form>
  @endif

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\SettingsRequest', '#settings') !!}

  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
      // Submit button spinner for settings form
      setTimeout(function() {
        const form = document.getElementById('settings');
        if (!form) return;

        form.addEventListener('submit', function(e) {
          if (e.defaultPrevented) return;
          const submitter = e.submitter || form.querySelector('button[type="submit"].btn-primary');
          if (submitter && submitter.classList.contains('btn-submit-with-spinner') && !submitter.disabled) {
            const btnText = submitter.querySelector('.btn-text');
            const btnSpinner = submitter.querySelector('.btn-spinner');
            const originalText = btnText ? btnText.textContent : (submitter.classList.contains('btn-danger') ? '{{ __("Delete") }}' : '{{ __("Save") }}');
            submitter.disabled = true;
            if (btnText && btnSpinner) {
              btnText.textContent = submitter.dataset.loadingText || 'Saving...';
              btnSpinner.classList.remove('d-none');
            }
            setTimeout(function() {
              submitter.disabled = false;
              if (btnText && btnSpinner) {
                btnText.textContent = originalText;
                btnSpinner.classList.add('d-none');
              }
            }, 8000);
          }
        });

        $('#settings').on('invalid-form.validate', function() {
          form.querySelectorAll('.btn-submit-with-spinner').forEach(function(btn) {
            btn.disabled = false;
            const btnText = btn.querySelector('.btn-text');
            const btnSpinner = btn.querySelector('.btn-spinner');
            if (btnText && btnSpinner) {
              btnText.textContent = btn.classList.contains('btn-danger') ? '{{ __("Delete") }}' : '{{ __("Save") }}';
              btnSpinner.classList.add('d-none');
            }
          });
        });
      }, 100);

      // Select2
      $('.select2').select2();

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
      $('#background_image_file').bind('change', function () {
        var filename = $("#background_image_file").val();
        if (/^\s*$/.test(filename)) {
          $(".fileName").text("No file chosen...");
        }
        else {
          $(".fileName").text(filename.replace("C:\\fakepath\\", ""));
        }
      });

    });
    function toggleLangSelector() {
      if($("#arabic").is(':checked') && $("#english").is(':checked')){
        $("#default_lang").show();
      }else{
        $("#default_lang").hide();
      }
    }
    function previewBackgroundImage() {
      const backgroundImage = document.getElementById('background_image_file').value;
      const previewImage = document.getElementById('preview_background_image');
      previewImage.src = backgroundImage;
    }
  </script>
@endpush
