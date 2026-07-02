@extends('layouts.app')

@section('title', __('Create a bill'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/jquery-ui/jquery-ui.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('content')

  <h4 class="mb-1">{{ __('Create a bill')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('/bills') }}" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Create a bill') }}</li>
    </ol>
  </nav>

  @if ($errors->any())
    <div class="mb-6">
      <ul class="list-group">
        @foreach ($errors->all() as $error)
          <li class="list-group-item list-group-item-danger">
            @if ($error == 'less_than')
              <div class="d-flex align-items-center justify-content-start gap-1">
                {{ __('Invoice total is less than') }}
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                  2 <i class="sar-icon"></i>
                </span>
              </div>
            @elseif ($error == 'more_than')
              <div class="d-flex align-items-center justify-content-start gap-1">
                {{ __('Invoice total is more than') }}
                <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                  {{config('bill.max_total_amount')}} <i class="sar-icon"></i>
                </span>
              </div>
            @else
                {{ $error }}
            @endif
          </li>
        @endforeach
      </ul>
    </div><!-- alert -->
  @endif

  <form method="POST" action="{{ route('bills.store') }}" class="form-repeater card" id="bill_create">
    @csrf
    <div class="card-body">
      <div class="row g-6">
        <div class="col-12 col-md-6 col-lg-4">
          <label for="customer_name" class="form-label">{{ __('Customer Name') }} <span class="requirement text-danger">*</span></label>
          <input value="{{ old('customer_name') }}" name="customer_name" type="text" class="form-control" id="customer_name" placeholder="{{ __('Customer Name') }} *" autocomplete="off">
        </div><!-- col -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="customer_mobile" class="form-label">{{ __('Mobile Number') }} <span class="requirement text-danger">*</span></label>
          <input name="customer_mobile" type="tel" class="form-control @error('customer_mobile') is-invalid @enderror" id="customer_mobile" placeholder="5XXXXXXXX" value="{{ old('customer_mobile') }}"  pattern="[0-9]*" maxlength="9" inputmod="numaric" autocomplete="off">
          @error('customer_mobile')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </div><!-- col -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="customer_email" class="form-label">{{ __('Email') }}</label>
          <input value="{{ old('customer_email') }}" name="customer_email" type="email" class="form-control @error('customer_email') is-invalid @enderror" id="customer_email" inputmode="email" placeholder="{{ __('Email') }}" autocomplete="off">
          @error('customer_email')
            <div class="invalid-feedback text-danger" role="alert">{{ $message }}</div>
          @enderror
        </div><!-- col -->
          <div class="col-12 col-md-6 col-lg-4">
          <label for="customer_notes" class="form-label">{{ __('Special Note') }}</label>
          <input value="{{ old('customer_notes') }}" name="customer_notes" type="text" class="form-control" id="customer_notes" placeholder="{{ __('Special Note') }}" autocomplete="off">
        </div><!-- col -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="due_date" class="form-label">{{ __('Due Date') }}</label>
          <input value="{{ Carbon\Carbon::now()->format('d/m/y') }}" name="due_date" id="due_date" class="form-control flatpickr" placeholder="{{ __('Due Date') }}" autocomplete="off">
        </div><!-- col -->
        <div class="col-12 col-md-6 col-lg-4">
          <label for="expiry_date" class="d-block mb-2">{{ __('Expiry Time') }}</label>
          @if(config('bills.pay_page_expiration_time_type') == 'Days')
            <select value="{{ old('expiry_date') }}" name="expiry_date" id="expiry_date" class="form-select select2">
              @for ($i = 1; $i <= config('bills.pay_page_expiration_time'); $i++)
                <option value="{{ $i }}" @if(old('expiry_date') == $i) selected="selected" @endif>{{ $i }} {{ __('Day') }}</option>
              @endfor
            </select>
          @elseif(config('bills.pay_page_expiration_time_type') == 'Hours')
            <select value="{{ old('expiry_hours') }}" name="expiry_hours" id="expiry_hours" class="form-select select2">
              @for ($i = 1; $i <= config('bills.pay_page_expiration_time'); $i++)
                <option value="{{ $i }}" @if(old('expiry_hours') == $i) selected="selected" @endif>{{ $i }} {{ __('Hour') }}</option>
              @endfor
            </select>
          @elseif(config('bills.pay_page_expiration_time_type') == 'Minutes')
            <select value="{{ old('expiry_minutes') }}" name="expiry_minutes" id="expiry_minutes" class="form-select select2">
              @for ($i = 1; $i <= config('bills.pay_page_expiration_time'); $i++)
                <option value="{{ $i }}" @if(old('expiry_minutes') == $i) selected="selected" @endif>{{ $i }} {{ __('Minute') }}</option>
              @endfor
            </select>
          @endif
        </div><!-- col -->
      </div><!-- row -->

      @if($settings->add_tax_invoice)
        <button type="button" class="additionalInformationBtn border-0 d-flex align-items-center justify-content-start bg-transparent p-0">{{__('Additional Information')}}</button>
        <div class="additionalInformationArea">
          <div class="pt-3">
            <div class="row">
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="bullding_no" class="d-block mb-2">{{__('Building Number')}}</label>
                  <input value="{{ old('bullding_no') }}" name="bullding_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('Building Number')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="street_name" class="d-block mb-2">{{__('Street Name')}}</label>
                  <input value="{{ old('street_name') }}" name="street_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('Street Name')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="district" class="d-block mb-2">{{__('District')}}</label>
                  <input value="{{ old('district') }}" name="district" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('District')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="city" class="d-block mb-2">{{__('City')}}</label>
                  <input value="{{ old('city') }}" name="city" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('City')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="postal_code" class="d-block mb-2">{{__('Postal Code')}}</label>
                  <input value="{{ old('postal_code') }}" name="postal_code" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('Postal Code')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="additional_no" class="d-block mb-2">{{__('Additional Number')}}</label>
                  <input value="{{ old('additional_no') }}"  name="additional_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('Additional Number')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="other_buyer_id" class="d-block mb-2">{{__('Additional ID')}}</label>
                  <input value="{{ old('other_buyer_id') }}"  name="other_buyer_id" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('Additional ID')}}">
                </div><!-- form-group -->
              </div><!-- col -->
              <div class="col-12 col-md-6 col-lg-4">
                <div class="form-group mb-3">
                  <label for="vat_registration_number" class="d-block mb-2">{{__('VAT Registration Number (optional)')}}</label>
                  <input value="{{ old('vat_registration_number') }}"  name="vat_registration_number" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="vat_registration_number" placeholder="{{__('VAT Registration Number (optional)')}}">
                </div><!-- form-group -->
              </div><!-- col -->
            </div><!-- row -->
          </div><!-- pt-3 -->
        </div><!-- additionalInformationArea -->
      @endif

      <hr class="my-5">

      <div class="d-flex align-items-center justify-content-between mb-5">
        <h5 class="card-title m-0">{{ __('Bill items') }}</h5>
        <button type="button" class="btn btn-primary btn-sm waves-effect waves-light" data-repeater-create>
          <span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Add Item') }}
        </button>
      </div>
      <div class="inner-repeater">
        <div data-repeater-list="items">
          @if(old('items'))
            @foreach( old('items') as $item)
              <div data-repeater-item>
                <div class="row g-5">
                  <div class="col-lg-6 col-xl-3 col-12">
                    <label class="form-label" for="name">{{ __('Product/Service') }} <span class="requirement text-danger">*</span></label>
                    <input type="text" id="name" class="form-control" name="name" value="{{$item['name']}}" placeholder="{{ __('Name') }}" />
                  </div><!-- col -->
                  <div class="col-lg-6 col-xl-3 col-12">
                    <label class="form-label" for="price">{{ __('Product/Service Price') }} <span class="requirement text-danger">*</span></label>
                    <input type="number" class="form-control qty1 product_price" id="price" name="price" value="{{$item['price']}}" placeholder="{{ __('Price') }}"  min="1" />
                  </div><!-- col -->
                  <div class="col-lg-6 col-xl-2 col-12">
                    <label class="form-label" for="quantity">{{ __('Quantity') }} <span class="requirement text-danger">*</span></label>
                    <input type="number" class="form-control qty1 product_quantity" id="quantity" name="quantity" value="{{$item['quantity']}}" placeholder="{{ __('Quantity') }}" min="1" />
                  </div><!-- col -->
                  <div class="col-lg-6 col-xl-2 col-12">
                    <label class="form-label" for="total">{{ __('Total') }}</label>
                    <input type="tel" name="total" value="{{ $item['price']* $item['quantity']}}" class="form-control" id="total" disabled>
                  </div><!-- col -->
                  <div class="col-lg-12 col-xl-2 col-12 d-flex align-items-center">
                    <button type="button" class="btn btn-label-danger mt-xl-6" data-repeater-delete>
                      <i class="icon-base ti ti-trash me-1"></i>
                      <span class="align-middle">{{ __('Delete') }}</span>
                    </button>
                  </div><!-- col -->
                </div><!-- row -->
                <hr class="my-5" />
              </div>
            @endforeach
          @else
            <div data-repeater-item>
              <div class="row g-5">
                <div class="col-lg-6 col-xl-3 col-12">
                  <label class="form-label" for="name">{{ __('Product/Service') }} <span class="requirement text-danger">*</span></label>
                  <input name="name" type="text" class="form-control" placeholder="{{ __('Name') }}" />
                </div><!-- col -->
                <div class="col-lg-6 col-xl-3 col-12">
                  <label class="form-label" for="price">{{ __('Product/Service Price') }} <span class="requirement text-danger">*</span></label>
                  <input name="price" type="number" min="1" class="form-control qty1 product_price" placeholder="{{ __('Price') }}" />
                </div><!-- col -->
                <div class="col-lg-6 col-xl-2 col-12">
                  <label class="form-label" for="quantity">{{ __('Quantity') }} <span class="requirement text-danger">*</span></label>
                  <input name="quantity" type="number" min="1" class="form-control qty1 product_quantity" placeholder="{{ __('Quantity') }}" />
                </div><!-- col -->
                <div class="col-lg-6 col-xl-2 col-12">
                  <label class="form-label" for="total">{{ __('Total') }}</label>
                  <input name="total" type="number" class="form-control fw-bold" disabled>
                </div><!-- col -->
                <div class="col-lg-12 col-xl-2 col-12 d-flex align-items-center">
                  <button type="button" class="btn btn-label-danger mt-xl-6" data-repeater-delete>
                    <i class="icon-base ti ti-trash me-1"></i>
                    <span class="align-middle">{{ __('Delete') }}</span>
                  </button>
                </div><!-- col -->
              </div><!-- row -->
              <hr class="my-5" />
            </div>
          @endif
        </div>
      </div><!-- inner-repeater -->

      <h5 class="card-title mb-5">{{ __('Additonal Details') }}</h5>
      <div class="row g-6">
        <div class="col-12">
          <label for="coupon_code" class="form-label">{{ __('Coupon Code') }} <small class="text-muted">( {{ __('Optional') }} )</small></label>
          <div class="row">
            <div class="col-12 col-md-6">
              <input type="text" name="coupon_code" id="coupon_code" class="form-control" value="{{ old('coupon_code') }}" placeholder="{{ __('Enter coupon code') }}" aria-describedby="couponCodeHelp">
              @error('coupon_code')
                <div class="text-danger small mt-1">{{ $message }}</div>
              @enderror
              <div id="couponCodeHelp" class="form-text">
                {{ __('If you have a coupon code, enter it here. The discount will be applied automatically.') }}
              </div><!-- form-text -->
            </div><!-- col -->
          </div><!-- row -->
        </div><!-- col -->
        <div class="col-12 col-md-6">
          <label for="Discount_Values_Checkbox" class="switch switch-lg m-0">
            <input type="checkbox" class="switch-input" name="add_discount" id="Discount_Values_Checkbox" @if(old('add_discount')) checked @endif>
            <span class="switch-toggle-slider">
              <span class="switch-on">
                <i class="icon-base ti ti-check"></i>
              </span>
              <span class="switch-off">
                <i class="icon-base ti ti-x"></i>
              </span>
            </span>
            <span class="switch-label">{{ __('Add Discount') }}</span>
          </label>
          <div class="Discount_Values" style="display: none;">
            <div class="row py-3">
              <div class="col-6">
                <label for="discount_type" class="form-label">{{ __('Discount type') }}</label>
                <select name="discount_type" id="discount_type" class="form-select select2" data-allow-clear="false" data-minimum-results-for-search="Infinity">
                  <option value="fixed" @if(old('discount_type') == 'fixed') selected @endif> {{ __('fixed') }}</option>
                  <option value="percentage" @if(old('discount_type') == 'percentage') selected @endif>{{ __('Percentage Discount (%)') }}</option>
                </select>
              </div><!-- col-6 -->
              <div class="col-6">
                <label for="fixed" class="form-label">{{ __('Discount Value') }}</label>
                <div class="input-group input-group-merge" dir="ltr">
                  <span class="input-group-text">
                    <i id="fixed" class="sar-icon lh-1"></i>
                    <i id="percentage" class="icon-base ti ti-percentage"></i>
                  </span>
                  <input type="number" inputmode="numeric" name="discount_value" class="form-control" value="{{old('discount_value')}}" id="Discount_Value" aria-describedby="fixed, percentage" />
                </div><!-- input-group -->
              </div><!-- col-6 -->
            </div><!-- row -->
          </div><!-- Discount_Values -->
        </div><!-- col -->
        <div class="col-12 col-md-6">
          <label for="Tax_Values_Checkbox" class="switch switch-lg m-0">
            <input type="checkbox" class="switch-input" name="add_tax" id="Tax_Values_Checkbox" @if($errors->any()) @if(old('add_tax') == true) checked @endif @else @if($settings->add_tax) checked @endif @endif>
            <span class="switch-toggle-slider">
              <span class="switch-on">
                <i class="icon-base ti ti-check"></i>
              </span>
              <span class="switch-off">
                <i class="icon-base ti ti-x"></i>
              </span>
            </span>
            <span class="switch-label">{{ __('Add Tax') }}</span>
          </label>
          <div class="Tax_Values" style="display: none;">
            <div class="row py-3">
              <div class="col-12 col-md-6">
                <label for="Value" class="form-label">{{ __('Tax Value') }}</label>
                <div class="input-group input-group-merge" dir="ltr">
                  <span class="input-group-text"><i class="icon-base ti ti-percentage"></i></span>
                  <input type="number" inputmode="numeric" name="tax_value" class="form-control" value="@if($settings->add_tax){{$settings->tax_value}}@else{{old('tax_value')}}@endif" id="Value" aria-describedby="Value" />
                </div><!-- input-group -->
              </div><!-- col-12 -->
            </div><!-- row -->
          </div><!-- Tax_Values -->
        </div><!-- col -->
      </div><!-- row -->

      <hr class="my-5" />

      <h5 class="card-title mb-5">{{ __('Send The Bill To Customer') }}</h5>
      <div class="row g-5">
        <div class="col-12 col-lg-6">
          <label for="send_sms" class="switch switch-lg m-0">
            <input type="checkbox" class="switch-input" name="send_sms" id="send_sms" @if($settings->create_send_sms || old('send_sms')) checked @endif>
            <span class="switch-toggle-slider">
              <span class="switch-on">
                <i class="icon-base ti ti-check"></i>
              </span>
              <span class="switch-off">
                <i class="icon-base ti ti-x"></i>
              </span>
            </span>
            <span class="switch-label">{{ __('Send SMS') }}</span>
          </label>
        </div><!-- col-12 -->
        <div class="col-12 col-lg-6">
          <label for="send_email" class="switch switch-lg m-0">
            <input type="checkbox" class="switch-input" name="send_email" id="send_email" @if($settings->create_send_email || old('send_email')) checked @endif>
            <span class="switch-toggle-slider">
              <span class="switch-on">
                <i class="icon-base ti ti-check"></i>
              </span>
              <span class="switch-off">
                <i class="icon-base ti ti-x"></i>
              </span>
            </span>
            <span class="switch-label">{{ __('Send Email') }}</span>
          </label>
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- card-body -->
    <div class="card-footer d-flex align-items-center justify-content-end">
      <button type="submit" id="create-bill" class="btn btn-primary btn-submit-with-spinner waves-effect waves-light" data-loading-text="{{__('Sending...')}}">
        <span class="btn-spinner d-none me-2" role="status">
          <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
        </span>
        <span class="btn-text">{{__('Send')}}</span>
      </button>
    </div><!-- card-footer -->
  </form>

  <div class="modal fade" id="deleteItemModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="d-flex align-items-center justify-content-center text-warning mb-3">
            <i class="icon-base ti ti-info-triangle icon-50px"></i>
          </div>
          <h5 class="m-0 text-center">{{ __('Are you sure you want to delete this element?') }}</h5>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{ __('Cancel') }}</button>
          <button type="button" class="btn btn-danger" id="confirmDeleteItem">{{ __('Delete') }}</button>
        </div>
      </div>
    </div>
  </div>

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript" src="{{ asset('assets/v2/vendor/libs/jquery-repeater/jquery-repeater.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('new/js/jquery-ui/jquery-ui.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script type="text/javascript">
    $(document).ready(function() {

      // Flatpickr
      $(".flatpickr").flatpickr({
        dateFormat: "d/m/Y",
        locale: {
          weekdays: {
            shorthand: [
              '{{ __("Sun") }}',
              '{{ __("Mon") }}',
              '{{ __("Tue") }}',
              '{{ __("Wed") }}',
              '{{ __("Thu") }}',
              '{{ __("Fri") }}',
              '{{ __("Sat") }}'
            ],
            longhand: [
              '{{ __("Sunday") }}',
              '{{ __("Monday") }}',
              '{{ __("Tuesday") }}',
              '{{ __("Wednesday") }}',
              '{{ __("Thursday") }}',
              '{{ __("Friday") }}',
              '{{ __("Saturday") }}'
            ]
          },
          months: {
            shorthand: [
              '{{ __("January") }}',
              '{{ __("February") }}',
              '{{ __("March") }}',
              '{{ __("April") }}',
              '{{ __("May") }}',
              '{{ __("June") }}',
              '{{ __("July") }}',
              '{{ __("August") }}',
              '{{ __("September") }}',
              '{{ __("October") }}',
              '{{ __("November") }}',
              '{{ __("December") }}'
            ],
            longhand: [
              '{{ __("January") }}',
              '{{ __("February") }}',
              '{{ __("March") }}',
              '{{ __("April") }}',
              '{{ __("May") }}',
              '{{ __("June") }}',
              '{{ __("July") }}',
              '{{ __("August") }}',
              '{{ __("September") }}',
              '{{ __("October") }}',
              '{{ __("November") }}',
              '{{ __("December") }}'
            ]
          },
          firstDayOfWeek: {{ app()->getLocale() == 'ar' ? 6 : 0 }},
          rangeSeparator: "{{ __("to") }}",
          weekAbbreviation: "{{ __("week") }}"
        }
      });

      // Select2
      $('.select2').select2();

      var pendingDeleteElement = null;
      var pendingDeleteRow = null;

      $('.form-repeater').repeater({
        initEmpty: false,
        show: function () {
          $(this).slideDown();
        },
        hide: function (deleteElement) {
          pendingDeleteElement = deleteElement;
          pendingDeleteRow = $(this);
          var deleteModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));
          deleteModal.show();
        },
        isFirstItemUndeletable: true
      });

      $('#confirmDeleteItem').on('click', function() {
        if (pendingDeleteRow && pendingDeleteElement) {
          pendingDeleteRow.slideUp(pendingDeleteElement);
          pendingDeleteRow = null;
          pendingDeleteElement = null;
        }
        bootstrap.Modal.getInstance(document.getElementById('deleteItemModal')).hide();
      });
    });





    // Additional Information
    $(".additionalInformationArea").hide();
    $("button.additionalInformationBtn").click(function(){
      $(this).toggleClass("show");
      $(".additionalInformationArea").slideToggle();
    });

    document.addEventListener('DOMContentLoaded', function() {
      setTimeout(function() {
        var form = document.getElementById('bill_create');
        if (!form) return;
        var btn = form.querySelector('.btn-submit-with-spinner');
        if (!btn) return;
        var btnText = btn.querySelector('.btn-text');
        var btnSpinner = btn.querySelector('.btn-spinner');
        var originalText = btnText ? btnText.textContent.trim() : '{{ __("Send") }}';

        function showSpinner() {
          btn.disabled = true;
          if (btnText && btnSpinner) {
            btnText.textContent = btn.dataset.loadingText || '{{ __("Sending...") }}';
            btnSpinner.classList.remove('d-none');
          }
        }

        function resetButton() {
          btn.disabled = false;
          if (btnText && btnSpinner) {
            btnText.textContent = originalText;
            btnSpinner.classList.add('d-none');
          }
        }

        form.addEventListener('submit', function(e) {
          if (btn.disabled) return;
          if (e.defaultPrevented) return;
          showSpinner();
          setTimeout(resetButton, 8000);
        }, false);

        $(form).on('invalid-form.validate', resetButton);
      }, 150);
    });

    $(document).on("change", ".qty1", function() {
      var name = $(this).attr('name');
      var res = name.replace("[price]", "");
      res = res.replace("[quantity]", "");
      var quantity_st  = 'input[name="'+res+ '[quantity]"]';
      var total_st  = 'input[name="'+res+ '[total]"]';
      var price_st  = 'input[name="'+res+ '[price]"]';
      var quantity = 1;
      if($(quantity_st).val() == ''){
        $(quantity_st).val(1);
      }else{
        quantity = $(quantity_st).val();
      }
      var price = $(price_st).val() == '' ? 0 :$(price_st).val();
      $(total_st).val(price * quantity);
    });

    $(document).ready(function () {
      if($('#Discount_Values_Checkbox').prop('checked')){
        $('.Discount_Values').show();
        if($('#discount_type').val() === 'percentage') {
          $('#percentage').show();
          $('#fixed').hide();
        } else {
          $('#percentage').hide();
          $('#fixed').show();
        }
      }else{
        $('.Discount_Values').hide();
      }

      if($('#Tax_Values_Checkbox').prop('checked')){
        $('.Tax_Values').show();
      }else{
        $('.Tax_Values').hide();
      }

      // Tax & Discount
      $('#Discount_Values_Checkbox').change(function() {
        $('.Discount_Values').slideToggle();
        if($('#discount_type').val() === 'percentage') {
          $('#percentage').show();
          $('#fixed').hide();
        } else {
          $('#percentage').hide();
          $('#fixed').show();
        }
      });
      $('#discount_type').change(function() {
        if($('#discount_type').val() === 'percentage') {
          $('#percentage').show();
          $('#fixed').hide();
        } else {
          $('#percentage').hide();
          $('#fixed').show();
        }
      });

      $('#Tax_Values_Checkbox').change(function() {
        $('.Tax_Values').slideToggle();
      });

      var customers = [];

      $( "#customer_name").autocomplete({
        source: function(request, response) {
          $.ajax({
            url: "{{route('customers.search_name')}}",
            data: {
              // _token: CSRF_TOKEN,
              search : request.term
              },
            dataType: "json",
            success: function(data){
              customers = data;
              var resp = $.map(data,function(obj){
                return {'value': obj.name, 'label': obj.name, 'id': obj.id};
              });
              response(resp);
            }
          });
        },
        select: function (event, ui) {
          var item = customers.find(x => x.id === ui.item.id);
          $('#customer_name').val(item.name);
          $('#customer_mobile').val(item.mobile);
          $('#customer_email').val(item.email);
          $('#customer_notes').val(item.notes);
          $('#bullding_no').val(item.bullding_no);
          $('#street_name').val(item.street_name);
          $('#district').val(item.district);
          $('#city').val(item.city);
          $('#postal_code').val(item.postal_code);
          $('#additional_no').val(item.additional_no);
          $('#other_buyer_id').val(item.other_buyer_id);
          $('#vat_registration_number').val(item.vat_registration_number);
          return false;
        },
        minLength: 1
      });
    });

    $('.inner-repeater').on('keypress', '.product_price',function (e) {
      var key = e.which;
      if(key == 13) {
        $(this).parent().parent().find(".product_quantity").focus();
        return false;
      }
    });
    $('.inner-repeater').on('keypress', '.product_quantity',function (e) {
      var key = e.which;
      if(key == 13) {
        $('.add_new_item').click();
        $('.product_name').last().focus();
        return false;
      }
    });
  </script>

<script>
  // Handle coupon code and manual discount interaction
  document.addEventListener('DOMContentLoaded', function() {
    const couponCodeInput = document.getElementById('coupon_code');
    const discountCheckbox = document.getElementById('Discount_Values_Checkbox');
    const discountValuesDiv = document.querySelector('.Discount_Values');

    if (couponCodeInput && discountCheckbox) {
      // Disable manual discount when coupon code is entered
      couponCodeInput.addEventListener('input', function() {
        if (this.value.trim() !== '') {
          discountCheckbox.checked = false;
          discountCheckbox.disabled = true;
          if (discountValuesDiv) {
            discountValuesDiv.style.display = 'none';
          }
        } else {
          discountCheckbox.disabled = false;
        }
      });

      // Disable coupon code when manual discount is checked
      discountCheckbox.addEventListener('change', function() {
        if (this.checked) {
          couponCodeInput.value = '';
          couponCodeInput.disabled = true;
        } else {
          couponCodeInput.disabled = false;
        }
      });

      // Initialize on page load
      if (couponCodeInput.value.trim() !== '') {
        discountCheckbox.checked = false;
        discountCheckbox.disabled = true;
        if (discountValuesDiv) {
          discountValuesDiv.style.display = 'none';
        }
      }
    }
  });
</script>
  {!! JsValidator::formRequest('App\Http\Requests\BillRequest', '#bill_create') !!}
@endpush
