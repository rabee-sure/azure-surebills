@extends('account.account_complete')

@section('steps')
    <div class="col-12">
      <div class="card">
        <div id="smartWizardValidation">
          <ul class="card-header">
            <li><a href="#step-0">1<br /><small>{{ __('My Information') }}</small></a></li>
            <li class="nav-item active"><a href="#step-1">2<br /><small>{{ __('Business Information') }}</small></a></li>
            <li><a href="#step-2">3<br /><small>{{ __('Bank Information') }}</small></a></li>
          </ul>
          <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data" >
            @csrf
              <div id="step-1" style="padding: 15px;">
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label name="license_type" for="inputEmail3">{{ __('License type') }}</label>
                      <select name="license_type" class="form-control">
                        <option value="Commercial Record" @if ($user->license_type == 'Commercial Record')selected="selected"@endif>{{ __('Commercial Record') }}</option>
                        <option value="Freelance" @if ($user->license_type == 'Freelance')selected="selected"@endif>{{ __('Freelance') }}</option>
                      </select>
                    </div>
                    <div class="form-group col-md-6">
                      <label for="vat_registration_number">{{ __('VAT Registration Number') }}</label>
                      <input value="{{ $user->vat_registration_number }}" name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{ __('VAT Registration Number') }}">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail1">{{ __('Business Name') }}</label>
                      <input value="{{ $user->business_name }}" name="business_name" type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Business Name') }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail2">{{ __('Sector') }}</label>
                      <input value="{{ $user->sector }}" name="sector" type="text" class="form-control" id="inputEmail2" placeholder="{{ __('Sector') }}">
                    </div>
                  </div>                  
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="business_address">{{ __('Address') }}</label>
                      <input value="{{ $user->business_address }}" name="business_address" type="text" class="form-control" id="business_address" placeholder="{{ __('Address') }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="business_mobile">{{ __('Mobile') }}</label>
                      <input value="{{ $user->business_mobile }}" name="business_mobile" type="tel" class="form-control" id="business_mobile" placeholder="{{ __('Mobile') }}">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail3">{{ __('Website') }}</label>
                      <input value="{{ $user->website }}" name="website"  type="text" class="form-control" id="inputEmail3" placeholder="{{ __('Website') }}">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail8">{{ __('Logo') }}</label>
                      <div class="custom-file">
                        <input name="logo" type="file" class="custom-file-input" id="inputEmail8">
                            {{-- <img src="{{ url(auth()->user()->logo)  }}" class="img-thumbnail" width="100" /> --}}
                            <input type="hidden" name="hidden_logo" value="{{ auth()->user()->logo }}" />
                        <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
                      </div>
                    </div>
                  </div> 
              </div><!-- step-1 -->
              <div class="btn-toolbar custom-toolbar text-center card-body pt-0">
                    <a class="btn btn-primary mx-2" href="/account?previous=1">{{__('Previous')}}</a>
                <button class="btn btn-primary next-btn mx-2" type="submit">{{__('Next')}}</button>
              </div>
          </form>

            </div>
      </div>
    </div>
@endsection

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
@endpush
