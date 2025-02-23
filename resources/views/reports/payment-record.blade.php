@extends('layouts.app')

@section('title', __('Payment Record'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Payment Record') }}</span>
  </div><!-- breadcrump -->

  <section id="statementIndexPage">

    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Payment Record') }}</h1>
    </div><!-- title -->

    <div class="filterArea mb-3 d-flex align-items-end justify-content-between flex-wrap flex-column flex-lg-row">
      <div class="rightCol d-flex align-items-end justify-content-start flex-wrap flex-grow-1">
        <div class="form-group mb-3">
          <select name="transaction_type" class="form-control select2-single filter">
            <option @if(!isset(request()->transaction_type)) selected @endif disabled> {{ __('Transaction Type') }}</option>
            @foreach ($filters['transaction_types'] as $typeKey => $transaction_type)
            <option value="{{$typeKey}}" @if(isset(request()->transaction_type) && request()->transaction_type == $typeKey) selected @endif>{{__($transaction_type)}}</option>
            @endforeach
          </select>
        </div><!-- form-group -->
        <div class="form-group mb-3">
          <select name="payment_way" class="form-control select2-single filter">
            <option @if(!isset(request()->payment_way)) selected @endif disabled> {{ __('Payment Method') }}</option>
            @foreach ($filters['payment_ways'] as $wayKey => $payment_way)
            @if(auth()->user()->source == 'pos' && !in_array($wayKey, ['all', 'cash', 'payment_machine']))
                @continue
            @endif
            <option value="{{$wayKey}}" @if(isset(request()->payment_way) && request()->payment_way == $wayKey) selected @endif>{{__($payment_way)}}</option>
            @endforeach
          </select>
        </div><!-- form-group -->
        @if(auth()->user()->source != 'pos')
            <div class="form-group mb-3">
            <select name="source" class="form-control select2-single filter">
                <option @if(!isset(request()->source)) selected @endif disabled> {{ __('Source') }}</option>
                @foreach ($filters['sources'] as $sourceKey => $source)
                <option value="{{$sourceKey}}" @if(isset(request()->source) && request()->source == $sourceKey) selected @endif>{{__($source)}}</option>
                @endforeach
            </select>
            </div><!-- form-group -->
        @endif
        <div class="dateInput position-relative mx-0 mb-3">
          <input class="bg-white border rounded-3 text-body" name="dates" placeholder="Search by day" readonly="readonly">
        </div><!-- dateInput -->
      </div><!-- rightCol -->
      <div class="leftCol">
        @php
          $items = explode("?", request()->fullUrl());
          $query = $items[1]??'';
        @endphp
        <a href="{{ route('reports.paymentRecordExport')}}?{{$query}}" target="_blanck" class="d-flex align-items-center justify-content-center btn-primary rounded-3 border-0 shadow-none mb-3">Excel</a>
      </div><!-- leftCol -->
    </div><!-- filterArea -->

    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      @if ($payments->count() != 0)

      <div class="table-responsive">
        <table class="table table-striped table-hover text-nowrap">
          <thead>
            <tr>
              <th class="text-center">{{ __('Date') }}</th>
              <th class="text-center">{{ __('Transaction Type') }}</th>
              <th class="text-center">{{ __('Reference') }}</th>
              <th class="text-center">{{ __('Payment Method') }}</th>
              <th class="text-center">{{ __('Source') }}</th>
              <th class="text-center">{{ __('Amount') }}</th>
            </tr>
          </thead>
          <tbody>
            @foreach ($payments as $record)
            <tr>
              <td class="text-center">{{$record->created_at}}</td>
              <td class="text-center">{{ __('reports.'.$record->type) }}</td>
              <td class="text-center">{{$record->reference}}</td>
              <td class="text-center">{{ $record->payment_way ? __('reports.'.$record->payment_way) : null }}</td>
              <td class="text-center">{{ $record->source ? __('reports.'.$record->source) : null }}</td>
              <td class="text-center">
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{ fact_number(round($record->amount, 2)) }}  <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              </td>
            </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="5" class="text-center fw-bold">{{ __('Total')}}</td>
              <td class="text-center fw-bold">
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{ $total ?? 0 }}  <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              </td>
            </tr>
          </tfoot>
        </table>
      </div>
      {{ $payments->appends($_GET)->links() }}
      @else
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column">
        <i class="fal fa-file-invoice-dollar"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('There are no data') }}</span>
      </div><!-- no_bills_yet -->
      @endif
    </div><!-- blockArea -->

  </section><!-- statementIndexPage -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/select2/select2.full.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script type="text/javascript">
    $(document).ready(function(){
      var url = new URL($(location).attr("href"));
      var paramName = '';
      var paramValue = '';

      $(".filter").change(function(e){
        var paramName = $(this).attr('name');
        var paramValue = $(this).val();
        console.log(paramName);
        console.log(paramValue);
        var search_params = url.searchParams;

        // new value of "id" is set to "101"
        search_params.set(paramName, paramValue);

        // change the search property of the main url
        url.search = search_params.toString();

        // the new url string
        var new_url = url.toString();

        // output : http://demourl.com/path?id=101&topic=main
        console.log(new_url);
        window.location.replace(new_url);
      });

    });
      function oldParams() {
        var params = ''
        let array1 = [
          'transaction_type',
          'payment_way',
          'source',
        ];
        array1.forEach(i => {
          if(getUrlParameter(i)){
            params += '&'+i+'='+getUrlParameter(i)
          }
        });
        return params;
      }
      var getUrlParameter = function getUrlParameter(sParam) {
          var sPageURL = window.location.search.substring(1),
              sURLVariables = sPageURL.split('&'),
              sParameterName,
              i;

          for (i = 0; i < sURLVariables.length; i++) {
              sParameterName = sURLVariables[i].split('=');

              if (sParameterName[0] === sParam) {
                  return sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
              }
          }
      };

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
          startDate: getUrlParameter('date_start')?getUrlParameter('date_start'): moment().startOf('month').format("MM/DD/YYYY"),
          endDate: getUrlParameter('date_to')?getUrlParameter('date_to'):moment(new Date()).format("MM/DD/YYYY"),
        }, function(start, end, label) {
            var dateParam = '?date_start=' + start.format('MM/DD/YYYY') + '&date_to='+end.format('MM/DD/YYYY')+oldParams();
            window.history.pushState('', '', dateParam);
            location.reload();
        });
      });


  </script>
@endpush
