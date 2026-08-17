@extends('account.account_complete')

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.css') }}?v={{ config('app.asset_version') }}" />
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
      <div class="step active" data-target="#business-information">
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
        <div class="step" data-target="#bank-information">
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

      <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data">
        @csrf
        <!-- Business Information -->
        <div id="business-information" class="content active dstepper-block">
          <div class="row g-6">
            <div class="col-sm-6">
              <label class="form-label d-flex align-items-center justify-content-start gap-1" for="license_type">
                {{ __('License type') }}
                <button class="text-info p-0 bg-transparent border-0" type="button" data-bs-toggle="modal" data-bs-target="#license_type_modal"><i class="ti ti-info-circle"></i></button>
              </label>
              <select id="license_type" name="license_type" class="select2 form-select" data-allow-clear="false" data-minimum-results-for-search="Infinity">
                <option value="Commercial Record"
                  @if($errors->any())
                    @if(old('license_type') == 'Commercial Record')
                      {{'selected'}}
                    @endif
                  @elseif($user->license_type == 'Commercial Record')
                    {{'selected'}}
                  @endif
                >
                  {{ __('Commercial Record') }}
                </option>
                <option value="Freelance"
                  @if($errors->any())
                    @if(old('license_type') == 'Freelance')
                      {{'selected'}}
                    @endif
                  @elseif($user->license_type == 'Freelance')
                    {{'selected'}}
                  @endif
                >
                  {{ __('Freelance') }}
                </option>
              </select>
            </div><!-- col -->

            <div class="col-sm-6">
              <label for="vat_registration_number" class="form-label">
                {{ __('VAT Registration Number') }}
                @if(auth()->user()->source == 'sure bills')
                  <small class="d-inline-block text-secondary">( {{ __('optional') }} )</small>
                @else
                  <span class="requirement text-danger">*</span>
                @endif
              </label>
              <input
                value="@if($errors->any()){{old('vat_registration_number')}}@else{{$user->vat_registration_number}}@endif"
                name="vat_registration_number"
                type="text" class="form-control"
                id="vat_registration_number"
                placeholder="{{ __('VAT Registration Number') }}"
                autocomplete="off"
              />
            </div><!-- col -->

            <div id="registry_expiry_date" class="col-sm-6">
              <label for="commercial_registry_expiry_date" class="form-label">
                {{ __('Commercial Registry Expiry Date') }}
                <span class="requirement text-danger">*</span>
              </label>
              <input
                value="{{ Carbon\Carbon::now()->format('d/m/Y') }}"
                name="commercial_registry_expiry_date"
                id="commercial_registry_expiry_date"
                class="form-control flatpickr"
                placeholder="{{ __('Commercial Registry Expiry Date') }}"
                autocomplete="off"
              />
            </div><!-- col -->

            <div class="col-sm-6">
              <label for="commercial_registry_expiry_date" class="form-label">
                {{ __('Business Name') }}
                <small class="d-inline-block text-secondary">( EN )</small>
                <span class="requirement text-danger">*</span>
              </label>
              <input
                value="@if($errors->any()){{old('business_name_en')}}@else{{$user->business_name_en}}@endif"
                name="business_name_en"
                type="text"
                class="form-control"
                id="business_name_en"
                placeholder="{{ __('Business Name') }} (EN)"
                autocomplete="off"
              />
            </div><!-- col -->

            <div class="col-sm-6">
              <label for="business_name_ar" class="form-label">
                {{ __('Business Name') }}
                <small class="d-inline-block text-secondary">( AR )</small>
                <span class="requirement text-danger">*</span>
              </label>
              <input
                value="@if($errors->any()){{old('business_name_ar')}}@else{{$user->business_name_ar}}@endif"
                name="business_name_ar"
                type="text"
                class="form-control"
                id="business_name_ar"
                placeholder="{{ __('Business Name') }} (AR)"
                autocomplete="off"
              />
            </div><!-- col -->

            <div class="col-sm-6">
              <label for="business_address" class="form-label">
                {{ __('City') }}
                <span class="requirement text-danger">*</span>
              </label>
              <input
                value="@if($errors->any()){{old('business_address')}}@else{{$user->business_address}}@endif"
                name="business_address"
                type="text"
                class="form-control"
                id="business_address"
                placeholder="{{ __('City') }}"
                autocomplete="off"
              />
            </div><!-- col -->

            <div class="col-sm-6">
              <label for="business_address" class="form-label">
                {{ __('Address') }}
                <span class="requirement text-danger">*</span>
              </label>
              <input
                value="@if($errors->any()){{old('business_address_details')}}@else{{$user->business_address_details}}@endif"
                name="business_address_details"
                type="text"
                class="form-control"
                id="business_address_details"
                placeholder="{{ __('Address') }}"
                autocomplete="off"
              />
            </div><!-- col -->

            <div class="col-sm-6">
              <label for="business_mobile" class="form-label">
                {{ __('Mobile') }}
                <span class="requirement text-danger">*</span>
              </label>
              <input
                value="@if($errors->any()){{old('business_mobile')}}@else{{$user->business_mobile}}@endif"
                name="business_mobile"
                type="tel"
                inputmode="numeric"
                class="form-control"
                id="business_mobile"
                placeholder="5XXXXXXXX"
                pattern="[0-9]*"
                maxlength="9"
                autocomplete="off"
              />
            </div><!-- col -->

            @if(auth()->user()->source == 'sure bills')
              <div class="col-sm-6">
                <label for="website" class="form-label">{{ __('Website') }}</label>
                <input
                  value="@if($errors->any()){{old('website')}}@else{{$user->website}}@endif"
                  name="website"
                  type="url"
                  inputmode="url"
                  class="form-control"
                  id="website"
                  placeholder="{{ __('Website') }}"
                  autocomplete="off"
                />
              </div><!-- col -->
              <div class="col-sm-6">
                <label for="sector" class="form-label">{{ __('Sector') }}</label>
                <input
                  value="@if($errors->any()){{old('sector')}}@else{{$user->sector}}@endif"
                  name="sector"
                  type="text"
                  class="form-control"
                  id="sector"
                  placeholder="{{ __('Sector') }}"
                  autocomplete="off"
                />
              </div><!-- col -->
            @endif

            <div class="col-sm-6">
              <label for="logo" class="form-label">{{ __('Logo') }}</label>
              <div class="uploadFiledArea">
                <div class="uploadInput">
                  <div class="fileName">{{ __('No file chosen...') }}</div>
                  <div class="fileBtn">{{ __('Choose file') }}</div>
                </div><!-- uploadInput -->
                <input name="logo" type="file" id="logo" autocomplete="off" accept="image/png, image/jpeg, image/jpg" />
              </div><!-- uploadFiledArea -->
              <input type="hidden" name="hidden_logo" value="{{ auth()->user()->logo }}" />
              @if($errors->has('logo'))
                <span id="inputEmail8-error" class="invalid-feedback">{{ $errors->first('logo') }}</span>
              @endif
            </div><!-- col -->

            @if(auth()->user()->logo || (auth()->user()->mainStoreUser && auth()->user()->mainStoreUser->logo))
              <div class="logoImage col-sm-6">
                <div class="card h-100 relative">
                  <img src="{{ merchant_logo_url(auth_user_logo_path()) }}" alt="logo" class="card-img-top rounded-3" />
                  <button type="button" class="delete_logo position-absolute btn btn-icon btn-danger waves-effect waves-light">
                    <span class="ti ti-trash ti-xs"></span>
                  </button>
                </div>
              </div><!-- col-12 -->
            @endif

            @if(auth()->user()->source == 'sure bills')
              <div class="col-12">
                <hr class="mb-6 mt-0" />
                <label for="required_documents" class="form-label d-flex align-items-start justify-content-start flex-column">
                  <span class="d-block fs-5 mb-1">{{ __('Upload the required documents') }}</span>
                  <span class="text-muted mb-2">{{ __('Commercial registry, self-employment document, ID card ..etc') }}</span>
                </label>
                @include('components.dropzone',['documents' => merchant_dropzone_documents_payload((int) (auth()->user()->mainStoreUser ?? auth()->user())->id, 'business_documents'), 'upload_context' => 'business_documents'])
              </div><!-- col -->
            @endif

            <div class="col-12 d-flex justify-content-between">
              <a href="/account?previous=1" class="btn btn-label-secondary btn-prev" id="previous">
                <i class="icon-base ti ti-arrow-left icon-xs me-sm-2 me-0"></i>
                <span class="align-middle d-sm-inline-block d-none">{{__('Previous')}}</span>
              </a>
              @if(auth()->user()->source == 'sure bills')
                <button type="submit" class="btn btn-primary btn-next" id="next">
                  <span class="align-middle d-sm-inline-block d-none me-sm-2">{{__('Next')}}</span>
                  <i class="icon-base ti ti-arrow-right icon-xs"></i>
                </button>
              @else
                <button type="submit" class="btn btn-primary btn-next">
                  <span class="align-middle d-sm-inline-block d-none me-sm-2">{{__('Finish')}}</span>
                  <i class="icon-base ti ti-arrow-right icon-xs"></i>
                </button>
              @endif
            </div><!-- col -->
          </div><!-- row -->
        </div><!-- business-information -->
      </form>
    </div>

  </div>


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
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}?v={{ config('app.asset_version') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/flatpickr/flatpickr.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    $(document).ready(function() {
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

      $('.select2').select2();
    });



    $('#logo').bind('change', function () {
      var filename = $("#logo").val();
      if (/^\s*$/.test(filename)) {
        $(".fileName").text("{{ __('No file chosen...') }}");
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

    window.addEventListener('load', function() {
      if($('#license_type').val() == 'Commercial Record'){
        $('#registry_expiry_date').show();
      }else{
        $('#registry_expiry_date').hide();
      }
    });
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
@endpush
