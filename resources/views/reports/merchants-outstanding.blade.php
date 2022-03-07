@extends('layouts.app')

@section('title', __('Merchants Outstanding'))

  @section('css_styles')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  @endsection

@section('content')

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Merchants Outstanding')}}</h1>
        <div class="top-right-button-container">
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Merchants Outstanding')}}</li>
        </ol>
      </nav>
      <div class="separator mt-3 mb-5"></div>
      </div>
    </div>
  </div>

  <div class="reportsMerchantsPage">
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">

            @if ($errors->any())
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
            @endif

            <form method="post" action="{{route('reports.merchants-outstanding-store')}}" id="merchants_outstanding_store">
              @csrf
              <div class="row">
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="Name" class="d-block w-100">{{__('Merchants')}}</label>
                    <select class="form-control" id="merchants" name="merchants[]" multiple="multiple">
                      <option value="all">ALL</option>
                      @foreach($merchants as $merchant)
                      <option value="{{$merchant->id}}">{{$merchant->business_name_en}}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="Mobile" class="d-block w-100">{{ __('Date range') }}</label>
                    <div class="input-group">
                      <span class="input-group-text input-group-append input-group-addon"><i class="simple-icon-calendar"></i></span>
                      <input class="form-control" id="daterange" name="dates" placeholder="Filter by day" readonly="readonly">
                    </div>
                  </div>
                </div>
                <div class="col-12 col-md-4">
                  <div class="form-group">
                    <label for="Mobile">{{ __('Emails') }}</label>
                    <input type="email" name="emails" class="form-control" id="emails" aria-describedby="emailHelp" placeholder="Enter email">
                  </div>
                </div>
                <div class="col-12">
                  <button type="submit" class="btn btn-primary mr-3">{{__('Request')}}</button>
                </div>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
  <hr>
  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-body">
          <div class="table-responsive">
            @if($requests->count() > 0)
            <table class="table table-striped">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">{{__('Report type')}}</th>
                  <th scope="col">{{__('Filter Parameters')}}</th>
                  <th scope="col">{{__('Emails')}}</th>
                  <th scope="col">{{__('Status')}}</th>
                  <th scope="col">{{__('Request date')}}</th>
                  <th scope="col">{{__('Download File')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($requests as $request)
                  <tr>
                    <th scope="row">{{$request->id}}</th>
                    <td>{{$request->name}}</td>
                    <td>{{$request->params}}</td>
                    <td>{{$request->emails}}</td>
                    <td>@if($request->active == 0) {{__('Report Pending')}} @else {{__('Report Done')}} @endif</td>
                    <td>{{$request->created_at}}</td>
                    <td>
                      <a href="#" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Download File') }}">{{ __('Download File') }}</a>
                    </td>
                  </tr>
                @endforeach

              </tbody>
            </table>
            @else
              <div class="no_customers_yet">
                <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 24 24" width="512" fill="#999" xmlns:v="https://vecta.io/nano"><path d="M20 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-3a.94.94 0 0 0-1 1 .94.94 0 0 0 1 1 .94.94 0 0 0 1-1 .94.94 0 0 0-1-1zm3.5 8a.47.47 0 0 1-.5-.5v-1a1.54 1.54 0 0 0-1.5-1.5h-3a.47.47 0 0 1-.5-.5.47.47 0 0 1 .5-.5h3c1.4 0 2.5 1.1 2.5 2.5v1a.47.47 0 0 1-.5.5zM4 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-3a.94.94 0 0 0-1 1 .94.94 0 0 0 1 1 .94.94 0 0 0 1-1 .94.94 0 0 0-1-1zM.5 17a.47.47 0 0 1-.5-.5v-1C0 14.1 1.1 13 2.5 13h3a.47.47 0 0 1 .5.5.47.47 0 0 1-.5.5h-3A1.54 1.54 0 0 0 1 15.5v1a.47.47 0 0 1-.5.5zM12 12.5c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-5c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM17 19a.47.47 0 0 1-.5-.5v-2A1.54 1.54 0 0 0 15 15H9a1.54 1.54 0 0 0-1.5 1.5v2a.47.47 0 0 1-.5.5.47.47 0 0 1-.5-.5v-2C6.5 15.1 7.6 14 9 14h6c1.4 0 2.5 1.1 2.5 2.5v2a.47.47 0 0 1-.5.5z"/></svg>
                <span>{{ __('No Requests in this report type') }}</span>
              </div><!-- no_customers_yet -->
            @endif
            <!-- produvts pagination links -->
          </div>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('footer-scripts')
{!! JsValidator::formRequest('App\Http\Requests\MerchantsOutstandingStoreRequest', '#merchants_outstanding_store') !!}
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    $(document).ready(function() {
        $('#merchants').select2();
        $('#daterange').daterangepicker();
    });
  </script>
@endpush