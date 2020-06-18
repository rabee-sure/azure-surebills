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
                      <label for="inputEmail3">Website</label>
                      <input type="text" class="form-control" id="inputEmail3" placeholder="Website">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail4">Instagram</label>
                      <input type="text" class="form-control" id="inputEmail4" placeholder="Instagram">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail5">Twitter</label>
                      <input type="text" class="form-control" id="inputEmail5" placeholder="Twitter">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail6">Facebook</label>
                      <input type="text" class="form-control" id="inputEmail6" placeholder="Facebook">
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail7">Description (BIO)</label>
                      <input type="text" class="form-control" id="inputEmail7" placeholder="Description (BIO)">
                    </div>
                    <div class="form-group col-md-6">
                      <label for="inputEmail8">Logo</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputEmail8">
                        <label class="custom-file-label" for="inputEmail8">Choose file</label>
                      </div>
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