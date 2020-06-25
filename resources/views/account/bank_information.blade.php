@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

        <div class="row">

          <div class="col-12">
            <h1>Bank Information</h1>
            <div class="separator mb-5"></div>
          </div>

          <div class="col-12">
            <div class="card mb-4">
              <div class="card-body">
                <form id="form" method="POST" action="{{ route('bank.information') }}">
                  @csrf 
                  <div class="form-row">
                    <div class="form-group col-12">
                      <label for="inputEmail5">License type</label>
                      <select name="license_type" id="inputEmail5" class="form-control">
                        <option value="commercial_registration" @if ($user->license_type == 'commercial_registration')selected="selected"@endif>Commercial Registration</option>
                        <option value="work_document" @if ($user->license_type == 'work_document')selected="selected"@endif>Work Document</option>
                      </select>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail6">Bank</label>
                      <input value="{{ $user->bank }}" name="bank" type="text" class="form-control" id="inputEmail6" placeholder="Bank">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail7">IBAN Number</label>
                      <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="IBAN Number">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail8">Organization Name</label>
                      <input value="{{ $user->organization_name }}" name="organization_name" type="text" class="form-control" id="inputEmail8" placeholder="Organization Name">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail9">Beneficiary Name</label>
                      <input value="{{ $user->beneficiary_name }}" name="beneficiary_name" type="text" class="form-control" id="inputEmail9" placeholder="Beneficiary Name">
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
    {!! JsValidator::formRequest('App\Http\Requests\BankInformationRequest', '#form') !!}
@endsection