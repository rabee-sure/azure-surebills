@extends('layouts.app')

@section('title', __('Transactions'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('/transfers')}}" title="{{ __('Transfers') }}">{{ __('Transfers') }}</a>
  </div><!-- breadcrump -->

  <div id="errors" class="d-print-none">
    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div><!-- table_items -->
    @endif
  </div><!-- alert -->

  @if(session()->has('success'))
      <div class="alert alert-success">
          {{ session()->get('success') }}
      </div>
  @endif

  <section id="transactionsPage">
    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Transfer Transactions') }}</h1>
      <a href="{{ route('transfer.export_bills', ['transfer' => $transfer])}}" title="{{ __('Export Transfer Bills')}}" class="d-flex align-items-center justify-content-center btn-primary text-white rounded-pill border-0 shadow-none" style="padding: 12px;">{{ __('Export Transfer Bills')}}</a>
    </div><!-- title -->

    <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      @if($transactions->count())
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th class="text-center">{{ __('Payment Date') }}</th>
                <th class="text-center">{{ __('Description') }}</th>
                <th class="text-center">{{ __('Reference') }}</th>
                <th class="text-center">{{ __('Receipt') }}</th>
                <th class="text-center">{{ __('Card') }}</th>
                <th class="text-center">{{ __('Debit') }}</th>
                <th class="text-center">{{ __('Credit') }}</th>
                <th class="text-center">{{ __('Balance') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($transactions as $transaction)
                <tr>
                  <td class="text-center">{{ $transaction->created_at }}</td>
                  <td class="text-center">{{ $transaction->description }}</td>
                  <td class="text-center">{{ $transaction->reference }}</td>
                  <td class="text-center">{{ $transaction->receipt }}</td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                      @if ($transaction->card_brand == 'VISA')
                        <img alt="mastercard" src="/images/cards/visa.gif" width="18px"> 
                      @elseif ($transaction->card_brand == 'MASTER')
                        <img alt="mastercard" src="/images/cards/mastercard.gif" width="18px"> 
                      @elseif ($transaction->card_brand == 'MADA')
                        <img alt="mastercard" src="/images/cards/mada.gif" width="18px"> 
                      @elseif ($transaction->card_brand == 'APPLEPAY')
                        <img alt="mastercard" src="/images/cards/applepay.gif" width="18px"> 
                      @endif
                      {{ $transaction->card }}
                    </div>
                  </td>
                  <td class="text-danger text-center">
                    <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                      {{ $transaction->type == 'debit' ? round2($transaction->amount) : '0' }}  <span class="riyal-symbol-font">$</span>
                    </div><!-- d-flex -->
                  </td>
                  <td class="text-success text-center">
                    <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                      {{ $transaction->type == 'credit' ? round2($transaction->amount) : '0' }}  <span class="riyal-symbol-font">$</span>
                    </div><!-- d-flex -->
                  </td>
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                      {{ fact_number(round($transaction->balance, 2)) }}  <span class="riyal-symbol-font">$</span>
                    </div><!-- d-flex -->
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5" class="text-center fw-bold">{{ __('Total')}}</td>
                <td class="text-danger text-center fw-bold">
                  <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                    {{ $totals['debit'] ?? 0 }}  <span class="riyal-symbol-font">$</span>
                  </div><!-- d-flex -->
                </td>
                <td class="text-success text-center fw-bold">
                  <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                    {{ $totals['credit'] ?? 0 }}  <span class="riyal-symbol-font">$</span>
                  </div><!-- d-flex -->
                </td>
                <td class="text-center fw-bold">
                  <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                    {{ $totals['all'] ?? 0 }}  <span class="riyal-symbol-font">$</span>
                  </div><!-- d-flex -->
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
        {{ $transactions->links() }}
      @else
        <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column">
          <i class="fal fa-file-invoice-dollar"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</span>
        </div><!-- no_bills_yet -->
      @endif
    </div><!-- blockArea -->

  </section><!-- transactionsPage -->

  
    <div class="row">
      <div class="col-12 list" data-check-all="checkAll">
        
      </div>
    </div>
@endsection

@push('footer-scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <script type="text/javascript">
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
