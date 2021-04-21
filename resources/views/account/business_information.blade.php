@extends('layouts.app')

@section('title', __('Business Information'))



@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{ __('Business Information') }}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item"><a href="{{ url('account') }}" title="{{__('Account')}}">{{__('Account')}}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Business Information')}}</li>
        </ol>
      </nav>
    <div class="separator mb-5"></div>
  </div>

  <div class="col-12">
    <div class="card mb-4">
      <div class="card-body">
        <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data">
          @csrf
          <div class="form-row">
            <div class="form-group col-md-6">
              <label name="license_type" for="inputEmail3">{{ __('License type') }} <button class="license_button" type="button" data-toggle="modal" data-target=".license_type_modal"></button></label>
              <select id="license_type" name="license_type" class="form-control">
                <option value="Commercial Record" @if ($user->license_type == 'Commercial Record')selected="selected"@endif>{{ __('Commercial Record') }}</option>
                <option value="Freelance" @if ($user->license_type == 'Freelance')selected="selected"@endif>{{ __('Freelance') }}</option>
              </select>
            </div>
            <div class="form-group col-md-6">
              <label for="vat_registration_number">{{ __('VAT Registration Number') }} ( {{ __('optional') }} )</label>
              <input value="{{ $user->vat_registration_number }}" name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
            </div>
          </div>

          <div id="registry_expiry_date" class="form-row" @if($user->license_type != 'Commercial Record')style="display: none;"@endif>
              <div class="form-group col-12 col-md-12">
                <label>{{ __('Commercial Registry Expiry Date') }}  <span class="requirement">*</span></label>
                <input 
                @if($user->commercial_registry_expiry_date)
                  value="{{ Carbon\Carbon::parse($user->commercial_registry_expiry_date)->format('m/d/Y') }}"
                @else 
                  value="{{ Carbon\Carbon::now()->format('m/d/Y') }}"
                @endif name="commercial_registry_expiry_date" class="form-control datepicker" placeholder="{{ __('Commercial Registry Expiry Date') }}">
              </div><!-- form-group -->
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="business_name_en">{{ __('Business Name') }} (EN) <span class="requirement">*</span></label>
              <input value="{{ $user->business_name_en }}" name="business_name_en" type="text" class="form-control" id="business_name_en" placeholder="{{ __('Business Name') }} (EN)">
            </div>
            <div class="form-group col-md-6">
              <label for="business_name_ar">{{ __('Business Name') }} (AR) <span class="requirement">*</span></label>
              <input value="{{ $user->business_name_ar }}" name="business_name_ar" type="text" class="form-control" id="business_name_ar" placeholder="{{ __('Business Name') }} (AR)">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="business_address">{{ __('Address') }} <span class="requirement">*</span></label>
              <input value="{{ $user->business_address }}" name="business_address" type="text" class="form-control" id="business_address" placeholder="{{ __('Address') }}">
            </div>
            <div class="form-group col-md-6">
              <label for="business_mobile">{{ __('Mobile') }} <span class="requirement">*</span></label>
              <input value="{{ $user->business_mobile }}" name="business_mobile" type="tel" pattern="[0-9]{9}" class="form-control" id="business_mobile" placeholder="{{ __('Mobile') }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail3">{{ __('Website') }}</label>
              <input value="{{ $user->website }}" name="website"  type="text" class="form-control" id="inputEmail3" placeholder="{{ __('Website') }}">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail2">{{ __('Sector') }}</label>
              <input value="{{ $user->sector }}" name="sector" type="text" class="form-control" id="inputEmail2" placeholder="{{ __('Sector') }}">
            </div>
          </div>

          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail8">{{ __('Logo') }}</label>
              <div class="custom-file">
                <input name="logo" type="file" class="custom-file-input" accept="image/png, image/jpeg, image/jpg">
                <input type="hidden" name="hidden_logo" value="{{ auth()->user()->logo }}" />
                <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
                @if($errors->has('logo'))
                    <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('logo') }}</span>
                @endif
              </div>
            </div>
            <div class="form-group col-md-6">
                <div class="custom-file">
                  @if(auth()->user()->logo)
                      <img src="@if(Storage::disk('public')->has(auth()->user()->logo)) {{url('storage/'.auth()->user()->logo)}} @else {{url(auth()->user()->logo)}} @endif" class="img-thumbnail logo_image" width="100" />
                      <i class="glyph-icon simple-icon-trash delete_logo"></i>

                  @endif
                </div>
              </div>
          </div>

          <h5 class="mb-2 mt-2">{{ __('Upload the required documents') }}</h5>
          <p class="">{{ __('Commercial registry, self-employment document, ID card ..etc') }}</p>

          @if(auth()->user()->disable_business_documents)
            <div class="dropzone">
              @foreach(auth()->user()->business_documents as $file)
                @include('components.file', ['file' => $file])
              @endforeach
            </div>
          @else
            @include('components.dropzone',[
              'documents' => auth()->user()->business_documents->toArray()
            ])
          @endif
          <button type="submit" class="btn btn-primary d-block mt-2">{{ __('Save') }}</button>

        </form>
      </div>
    </div>
  </div>
</div>

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
