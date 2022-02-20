@extends('layouts.app')

@section('title', __('Statement'))

@section('content')
<div id="statement_index">
  <div class="row">
    <div class="col-12">
      <div class="mb-2">
        <h1>{{ __('Statement') }}</h1>
        <div class="top-right-button-container">
         <h3>{{ __('Balance') }} : {{ round2(auth()->user()->balance) }} {{ __('SAR')}}</h3>
        </div>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/')}}">{{ __('Home') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Statement')}}</li>
          </ol>
        </nav>

        <div class="filter_area d-flex align-items-center justify-content-between flex-wrap">
          <div class="d-flex align-items-center justify-content-start flex-wrap">
            <select name="transaction_type" class="form-control select2-single filter">
              <option selected disabled>
                @if(request()->transaction_type == 'debit')
                  {{ __('Debit') }}
                @elseif(request()->transaction_type == 'credit')
                  {{ __('Credit') }}
                @else
                  {{ __('Transaction Type') }}
                @endif
              </option>
              <option value="all">{{ __('All') }}</option>
              <option value="debit">{{ __('Debit') }}</option>
              <option value="credit">{{ __('Credit') }}</option>
            </select>
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
            @if(count($channels))
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
            @endif
            <div class="search-sm calendar-sm">
              <input class="form-control" name="dates" placeholder="Search by day" readonly="readonly">
            </div><!-- search-sm -->
          </div><!-- d-flex -->
          <div class="exel_btn">
            @php
              $items = explode("?", request()->fullUrl());
              $query = $items[1]??'';
            @endphp
            <a href="{{ route('statement.export')}}?{{$query}}" target="_blanck" class="btn btn-success btn-xs">Excel</a>
          </div><!-- exel_btn -->
        </div><!-- filter_area -->

        <div class="separator mb-5"></div>
      </div><!-- mb-2 -->
    </div><!-- col-12 -->
  </div><!-- row -->
  @if($statement->count())
    <div class="row">
      <div class="col-12 list" data-check-all="checkAll">
        <div class="table-responsive">
          <table class="table table-striped text-center">
            <thead>
              <tr>
                <th>{{ __('Payment Date') }}</th>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Reference') }}</th>
                <th>{{ __('Receipt') }}</th>
              @if(count($channels))
                <th>{{ __('Application') }}</th>
              @endif
                <th>{{ __('Card') }}</th>
                <th>{{ __('Debit') }}</th>
                <th>{{ __('Credit') }}</th>
                <th>{{ __('Balance') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($statement as $transaction)
                <tr>
                  <td>{{ $transaction->created_at }}</td>
                  <td>{{ $transaction->description }}</td>
                  <td>{{ $transaction->reference }}</td>
                  <td>{{ $transaction->receipt }}</td>
                  @if(count($channels))
                    <td>
                      @if(isset($transaction->bill->application_id) && isset ($transaction->bill->application->channel_id))
                          
                        {{$transaction->bill->application_id}} - {{ $transaction->bill->user->business_name}}
                      @else
                      --
                      @endif
                    </td>
                  @endif
                  <td>
                    @if ($transaction->card_brand == 'VISA')
                      <img alt="mastercard" src="images/cards/visa.gif" class="mr-1" width="18px"> 
                    @elseif ($transaction->card_brand == 'MASTER')
                      <img alt="mastercard" src="images/cards/mastercard.gif" class="mr-1" width="18px"> 
                    @elseif ($transaction->card_brand == 'MADA')
                      <img alt="mastercard" src="images/cards/mada.gif" class="mr-1" width="18px"> 
                    @elseif ($transaction->card_brand == 'APPLEPAY')
                      <img alt="mastercard" src="images/cards/applepay.gif" class="mr-1" width="18px"> 
                    @endif
                    {{ $transaction->card }}
                  </td>
                  <td class="text-danger">{{ $transaction->type == 'debit' ? round2($transaction->amount) : '-' }}</td>
                  <td class="text-success">{{ $transaction->type == 'credit' ? round2($transaction->amount) : '-' }}</td>
                  <td>{{ fact_number(round($transaction->balance, 2)) }}</td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="@if(count($channels)) 6 @else 5 @endif">{{ __('Total')}}</td>
                <td class="text-danger">{{ $totals['debit'] ?? 0 }}</td>
                <td class="text-success">{{ $totals['credit'] ?? 0 }}</td>
                <td>{{ $totals['all'] ?? 0 }}</td>
              </tr>
            </tfoot>
          </table>
        </div>
          {{ $statement->appends($_GET)->links() }}
      </div>
    </div>
  @else
    <div class="no_bills_yet">
      <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 480 480" width="512" xmlns:v="https://vecta.io/nano"><path d="M215 164c0 57.897 47.103 105 105 105s105-47.103 105-105S377.897 59 320 59s-105 47.103-105 105zm194 0c0 49.075-39.925 89-89 89s-89-39.925-89-89 39.925-89 89-89 89 39.925 89 89zm-89-54a8 8 0 0 1 8 8v3.376c9.31 3.303 16 12.195 16 22.624a8 8 0 1 1-16 0 8.01 8.01 0 0 0-8-8 8.01 8.01 0 0 0-8 8v3.237c0 3.518 2.256 6.586 5.614 7.636l9.544 2.982C337.232 161.004 344 170.2 344 180.763V184c0 11.52-8.16 21.166-19 23.473V210a8 8 0 1 1-16 0v-4.68c-7.714-3.996-13-12.05-13-21.32a8 8 0 1 1 16 0 8.01 8.01 0 0 0 8 8 8.01 8.01 0 0 0 8-8v-3.237c0-3.518-2.256-6.586-5.614-7.636l-9.544-2.982C302.768 166.996 296 157.8 296 147.237V144c0-10.43 6.69-19.32 16-22.624V118a8 8 0 0 1 8-8zm130 212v102c0 30.88-25.122 56-56 56H86c-30.878 0-56-25.12-56-56V32C30 14.355 44.355 0 62 0h260c17.645 0 32 14.355 32 32a8 8 0 1 1-16 0c0-8.822-7.178-16-16-16H62c-8.822 0-16 7.178-16 16v392c0 22.056 17.944 40 40 40h268.862C344.467 453.828 338 439.658 338 424V299a8 8 0 1 1 16 0v125c0 22.056 17.944 40 40 40s40-17.944 40-40V322c0-8.822-7.178-16-16-16h-34a8 8 0 1 1 0-16h34c17.645 0 32 14.355 32 32zM100 215a8 8 0 0 1 8-8h68a8 8 0 1 1 0 16h-68a8 8 0 0 1-8-8zm0-90a8 8 0 0 1 8-8h68a8 8 0 1 1 0 16h-68a8 8 0 0 1-8-8zm184 180a8 8 0 0 1-8 8H108a8 8 0 1 1 0-16h168a8 8 0 0 1 8 8zm0 84a8 8 0 0 1-8 8H108a8 8 0 1 1 0-16h168a8 8 0 0 1 8 8z" fill="#999"/></svg>
      <span>{{ __('No Bill Matched The Given Criteria.') }}</span>
    </div><!-- no_bills_yet -->
  @endif
</div><!-- statement_index -->
@endsection

@push('footer-scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <script type="text/javascript" src="{{ asset('js/select2.full.js') }}"></script>
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2.min.css') }}" />
  <link rel="stylesheet" type="text/css" href="{{ asset('css/select2-bootstrap.min.css') }}" />
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
