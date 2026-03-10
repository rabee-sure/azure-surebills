@extends('layouts.app')

@section('title', __('Business Information'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('content')

  <h4 class="mb-1">{{ __('Business Information')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('account') }}" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Business Information') }}</li>
    </ol>
  </nav>

  <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data" class="card">
    @csrf
    <div class="card-body">
      <div class="row row-cols-1 row-cols-md-3 row-cols-lg-3 g-6">
        <div class="col">
          <label class="form-label d-flex align-items-center justify-content-start gap-1" for="license_type">
            {{ __('License type') }}
            <button class="text-info p-0 bg-transparent border-0" type="button" data-bs-toggle="modal" data-bs-target="#license_type_modal"><i class="ti ti-info-circle"></i></button>
          </label>
          <select id="license_type" name="license_type" class="select2 form-select" data-allow-clear="false" data-minimum-results-for-search="Infinity">
            <option value="Commercial Record" @if ($user->license_type == 'Commercial Record')selected="selected"@endif>{{ __('Commercial Record') }}</option>
            <option value="Freelance" @if ($user->license_type == 'Freelance')selected="selected"@endif>{{ __('Freelance') }}</option>
          </select>
        </div><!-- col -->
        <div class="col">
          <label for="vat_registration_number" class="form-label">
            {{ __('VAT Registration Number') }}
            @if(auth()->user()->source == 'sure bills')
            <small class="d-inline-block text-secondary">( {{ __('optional') }} )</small>
            @else
            <span class="requirement text-danger">*</span>
            @endif
          </label>
          <input value="{{ $user->vat_registration_number }}" name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}" autocomplete="off">
        </div><!-- col-12 -->
        <div class="col4" id="registry_expiry_date" @if($user->license_type != 'Commercial Record')style="display: none;"@endif>
          <label for="commercial_registry_expiry_date" class="form-label">{{ __('Commercial Registry Expiry Date') }} <span class="requirement text-danger">*</span></label>
          <input
            @if($user->commercial_registry_expiry_date)
              value="{{ Carbon\Carbon::parse($user->commercial_registry_expiry_date)->format('d/m/Y') }}"
            @else
              value="{{ Carbon\Carbon::now()->format('d/m/Y') }}"
            @endif
            name="commercial_registry_expiry_date"
            id="commercial_registry_expiry_date"
            class="form-control flatpickr"
            placeholder="{{ __('Commercial Registry Expiry Date') }}"
            autocomplete="off"
          >
        </div><!-- col-12 -->
        <div class="col">
          <label for="business_name_en" class="form-label">{{ __('Business Name') }} <small class="d-inline-block text-secondary">( EN )</small> <span class="requirement text-danger">*</span></label>
          <input value="{{ $user->business_name_en }}" name="business_name_en" type="text" class="form-control" id="business_name_en" placeholder="{{ __('Business Name') }} (EN)" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="business_name_ar" class="form-label">{{ __('Business Name') }} <small class="d-inline-block text-secondary">( AR )</small> <span class="requirement text-danger">*</span></label>
          <input value="{{ $user->business_name_ar }}" name="business_name_ar" type="text" class="form-control" id="business_name_ar" placeholder="{{ __('Business Name') }} (AR)" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="business_address" class="form-label">{{ __('City') }} <span class="requirement text-danger">*</span></label>
          <input value="{{ $user->business_address }}" name="business_address" type="text" class="form-control onlyEng" id="business_address" placeholder="{{ __('City') }}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="business_address_details" class="form-label">{{ __('Address') }} <span class="requirement text-danger">*</span></label>
          <input value="{{ $user->business_address_details }}" name="business_address_details" type="text" class="form-control" id="business_address_details" placeholder="{{ __('Address') }}" autocomplete="off">
        </div><!-- col -->
        <div class="col">
          <label for="business_mobile" class="form-label">{{ __('Mobile') }} <span class="requirement text-danger">*</span></label>
          <input value="{{ $user->business_mobile }}" name="business_mobile" type="tel" inputmode="numeric" class="form-control" id="business_mobile" placeholder="{{ __('Mobile') }}" pattern="[0-9]*" autocomplete="off">
        </div><!-- col -->
        @if(auth()->user()->source == 'sure bills')
          <div class="col">
            <label for="website" class="form-label">{{ __('Website') }}</label>
            <input value="{{ $user->website }}" name="website"  type="url" inputmode="url" class="form-control" id="website" placeholder="{{ __('Website') }}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="sector" class="form-label">{{ __('Sector') }}</label>
            <input value="{{ $user->sector }}" name="sector" type="text" class="form-control" id="sector" placeholder="{{ __('Sector') }}" autocomplete="off">
          </div><!-- col -->
        @endif
        <div class="col">
          <label for="logo" class="form-label">{{ __('Logo') }}</label>
          <input name="logo" type="file" id="logo" class="form-control" accept="image/png, image/jpeg, image/jpg">
          <input type="hidden" name="hidden_logo" value="{{ auth()->user()->logo }}" />
          @if($errors->has('logo'))
            <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('logo') }}</span>
          @endif
        </div><!-- col -->
        @if(auth()->user()->logo || (auth()->user()->mainStoreUser && auth()->user()->mainStoreUser->logo))
          <div class="logoImage col">
            <div class="card h-100 relative">
              <img src="@if(Storage::disk('public')->has(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->logo : auth()->user()->logo)) {{url('storage/'.auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->logo : auth()->user()->logo)}} @else {{url(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->logo : auth()->user()->logo)}} @endif" alt="logo" class="card-img-top rounded-3" />
              <button type="button" class="delete_logo position-absolute btn btn-icon btn-danger waves-effect waves-light">
                <span class="ti ti-trash ti-xs"></span>
              </button>
            </div>
          </div><!-- col -->
        @endif
        @if(auth()->user()->source == 'sure bills')
          <div class="col-12 w-100">
            <hr class="mb-6 mt-0" />
            <label for="commercial_registry_expiry_date" class="form-label d-flex align-items-start justify-content-start flex-column">
              <span class="d-block fs-5 mb-1">{{ __('Upload the required documents') }}</span>
              <span class="text-muted mb-2">{{ __('Commercial registry, self-employment document, ID card ..etc') }}</span>
            </label>
            @if($user->disable_business_documents)

                <div class="dropzone">
                    @foreach($documents as $file)
                        @include('components.file', ['file' => $file])
                    @endforeach
                </div>

            @else

                @include('components.dropzone', [
                    'documents' => $documents
                ])

            @endif
          </div><!-- col -->
        @endif
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
  </form><!-- card -->

  <!-- License type Modal -->
  <div class="modal fade" id="license_type_modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="license_type_modal_Label">{{ __('License type') }}</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-body d-block m-0">السجل التجاري يمكن إصدارة من وزارة التجارة من خلال الموقع الاكتروني الخاص بهم من خلال هذا الرابط .. <a href="http://mc.gov.sa/ar/eservices/Pages/ServiceDetails.aspx?sID=2" target="_blank" title="إضغط هنا">إضغط هنا</a></p>
          <p class="text-body d-block m-0">وثيقة العمل الحر وهي وثيقة مجانية تصدر من قبل وزارة العمل والتنمية الاجتماعية لممارسة العمل الحر، ولإصدار وثيقة العمل المجانية تقدم بطلب من خلال هذا الرابط .. <a href="https://freelance.sa/" target="_blank" title="أضغط هنا">أضغط هنا</a></p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"> {{ __('Close') }}</button>
        </div>
      </div>
    </div>
  </div>
  <!-- License type Modal -->

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    setTimeout(function() {
    const form = document.getElementById('form');
    if (form) {
      const btn = form.querySelector('.btn-submit-with-spinner');
      if (btn) {
        const btnText = btn.querySelector('.btn-text');
        const btnSpinner = btn.querySelector('.btn-spinner');
        const originalText = btnText ? btnText.textContent : '{{ __("Save") }}';
        function resetButton() {
          btn.disabled = false;
          if (btnText && btnSpinner) { btnText.textContent = originalText; btnSpinner.classList.add('d-none'); }
        }
        form.addEventListener('submit', function(e) {
          if (e.defaultPrevented || btn.disabled) return;
          btn.disabled = true;
          if (btnText && btnSpinner) { btnText.textContent = btn.dataset.loadingText || 'Saving...'; btnSpinner.classList.remove('d-none'); }
          setTimeout(resetButton, 8000);
        });
        $(form).on('invalid-form.validate', resetButton);
      }
    }
  }, 100);

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
    });

    $('#logo').bind('change', function () {
      var filename = $("#logo").val();
      if (/^\s*$/.test(filename)) {
        $(".fileName").text("No file chosen...");
      }
      else {
        $(".fileName").text(filename.replace("C:\\fakepath\\", ""));
      }
    });

    $('.delete_logo').click(function(){
      $('input[name="hidden_logo"]').val('');
      $('.logoImage').remove();
      $(this).remove();
    });

    $('#license_type').on('change', function() {
      if(this.value == 'Commercial Record'){
          $('#registry_expiry_date').show();
      }else{
          $('#registry_expiry_date').hide();
      }
    });
  </script>
@endpush
