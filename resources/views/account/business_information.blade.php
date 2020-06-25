@extends('layouts.app')

@section('title', 'Page Title')

@section('content')
        <div class="row">

          <div class="col-12">
            <h1>Business Information</h1>
            <div class="separator mb-5"></div>
          </div>

          <div class="col-12">
            <div class="card mb-4">
              <div class="card-body">
                <form id="form" method="POST" action="{{ route('business.information') }}" enctype="multipart/form-data">
                  @csrf 
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail1">Business Name</label>
                      <input value="{{ $user->business_name }}" name="business_name" type="text" class="form-control" id="inputEmail1" placeholder="Business Name">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail2">Sector</label>
                      <input value="{{ $user->sector }}" name="sector" type="text" class="form-control" id="inputEmail2" placeholder="Sector">
                    </div>
                  </div>                  
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="business_address">{{ __('Address') }}</label>
                      <input value="{{ $user->business_address }}" name="business_address" type="text" class="form-control" id="business_address" placeholder="Business Address">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="business_mobile">{{ __('Mobile') }}</label>
                      <input value="{{ $user->business_mobile }}" name="business_mobile" type="tel" class="form-control" id="business_mobile" placeholder="Business Mobile">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail3">Website</label>
                      <input value="{{ $user->website }}" name="website"  type="text" class="form-control" id="inputEmail3" placeholder="Website">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail4">Instagram</label>
                      <input value="{{ $user->instagram }}" name="instagram" type="text" class="form-control" id="inputEmail4" placeholder="Instagram">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail5">Twitter</label>
                      <input value="{{ $user->twitter }}" name="twitter" type="text" class="form-control" id="inputEmail5" placeholder="Twitter">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail6">Facebook</label>
                      <input value="{{ $user->facebook }}" name="facebook" type="text" class="form-control" id="inputEmail6" placeholder="Facebook">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail7">Description (BIO)</label>
                      <input value="{{ $user->description }}" name="description" type="text" class="form-control" id="inputEmail7" placeholder="Description (BIO)">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail8">Logo</label>
                      <div class="custom-file">
                        <input name="logo" type="file" class="custom-file-input" id="inputEmail8">
                        <label class="custom-file-label" for="inputEmail8">Choose file</label>
                      </div>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="vat_registration_number">VAT Registration Number</label>
                      <input value="{{ $user->vat_registration_number }}" name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="VAT Registration Number">
                    </div>
                  </div>
                  <button type="submit" class="btn btn-primary d-block mt-2">Save</button>
                </form>
              </div>
            </div>
          </div>
    </div>
@endsection

@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\BusinessInformationRequest', '#form') !!}
@endsection