@extends('layouts.app')

@section('title', __('Statement'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')

<section id="statement-index-page">

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <h4 class="m-0 flex-grow-1">{{ __('Electronic payment') }}</h4>
    <h6 class="text-success bg-label-success rounded-3 py-2 px-3 m-0 d-flex align-items-center justify-content-end gap-1">
      {{ __('Balance') }} :
      <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
        {{ round2(auth()->user()->balance) }} <i class="sar-icon"></i>
      </span>
    </h6>
  </div><!-- d-flex -->

  <div class="card mb-6">
    <div class="card-body p-3">
      <div class="row row-cols-2 row-cols-md-5 g-3">
        <div class="col">
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
        </div><!-- col -->
        <div class="col">
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
        </div><!-- col -->
        @if(count($channels))
          <div class="col">
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
          </div><!-- col -->
          <div class="col">
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
          </div><!-- col -->
        @endif
        <div class="col">
          <input class="form-control" name="dates" placeholder="Search by day" readonly="readonly">
        </div><!-- col -->
      </div><!-- row -->
    </div><!-- card-body -->
  </div><!-- card -->

  <div class="d-flex align-items-center justify-content-end mb-3">
    @php
      $items = explode("?", request()->fullUrl());
      $query = $items[1]??'';
    @endphp
    <a href="{{ route('statement.export')}}?{{$query}}" title="{{ __('Export Electronic Payment')}}" class="excelBtn d-flex align-items-center justify-content-center">
      <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 -1.27 110.037 110.037"><path d="M57.55 0h7.425v10c12.513 0 25.025.025 37.537-.038 2.113.087 4.438-.062 6.275 1.2 1.287 1.85 1.138 4.2 1.225 6.325-.062 21.7-.037 43.388-.024 65.075-.062 3.638.337 7.35-.425 10.938-.5 2.6-3.625 2.662-5.713 2.75-12.95.037-25.912-.025-38.875 0v11.25h-7.763c-19.05-3.463-38.138-6.662-57.212-10V10.013C19.188 6.675 38.375 3.388 57.55 0z" fill="#207245"/><path d="M64.975 13.75h41.25V92.5h-41.25V85h10v-8.75h-10v-5h10V62.5h-10v-5h10v-8.75h-10v-5h10V35h-10v-5h10v-8.75h-10v-7.5z" fill="#fff"/><path d="M79.975 21.25h17.5V30h-17.5v-8.75z" fill="#207245"/><path d="M37.025 32.962c2.825-.2 5.663-.375 8.5-.512a2607.344 2607.344 0 0 1-10.087 20.487c3.438 7 6.949 13.95 10.399 20.95a716.28 716.28 0 0 1-9.024-.575c-2.125-5.213-4.713-10.25-6.238-15.7-1.699 5.075-4.125 9.862-6.074 14.838-2.738-.038-5.476-.15-8.213-.263C19.5 65.9 22.6 59.562 25.912 53.312c-2.812-6.438-5.9-12.75-8.8-19.15 2.75-.163 5.5-.325 8.25-.475 1.862 4.888 3.899 9.712 5.438 14.725 1.649-5.312 4.112-10.312 6.225-15.45z" fill="#fff"/><path d="M79.975 35h17.5v8.75h-17.5V35zm0 13.75h17.5v8.75h-17.5v-8.75zm0 13.75h17.5v8.75h-17.5V62.5zm0 13.75h17.5V85h-17.5v-8.75z" fill="#207245"/></svg>
    </a>
  </div><!-- d-flex -->

  @if($statement->count())
    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th class="fw-bold">{{ __('Payment Date') }}</th>
              <th class="fw-bold">{{ __('Description') }}</th>
              <th class="fw-bold">{{ __('Reference') }}</th>
              <th class="fw-bold">{{ __('Receipt') }}</th>
              @if(count($channels))
                <th class="fw-bold">{{ __('Application') }}</th>
              @endif
              <th class="fw-bold">{{ __('Card') }}</th>
              <th class="fw-bold">{{ __('Debit') }}</th>
              <th class="fw-bold">{{ __('Credit') }}</th>
              <th class="fw-bold">{{ __('Balance') }}</th>
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
                    <!-- @if(isset($transaction->bill->application_id) && isset ($transaction->bill->application->channel_id))
                      {{$transaction->bill->application_id}} - {{ $transaction->bill->user->business_name}}
                    @else
                    --
                    @endif -->
                    --
                  </td>
                @endif
                <td>
                  <div class="d-flex align-items-center justify-content-start gap-1">
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
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-danger">
                    {{ $transaction->type == 'debit' ? round2($transaction->amount) : '0' }} <i class="sar-icon"></i>
                  </span>
                </td>
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-success">
                    {{ $transaction->type == 'credit' ? round2($transaction->amount) : '0' }} <i class="sar-icon"></i>
                  </span>
                </td>
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
                    {{ fact_number(round($transaction->balance, 2)) }} <i class="sar-icon"></i>
                  </span>
                </td>
              </tr>
            @endforeach
          </tbody>
          <tfoot>
            <tr>
              <td colspan="@if(count($channels)) 6 @else 5 @endif" class="fw-bold">{{ __('Total')}}</td>
              <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-danger fw-bold">
                    {{ $totals['debit'] ?? 0 }} <i class="sar-icon"></i>
                  </span>

              <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-success fw-bold">
                    {{ $totals['credit'] ?? 0 }} <i class="sar-icon"></i>
                  </span>
              </td>
              <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 fw-bold">
                    {{ $totals['all'] ?? 0 }} <i class="sar-icon"></i>
                  </span>
              </td>
            </tr>
          </tfoot>
        </table>
      </div><!-- table-responsive -->
    </div><!-- card -->
    <div class="d-flex align-items-center justify-content-center mt-3">
      {{ $statement->appends($_GET)->links() }}
    </div><!-- d-flex -->
  @else
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center flex-column py-5">
        <i class="ti ti-credit-card-pay ti-xl"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('There are no data') }}</span>
        </div><!-- d-flex -->
      </div><!-- card-body -->
    </div><!-- card -->
  @endif

</section><!-- statement-index-page -->
@endsection

@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}"></script>
  <script type="text/javascript">
    // --------------------------------------------------------------------
    // Select2
    // --------------------------------------------------------------------
    $(document).ready(function() {
      $('.select2-single').select2({
        placeholder: '{{ __('Select Payment Status') }}',
        allowClear: true
      });
    });

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
