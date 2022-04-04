@extends('layouts.app')

@section('title', __('Merchants Outstanding'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('/reports')}}" title="{{ __('Reports') }}">{{ __('Reports') }}</a>
    <i>/</i>
    <span>{{ __('Merchants Outstanding')}}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section id="reportsMerchantsPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Merchants Outstanding')}}</h1>
    </div><!-- title -->
    <div class="filterArea">
      <form method="post" action="{{route('reports.merchants-outstanding-store')}}" id="merchants_outstanding_store">
        @csrf

        @php
        $business_name = 'business_name_'.Config::get('app.locale');
        @endphp

        <div class="row align-items-end">
          <div class="col-12 col-md-3">
            <div class="form-group mb-3">
              <label for="Name" class="d-block mb-2">{{__('Merchants')}}</label>
              <select class="form-control select2-single w-100" id="merchants" name="merchants[]" multiple="multiple">
                <option value="all">ALL</option>
                @foreach($merchants as $merchant)
                <option value="{{$merchant->id}}">{{$merchant->$business_name}}</option>
                @endforeach
              </select>
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-3">
            <div class="form-group mb-3">
              <label for="Mobile" class="d-block mb-2">{{ __('select date range') }}</label>
              <div class="dateInput position-relative">
                <input class="bg-white border rounded-3 text-body w-100" id="daterange" name="dates" placeholder="Filter by day" readonly="readonly">
              </div><!-- dateInput -->
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-3">
            <div class="form-group mb-3">
              <label for="emails" class="d-block mb-2">{{ __('Emails') }}</label>
              <input type="email" name="emails" class="form-control border rounded-3 text-body shadow-none w-100" id="emails" placeholder="Enter email">
            </div><!-- form-group -->
          </div><!-- col-12 -->
          <div class="col-12 col-md-3">
            <div class="form-group mb-3">
              <button type="submit" class="btn-primary rounded-3 border-0 shadow-none d-flex align-items-center justify-content-center">{{__('Request')}}</button>
            </div><!-- form-group -->
          </div><!-- col-12 -->
        </div><!-- row -->
      </form>
    </div><!-- filterArea -->
    @if($requests->count() > 0)
      <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
          <div class="table-responsive">
            <table class="table table-striped table-hover text-nowrap">
              <thead>
                <tr>
                  <th scope="col" class="text-center">#</th>
                  <th scope="col" class="text-center">{{__('Report type')}}</th>
                  <th scope="col" class="text-center">{{__('Report Period')}}</th>
                  <th scope="col" class="text-center">{{__('Emails')}}</th>
                  <th scope="col" class="text-center">{{__('Status')}}</th>
                  <th scope="col" class="text-center">{{__('Request date')}}</th>
                  <th scope="col" class="text-center">{{__('Download File')}}</th>
                </tr>
              </thead>
              <tbody>
                @foreach($requests as $request)
                  <tr>
                    <td class="text-center">{{$request->id}}</td>
                    <td class="text-center">{{$request->name}}</td>
                    @php
                    $paramArr = json_decode($request->params ,true);
                    @endphp
                    <td class="text-center">{{__('From :from To :to', ['from' => $paramArr["from"], 'to' => $paramArr["to"]])}}</td>
                    <td class="text-center">{{$request->emails}}</td>
                    <td class="text-center">@if($request->active == 0) {{__('Report Pending')}} @else {{__('Report Done')}} @endif</td>
                    <td class="text-center">{{$request->created_at}}</td>
                    <td class="text-center">
                      @if(file_exists(storage_path('app/public/reports/'.$request->name.'/'.$request->name.'_'.$request->id.'.xlsx')))
                        <a href="{{Storage::url('reports/'.$request->name.'/'.$request->name.'_'.$request->id.'.xlsx')}}" class="d-flex align-items-center justify-content-center btn-primary rounded-3 border-0 shadow-none mx-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Download File') }}"><i class="fal fa-download"></i></a>
                      @endif
                    </td>
                  </tr>
                @endforeach
              </tbody>
            </table>
          </div>
        {{ $requests->links() }}
      </div><!-- blockArea -->
    @else
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column">
        <i class="fal fa-file-chart-line"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No Requests in this report type') }}</span>
      </div><!-- no_bills_yet -->
    @endif
  </section><!-- reportsMerchantsPage -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script>
    $(document).ready(function() {
      $('#merchants').select2();
      $(function() {
        var lang = "<?php echo app()->getLocale(); ?>";
        $('input[name="dates"]').daterangepicker({
          opens: lang == 'en' ? 'right' : 'left',
          locale: {
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
          ranges: {
            '{{__('Today')}}': [moment(), moment()],
            '{{__('Yesterday')}}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
            '{{__('Last 7 Days')}}': [moment().subtract(6, 'days'), moment()],
            '{{__('Last 30 Days')}}': [moment().subtract(29, 'days'), moment()],
            '{{__('This Month')}}': [moment().startOf('month'), moment().endOf('month')],
            '{{__('Last Month')}}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
          },
        });
      });
    });
  </script>
  {!! JsValidator::formRequest('App\Http\Requests\MerchantsOutstandingStoreRequest', '#merchants_outstanding_store') !!}
@endpush
