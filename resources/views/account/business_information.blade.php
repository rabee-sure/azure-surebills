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
        <form>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail1">Business Name</label>
              <input type="text" class="form-control" id="inputEmail1" placeholder="Business Name">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail2">Sector</label>
              <input type="text" class="form-control" id="inputEmail2" placeholder="Sector">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail1">Address</label>
              <input type="text" class="form-control" id="inputEmail1" placeholder="Address">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail2">Telephone</label>
              <input type="tel" class="form-control" id="inputEmail2" placeholder="Telephone">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-6">
              <label for="inputEmail3">Website</label>
              <input type="text" class="form-control" id="inputEmail3" placeholder="Website">
            </div>
            <div class="form-group col-md-6">
              <label for="inputEmail8">Logo</label>
              <div class="custom-file">
                <input type="file" class="custom-file-input" id="inputEmail8">
                <label class="custom-file-label" for="inputEmail8">Choose file</label>
              </div>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group col-md-12">
              <label for="inputEmail3">License type</label>
              <select name="license_type" class="form-control">
                <option value="Commercial Record">Commercial Record</option>
                <option value="Freelance">Freelance</option>
              </select>
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