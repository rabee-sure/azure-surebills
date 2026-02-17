@extends('layouts.app')

@section('title', __('Payment Record'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')

  <section id="payment-record-index-page">

    <h4 class="mb-6">{{ __('Payment Record') }}</h4>

    <div class="card mb-6">
      <div class="card-body p-3">
        <div class="row row-cols-2 row-cols-md-4 g-3">
          <div class="col">
            <select name="transaction_type" class="form-control select2-single filter">
              <option @if(!isset(request()->transaction_type)) selected @endif disabled> {{ __('Transaction Type') }}</option>
              @foreach ($filters['transaction_types'] as $typeKey => $transaction_type)
              <option value="{{$typeKey}}" @if(isset(request()->transaction_type) && request()->transaction_type == $typeKey) selected @endif>{{__($transaction_type)}}</option>
              @endforeach
            </select>
          </div><!-- col -->
          <div class="col">
            <select name="payment_way" class="form-control select2-single filter">
              <option @if(!isset(request()->payment_way)) selected @endif disabled> {{ __('Payment Method') }}</option>
              @foreach ($filters['payment_ways'] as $wayKey => $payment_way)
              @if(auth()->user()->source == 'pos' && !in_array($wayKey, ['all', 'cash', 'payment_machine']))
                  @continue
              @endif
              <option value="{{$wayKey}}" @if(isset(request()->payment_way) && request()->payment_way == $wayKey) selected @endif>{{__($payment_way)}}</option>
              @endforeach
            </select>
          </div><!-- col -->
          @if(auth()->user()->source != 'pos')
            <div class="col">
              <select name="source" class="form-control select2-single filter">
                <option @if(!isset(request()->source)) selected @endif disabled> {{ __('Source') }}</option>
                @foreach ($filters['sources'] as $sourceKey => $source)
                <option value="{{$sourceKey}}" @if(isset(request()->source) && request()->source == $sourceKey) selected @endif>{{__($source)}}</option>
                @endforeach
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
      <a href="{{ route('reports.paymentRecordExport')}}?{{$query}}" title="{{ __('Export Payment Record')}}" class="excelBtn d-flex align-items-center justify-content-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 -1.27 110.037 110.037"><path d="M57.55 0h7.425v10c12.513 0 25.025.025 37.537-.038 2.113.087 4.438-.062 6.275 1.2 1.287 1.85 1.138 4.2 1.225 6.325-.062 21.7-.037 43.388-.024 65.075-.062 3.638.337 7.35-.425 10.938-.5 2.6-3.625 2.662-5.713 2.75-12.95.037-25.912-.025-38.875 0v11.25h-7.763c-19.05-3.463-38.138-6.662-57.212-10V10.013C19.188 6.675 38.375 3.388 57.55 0z" fill="#207245"/><path d="M64.975 13.75h41.25V92.5h-41.25V85h10v-8.75h-10v-5h10V62.5h-10v-5h10v-8.75h-10v-5h10V35h-10v-5h10v-8.75h-10v-7.5z" fill="#fff"/><path d="M79.975 21.25h17.5V30h-17.5v-8.75z" fill="#207245"/><path d="M37.025 32.962c2.825-.2 5.663-.375 8.5-.512a2607.344 2607.344 0 0 1-10.087 20.487c3.438 7 6.949 13.95 10.399 20.95a716.28 716.28 0 0 1-9.024-.575c-2.125-5.213-4.713-10.25-6.238-15.7-1.699 5.075-4.125 9.862-6.074 14.838-2.738-.038-5.476-.15-8.213-.263C19.5 65.9 22.6 59.562 25.912 53.312c-2.812-6.438-5.9-12.75-8.8-19.15 2.75-.163 5.5-.325 8.25-.475 1.862 4.888 3.899 9.712 5.438 14.725 1.649-5.312 4.112-10.312 6.225-15.45z" fill="#fff"/><path d="M79.975 35h17.5v8.75h-17.5V35zm0 13.75h17.5v8.75h-17.5v-8.75zm0 13.75h17.5v8.75h-17.5V62.5zm0 13.75h17.5V85h-17.5v-8.75z" fill="#207245"/></svg>
      </a>
    </div><!-- d-flex -->

    <div class="card">
      @if ($payments->count() != 0)
        <div class="table-responsive text-nowrap">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th class="fw-bold">{{ __('Date') }}</th>
                <th class="fw-bold">{{ __('Transaction Type') }}</th>
                <th class="fw-bold">{{ __('Reference') }}</th>
                <th class="fw-bold">{{ __('Payment Method') }}</th>
                <th class="fw-bold">{{ __('Source') }}</th>
                <th class="fw-bold">{{ __('Amount') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach ($payments as $record)
                <tr>
                  <td>{{$record->created_at}}</td>
                  <td>{{ __('reports.'.$record->type) }}</td>
                  <td>{{$record->reference}}</td>
                  <td>{{ $record->payment_way ? __('reports.'.$record->payment_way) : null }}</td>
                  <td>{{ $record->source ? __('reports.'.$record->source) : null }}</td>
                  <td>
                    <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                      {{ fact_number(round($record->amount, 2)) }} <i class="sar-icon"></i>
                    </span>
                  </td>
                </tr>
              @endforeach
            </tbody>
            <tfoot>
              <tr>
                <td colspan="5" class="fw-bold">{{ __('Total')}}</td>
                <td>
                  <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 fw-bold">
                  {{ $total ?? 0 }} <i class="sar-icon"></i>
                  </span>
                </td>
              </tr>
            </tfoot>
          </table>
        </div><!-- table-responsive -->
        {{ $payments->appends($_GET)->links() }}
      @else
        <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column py-5">
          <i class="ti ti-logs ti-xl"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('There are no data') }}</span>
        </div><!-- no_bills_yet -->
      @endif
    </div><!-- card -->

  </section><!-- payment-record-index-page -->

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
        allowClear: false
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
