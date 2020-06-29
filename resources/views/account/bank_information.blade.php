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
                      <label for="inputEmail5">Bank</label>
                      <select name="bank" id="inputEmail5" class="form-control">
                        @foreach(getBanks() as $bank)
                          <option value="{{$bank['id']}}" @if ($user->bank == $bank['id'])selected="selected"@endif>{{$bank['en']}}</option>
                        @endforeach
                      </select>
                    </div>
                  </div>
                  <div class="form-row">
                    <div class="form-group col-md-6">
                      <label for="inputEmail7">IBAN Number</label>
                      <input value="{{ $user->iban_number }}"  name="iban_number" type="text" class="form-control" id="inputEmail7" placeholder="IBAN Number">
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