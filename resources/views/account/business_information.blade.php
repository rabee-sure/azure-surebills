@extends('layouts.app')

@section('title', __('Business Information'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Business Information') }}</span>
  </div><!-- breadcrump -->

  <section id="businessInformationPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Account Information')}}</h1>
    </div><!-- title -->
    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3 p-3">
      <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="license_type" class="d-flex align-items-center justify-content-start mb-2">
                {{ __('License type') }}
                <button class="btn-primary border-0 rounded-circle shadow-none p-0 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="modal" data-bs-target=".licenseTypeModal"><i class="fas fa-question"></i></button>
              </label>
              <select id="license_type" name="license_type" class="form-control rounded-3 shadow-none border select2-single">
                <option value="Commercial Record" @if ($user->license_type == 'Commercial Record')selected="selected"@endif>{{ __('Commercial Record') }}</option>
                <option value="Freelance" @if ($user->license_type == 'Freelance')selected="selected"@endif>{{ __('Freelance') }}</option>
              </select>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="vat_registration_number" class="d-block mb-2">
                {{ __('VAT Registration Number') }}
                @if(auth()->user()->source == 'sure bills')
                <small class="d-inline-block text-secondary">( {{ __('optional') }} )</small>
                @else
                <span class="requirement text-danger">*</span>
                @endif
            </label>
              <input value="{{ $user->vat_registration_number }}" name="vat_registration_number" type="text" class="form-control rounded-3 shadow-none border" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}" autocomplete="off">
            </div>
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4" id="registry_expiry_date" @if($user->license_type != 'Commercial Record')style="display: none;"@endif>
            <div class="form-group mb-3">
              <label for="commercial_registry_expiry_date" class="d-block mb-2">{{ __('Commercial Registry Expiry Date') }} <span class="requirement text-danger">*</span></label>
              <input
                @if($user->commercial_registry_expiry_date)
                  value="{{ Carbon\Carbon::parse($user->commercial_registry_expiry_date)->format('d/m/Y') }}"
                @else
                  value="{{ Carbon\Carbon::now()->format('d/m/Y') }}"
                @endif
                name="commercial_registry_expiry_date"
                id="commercial_registry_expiry_date"
                class="form-control rounded-3 shadow-none border expiryDate"
                placeholder="{{ __('Commercial Registry Expiry Date') }}"
                autocomplete="off"
              >
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="business_name_en" class="d-block mb-2">{{ __('Business Name') }} <small class="d-inline-block text-secondary">( EN )</small> <span class="requirement text-danger">*</span></label>
              <input value="{{ $user->business_name_en }}" name="business_name_en" type="text" class="form-control rounded-3 shadow-none border onlyEng" id="business_name_en" placeholder="{{ __('Business Name') }} (EN)" autocomplete="off">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="business_name_ar" class="d-block mb-2">{{ __('Business Name') }} <small class="d-inline-block text-secondary">( AR )</small> <span class="requirement text-danger">*</span></label>
              <input value="{{ $user->business_name_ar }}" name="business_name_ar" type="text" class="form-control rounded-3 shadow-none border" id="business_name_ar" placeholder="{{ __('Business Name') }} (AR)" autocomplete="off">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="business_address" class="d-block mb-2">{{ __('City') }} <span class="requirement text-danger">*</span></label>
              <input value="{{ $user->business_address }}" name="business_address" type="text" class="form-control rounded-3 shadow-none border onlyEng" id="business_address" placeholder="{{ __('City') }}" autocomplete="off">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="business_address_details" class="d-block mb-2">{{ __('Address') }} <span class="requirement text-danger">*</span></label>
              <input value="{{ $user->business_address_details }}" name="business_address_details" type="text" class="form-control rounded-3 shadow-none border" id="business_address_details" placeholder="{{ __('Address') }}" autocomplete="off">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="business_mobile" class="d-block mb-2">{{ __('Mobile') }} <span class="requirement text-danger">*</span></label>
              <div class="phoneInput overflow-hidden position-relative">
                <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
                <input value="{{ $user->business_mobile }}" name="business_mobile" type="tel" inputmode="numeric" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="business_mobile" placeholder="{{ __('Mobile') }}" pattern="[0-9]*" autocomplete="off">
              </div><!-- phoneInput -->
            </div><!-- form-group -->
          </div><!-- col-12 -->
          @if(auth()->user()->source == 'sure bills')
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="website" class="d-block mb-2">{{ __('Website') }}</label>
              <input value="{{ $user->website }}" name="website"  type="url" inputmode="url" class="form-control rounded-3 shadow-none border" id="website" placeholder="{{ __('Website') }}" autocomplete="off">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="sector" class="d-block mb-2">{{ __('Sector') }}</label>
              <input value="{{ $user->sector }}" name="sector" type="text" class="form-control rounded-3 shadow-none border" id="sector" placeholder="{{ __('Sector') }}" autocomplete="off">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          @endif
          <div class="col-12 col-md-6 col-lg-4">
            <div class="form-group mb-3">
              <label for="logo" class="d-block mb-2">{{ __('Logo') }}</label>
              <div class="upoadInput border rounded-3 position-relative overflow-hidden d-flex align-items-center justify-content-start">
                <input name="logo" type="file" id="logo" class="d-block position-absolute top-0 start-0 w-100 h-100" accept="image/png, image/jpeg, image/jpg">
                <input type="hidden" name="hidden_logo" value="{{ auth()->user()->logo }}" />
                <div class="fileName h-100 d-flex align-items-center justify-content-start flex-grow-1 px-2"></div>
                <div class="fileBtn text-body d-flex align-items-center justify-content-center fw-bold">{{ __('Choose file') }}</div>
              </div><!-- upoadInput -->
              @if($errors->has('logo'))
                <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('logo') }}</span>
              @endif
            </div><!-- form-group -->
          </div><!-- col-12 -->
          @if(auth()->user()->logo || (auth()->user()->mainStoreUser && auth()->user()->mainStoreUser->logo))
            <div class="col-12 col-md-6 col-lg-4">
              <div class="form-group mb-3">
                <div class="logoImage p-2 border overflow-hidden rounded-3 position-relative d-flex align-items-center justify-content-center">
                  <img src="@if(Storage::has(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->logo : auth()->user()->logo)) {{url('storage/'.auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->logo : auth()->user()->logo)}} @else {{url(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->logo : auth()->user()->logo)}} @endif" alt="logo" class="logo_image mw-100 mh-100" />
                  <i class="fal fa-trash-alt delete_logo position-absolute btn-danger rounded-3 d-flex align-items-center justify-content-center text-white"></i>
                </div><!-- logoImage -->
              </div><!-- form-group -->
            </div><!-- col-12 -->
          @endif
          @if(auth()->user()->source == 'sure bills')
          <div class="col-12">
            <span class="d-block fw-bold fs-6 text-body mb-1">{{ __('Upload the required documents') }}</span>
            <p class="d-block mb-3 text-secondary">{{ __('Commercial registry, self-employment document, ID card ..etc') }}</p>
            @if(auth()->user()->disable_business_documents)
              <div class="dropzone">
                @foreach(auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->business_documents : auth()->user()->business_documents as $file)
                  @include('components.file', ['file' => $file])
                @endforeach
              </div>
            @else
              @include('components.dropzone',[
                'documents' => auth()->user()->mainStoreUser ? auth()->user()->mainStoreUser->business_documents->toArray() : auth()->user()->business_documents->toArray()
              ])
            @endif
          </div><!-- col-12 -->
          @endif
        </div><!-- row -->
        <div class="saveBtn d-flex justify-content-start mt-3">
          <button type="submit" class="formBtn btn-primary rounded-3 border-0 d-flex align-items-center justify-content-center fw-bold"> {{__('Save')}}</button>
        </div><!-- saveBtn -->
      </form>
    </div><!-- blockArea -->
  </section><!-- businessInformationPage -->

  <div class="modal fade licenseTypeModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header d-flex align-items-center justify-content-between">
          <span class="d-block fw-bold text-body">{{ __('License type') }}</span>
          <button type="button" class="btn-close m-0 p-0" data-bs-dismiss="modal" aria-label="Close"></button>
        </div><!-- modal-header -->
        <div class="modal-body">
          <p>السجل التجاري يمكن إصدارة من وزارة التجارة من خلال الموقع الاكتروني الخاص بهم من خلال هذا الرابط .. <a href="http://mc.gov.sa/ar/eservices/Pages/ServiceDetails.aspx?sID=2" target="_blank" title="إضغط هنا">إضغط هنا</a></p>
          <p>وثيقة العمل الحر وهي وثيقة مجانية تصدر من قبل وزارة العمل والتنمية الاجتماعية لممارسة العمل الحر، ولإصدار وثيقة العمل المجانية تقدم بطلب من خلال هذا الرابط .. <a href="https://freelance.sa/" target="_blank" title="أضغط هنا">أضغط هنا</a></p>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script type="text/javascript">
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

    // Single Daterangepicker
    $(function() {
      $('.expiryDate').daterangepicker({
        "singleDatePicker": true,
        "autoApply": true,
        "maxSpan": {
          "days": 7
        },
        locale: {
          format: 'DD/MM/YYYY',
          daysOfWeek: [
            '{{__('Sun')}}',
            '{{__('Mon')}}',
            '{{__('Tue')}}',
            '{{__('Wed')}}',
            '{{__('Thur')}}',
            '{{__('Fri')}}',
            '{{__('Sat')}}'
          ],
          monthNames: [
            '{{__('January')}}',
            '{{__('February')}}',
            '{{__('March')}}',
            '{{__('April')}}',
            '{{__('May')}}',
            '{{__('June')}}',
            '{{__('July')}}',
            '{{__('August')}}',
            '{{__('September')}}',
            '{{__('October')}}',
            '{{__('November')}}',
            '{{__('December')}}'
          ],
          fromLabel: '{{__('from')}}',
          toLabel: '{{__('to')}}',
          applyLabel: '{{__('apply')}}',
          cancelLabel:'{{__('cancel')}}',
          customRangeLabel: '{{__('custom Range')}}',
          weekLabel: '{{__('week')}}',
        },
      });
    });

    $('#license_type').on('change', function() {
      if(this.value == 'Commercial Record'){
          $('#registry_expiry_date').show();
      }else{
          $('#registry_expiry_date').hide();
      }
    });
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
@endpush
