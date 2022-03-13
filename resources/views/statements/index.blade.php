@extends('layouts.app')

@section('title', __('Statement'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2.min.css') }}?v={{ config('app.asset_version') }}">
  <link rel="stylesheet" href="{{ asset('new/css/plugins/select2/select2-bootstrap.min.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Statement') }}</span>
  </div><!-- breadcrump -->

  <section id="statementIndexPage">

    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Statement') }}</h1>
      <h2 class="d-block fw-bold m-0 fs-6">{{ __('Balance') }} : {{ round2(auth()->user()->balance) }} {{ __('SAR')}}</h2>
    </div><!-- title -->

    <div class="filterArea mb-3 d-flex align-items-end justify-content-between flex-wrap flex-column flex-lg-row">
      <div class="rightCol d-flex align-items-end justify-content-start flex-wrap flex-grow-1">
        <div class="form-group mb-3">
          <select name="transaction_type" class="form-control select2-single filter">
            <option selected disabled>
            @if(request()->transaction_type == 'debit')
                {{ __('Debit') }}
              @elseif(request()->transaction_type == 'credit')
                {{ __('Credit') }}
              @else
                {{ __('Transaction Type') }}
              @case('channel_fees')
                {{ __('Channel Fees') }}
              @break
              @case('channel_vat')
                {{ __('Channel VAT') }}
              @break
              @case('fees')
                {{ __('Bill Fees') }}
              @break
              @case('vat')
                {{ __('Bill VAT') }}
              @break
              @case('transfer')
                {{ __('Transfer') }}
              @break
              @case('refund')
                {{ __('Refunded') }}
              @break
              @default
              {{ __('Transactions') }}
            @endswitch
            </option>
            <option value="all">{{ __('All') }}</option>
            <option value="debit">{{ __('Debit') }}</option>
            <option value="credit">{{ __('Credit') }}</option>
          </select>
        </div><!-- form-group -->
        <div class="form-group mb-3">
          <select name="transaction_source" class="form-control select2-single filter" @if(request()->transaction_type != 'credit' && request()->transaction_type != 'debit') disabled @endif>
            <option selected disabled>
              @switch(request()->transaction_source)
                @case('bill')
                    {{ __('Bill') }}
                    @break

                @case('channel_fees')
                    {{ __('Channel Fees') }}
                    @break                    

                @case('channel_vat')
                    {{ __('Channel VAT') }}
                    @break                   

                @case('fees')
                    {{ __('Bill Fees') }}
                    @break                  

                @case('vat')
                    {{ __('Bill VAT') }}
                    @break                 

                @case('transfer')
                    {{ __('Transfer') }}
                    @break 

                @case('refund')
                    {{ __('Refunded') }}
                    @break

                @default
                    {{ __('Transactions') }}
              @endswitch
            </option>
            <option value="all">{{ __('All') }}</option>
            @if(request()->transaction_type == 'credit')
              <option value="bill">{{ __('Bill') }}</option>
              @if(count($channels))
                <option value="channel_fees">{{ __('Channel Fees') }}</option>
                <option value="channel_vat">{{ __('Channel VAT') }}</option>
              @endif
            @elseif(request()->transaction_type == 'debit')
              <option value="fees">{{ __('Bill Fees') }}</option>
              <option value="vat">{{ __('Bill VAT') }}</option>
              <option value="transfer">{{ __('Transfer') }}</option>
            @endif
            <option value="refund">{{ __('Refunded') }}</option>
          </select>
        </div><!-- form-group -->
        @if(count($channels))
          <div class="form-group mb-3">
            <select name="channel_id" class="form-control select2-single filter">
              <option selected disabled>
                @if(isset($channel))
                  {{ $channel->name}}
                @else
                  {{ __('Channels') }}
                @endif
              </option>
              <option value="all">{{ __('All') }}</option>
              @foreach($channels as $channel)
                <option value="{{$channel->id}}">{{$channel->name}}</option>
              @endforeach
            </select>
          </div><!-- form-group -->
          <div class="form-group mb-3">
            <select name="application_id" class="form-control select2-single filter" @if(count($applications) == 0) disabled @endif>
              <option selected disabled>
                @if(isset($application))
                  {{$application->id}} - {{ $application->user->business_name}}
                @else
                  {{ __('Applications') }}
                @endif
              </option>
              <option value="all">{{ __('All') }}</option>
              @if($applications)
                @foreach($applications as $application)
                  <option value="{{$application->id}}">{{$application->id}} - {{ $application->user->business_name }}</option>
                @endforeach
              @endif
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
        <a href="{{ route('statement.export')}}?{{$query}}" target="_blanck" class="d-flex align-items-center justify-content-center btn-primary rounded-3 border-0 shadow-none mb-3">Excel</a>
      </div><!-- leftCol -->
    </div><!-- filterArea -->

    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      @if($statement->count())
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th class="text-center">{{ __('Payment Date') }}</th>
                <th class="text-center">{{ __('Description') }}</th>
                <th class="text-center">{{ __('Reference') }}</th>
                <th class="text-center">{{ __('Receipt') }}</th>
                @if(count($channels))
                  <th class="text-center">{{ __('Application') }}</th>
                @endif
                <th class="text-center">{{ __('Card') }}</th>
                <th class="text-center">{{ __('Debit') }}</th>
                <th class="text-center">{{ __('Credit') }}</th>
                <th class="text-center">{{ __('Balance') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($statement as $transaction)
                <tr>
                  <td class="text-center">{{ $transaction->created_at }}</td>
                  <td class="text-center">{{ $transaction->description }}</td>
                  <td class="text-center">{{ $transaction->reference }}</td>
                  <td class="text-center">{{ $transaction->receipt }}</td>
                  @if(count($channels))
                    <td class="text-center">
                      @if(isset($transaction->bill->application_id) && isset ($transaction->bill->application->channel_id))

                        {{$transaction->bill->application_id}} - {{ $transaction->bill->user->business_name}}
                      @else
                      --
                      @endif
                    </td>
                  @endif
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      @if ($transaction->card_brand == 'VISA')
                        <img alt="mastercard" src="images/cards/visa.gif" width="18px"> 
                      @elseif ($transaction->card_brand == 'MASTER')
                        <img alt="mastercard" src="images/cards/mastercard.gif" width="18px"> 
                      @elseif ($transaction->card_brand == 'MADA')
                        <img alt="mastercard" src="images/cards/mada.gif" width="18px"> 
                      @elseif ($transaction->card_brand == 'APPLEPAY')
                        <img alt="mastercard" src="images/cards/applepay.gif" width="18px"> 
                      @endif
                      {{ $transaction->card }}
                    </div>
                  </td>
                  <td class="text-danger text-center">{{ $transaction->type == 'debit' ? round2($transaction->amount) : '-' }}</td>
                  <td class="text-success text-center">{{ $transaction->type == 'credit' ? round2($transaction->amount) : '-' }}</td>
                  <td class="text-center">{{ fact_number(round($transaction->balance, 2)) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="@if(count($channels)) 6 @else 5 @endif" class="text-center fw-bold">{{ __('Total')}}</td>
                <td class="text-danger text-center fw-bold">{{ $totals['debit'] ?? 0 }}</td>
                <td class="text-success text-center fw-bold">{{ $totals['credit'] ?? 0 }}</td>
                <td class="text-center fw-bold">{{ $totals['all'] ?? 0 }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
        {{ $statement->appends($_GET)->links() }}
      @else
        <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column">
          <i class="fal fa-file-invoice-dollar"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</span>
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

        if(paramName == 'transaction_type'){
          search_params.set('transaction_source', 'all');
        }

        if(paramName == 'channel_id'){
          search_params.set('application_id', 'all');
        }
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
          'transaction_source',
          'channel_id',
          'application_id',
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
