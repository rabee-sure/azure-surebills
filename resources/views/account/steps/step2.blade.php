@extends('account.account_complete')

@section('steps')
  <div class="stepsArea d-flex align-items-start justify-content-between position-relative mb-5">
    <div class="item d-flex align-items-center justify-content-center flex-column done">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm"><i class="fal fa-check"></i></span>
      <p class="d-block text-center mb-0 mt-2">{{ __('My Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column active">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">2</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Business Information') }}</p>
    </div><!-- item -->
    <div class="item d-flex align-items-center justify-content-center flex-column">
      <span class="border rounded-circle fw-bold d-flex align-items-center justify-content-center position-relative bg-light shadow-sm">3</span>
      <p class="d-block text-center mb-0 mt-2">{{ __('Bank Information') }}</p>
    </div><!-- item -->
  </div><!-- stepsArea -->
  <div class="blockStep2 bg-white rounded-3 shadow-sm p-3">
    <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data" class="m-0">
      @csrf
      <div class="row">
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="license_type" class="d-flex align-items-center justify-content-start mb-2">
              {{ __('License type') }} 
              <button class="btn-primary border-0 rounded-circle shadow-none p-0 d-flex align-items-center justify-content-center" type="button" data-bs-toggle="modal" data-bs-target=".license_type_modal"><i class="fas fa-question"></i></button>
            </label>
            <select id="license_type" name="license_type" class="form-control rounded-3 shadow-none border">
              {{-- <option value="Commercial Record"@if($errors->any()) @if(old('license_type') == 'Commercial Record') {{selected}} @elseif($user->license_type == 'Commercial Record')selected="selected"@endif>{{ __('Commercial Record') }}</option> --}}
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
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="vat_registration_number" class="d-block mb-2">{{ __('VAT Registration Number') }} <small class="d-inline-block text-secondary">( {{ __('optional') }} )</small></label>
            <input value="@if($errors->any()){{old('vat_registration_number')}}@else{{$user->vat_registration_number}}@endif" name="vat_registration_number" type="text" class="form-control rounded-3 shadow-none border" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div id="registry_expiry_date" class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="commercial_registry_expiry_date" class="d-block mb-2">{{ __('Commercial Registry Expiry Date') }} <span class="requirement text-danger">*</span></label>
            <input value="{{ Carbon\Carbon::now()->format('m/d/Y') }}" name="commercial_registry_expiry_date" id="commercial_registry_expiry_date" class="form-control rounded-3 shadow-none border datepicker" placeholder="{{ __('Commercial Registry Expiry Date') }}">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="business_name_en" class="d-block mb-2">{{ __('Business Name') }} <small class="d-inline-block text-secondary">( EN )</small> <span class="requirement text-danger">*</span></label>
            <input value="@if($errors->any()){{old('business_name_en')}}@else{{$user->business_name_en}}@endif" name="business_name_en" type="text" class="form-control rounded-3 shadow-none border onlyEng" id="business_name_en" placeholder="{{ __('Business Name') }} (EN)">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="business_name_ar" class="d-block mb-2">{{ __('Business Name') }} <small class="d-inline-block text-secondary">( AR )</small> <span class="requirement text-danger">*</span></label>
            <input value="@if($errors->any()){{old('business_name_ar')}}@else{{$user->business_name_ar}}@endif" name="business_name_ar" type="text" class="form-control rounded-3 shadow-none border" id="business_name_ar" placeholder="{{ __('Business Name') }} (AR)">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="business_address" class="d-block mb-2">{{ __('Address') }} <span class="requirement text-danger">*</span></label>
            <input value="@if($errors->any()){{old('business_address')}}@else{{$user->business_address}}@endif" name="business_address" type="text" class="form-control rounded-3 shadow-none border onlyEng" id="business_address" placeholder="{{ __('Address') }}">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="business_mobile" class="d-block mb-2">{{ __('Mobile') }} <span class="requirement text-danger">*</span></label>
            <div class="phoneInput overflow-hidden position-relative">
              <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
              <input value="@if($errors->any()){{old('business_mobile')}}@else{{$user->business_mobile}}@endif" name="business_mobile" type="tel" inputmode="numeric" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="business_mobile" placeholder="5XXXXXXXX" pattern="[0-9]*" maxlength="9">
            </div><!-- phoneInput -->
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="website" class="d-block mb-2">{{ __('Website') }}</label>
            <input value="@if($errors->any()){{old('website')}}@else{{$user->website}}@endif" name="website"  type="url" inputmode="url" class="form-control rounded-3 shadow-none border" id="website" placeholder="{{ __('Website') }}">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
          <div class="form-group mb-3">
            <label for="sector" class="d-block mb-2">{{ __('Sector') }}</label>
            <input value="@if($errors->any()){{old('sector')}}@else{{$user->sector}}@endif" name="sector" type="text" class="form-control rounded-3 shadow-none border" id="sector" placeholder="{{ __('Sector') }}">
          </div><!-- form-group -->
        </div><!-- col-12 -->
        <div class="col-12 col-md-6">
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
        @if(auth()->user()->logo)
          <div class="col-12 col-md-6">
            <div class="form-group mb-3">
              <div class="custom-file">
                <img  src="{{ url(auth()->user()->logo)  }}" class="img-thumbnail logo_image" width="100" />
                <i class="glyph-icon simple-icon-trash delete_logo"></i>
              </div>
            </div>
          </div><!-- col-12 -->
        @endif
        <div class="col-12">
          <span class="d-block fw-bold fs-6 text-body mb-1">{{ __('Upload the required documents') }}</span>
          <p class="d-block mb-3 text-secondary">{{ __('Commercial registry, self-employment document, ID card ..etc') }}</p>
          @include('components.dropzone',['documents' => auth()->user()->business_documents->toArray()])
        </div><!-- col-12 -->
      </div><!-- row -->
      <div class="btnsArea d-flex align-items-center justify-content-center flex-wrap border-top pt-3">
        <a id="previous" class="d-flex align-items-center justify-content-center btn-primary rounded-3 shadow-none fw-bold border-0 mx-2" href="/account?previous=1">{{__('Previous')}}</a>
        <button  id="next" class="d-flex align-items-center justify-content-center btn-primary rounded-3 shadow-none fw-bold border-0 mx-2" type="submit">{{__('Next')}}</button>
      </div><!-- btnsArea -->
    </form>
  </div><!-- blockStep2 -->

<div class="modal fade license_type_modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('License type') }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>السجل التجاري يمكن إصدارة من وزارة التجارة من خلال الموقع الاكتروني الخاص بهم من خلال هذا الرابط .. <a href="http://mc.gov.sa/ar/eservices/Pages/ServiceDetails.aspx?sID=2" target="_blank" title="إضغط هنا">إضغط هنا</a></p>
        <p>وثيقة العمل الحر وهي وثيقة مجانية تصدر من قبل وزارة العمل والتنمية الاجتماعية لممارسة العمل الحر، ولإصدار وثيقة العمل المجانية تقدم بطلب من خلال هذا الرابط .. <a href="https://freelance.sa/" target="_blank" title="أضغط هنا">أضغط هنا</a></p>
      </div>
    </div>
  </div>
</div>
@endsection

@push('footer-scripts')
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

    $('#license_type').on('change', function() {
      if(this.value == 'Commercial Record'){
          $('#registry_expiry_date').show();
      }else{
          $('#registry_expiry_date').hide();
      }
    });
  </script>
@endpush


@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
@endpush