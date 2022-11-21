 @extends('layouts.app')

@section('title', __('Bills'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('new/css/plugins/daterangepicker/daterangepicker.css') }}?v={{ config('app.asset_version') }}">
@endsection

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Bills')}}</span>
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

  <section id="billIndexPage">
    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Bills') }}</h1>
      <div class="btnsArea d-flex align-items-center justify-content-end gap-3">
        @can('create bills')
          @if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0))
            <a href="{{ route('bills.create')}}" title="{{ __('Create a bill')}}" class="d-flex align-items-center justify-content-center btn-primary text-white rounded-pill border-0 shadow-none">{{ __('Create a bill')}}</a>
          @endif
        @endcan
      </div><!-- btnsArea -->
    </div><!-- title -->

    <div class="filterArea mb-3 d-flex align-items-end justify-content-between flex-wrap">
      <div class="rightCol d-flex align-items-start justify-content-start flex-column">
        <div class="dateInput position-relative mb-3">
          <input class="bg-white border rounded-3 text-body" name="dates" placeholder="Search by day" readonly="readonly">
        </div><!-- dateInput -->
        <div class="checkboxArea d-flex align-items-center justify-content-start flex-wrap mb-3 mb-lg-0">
          <label for="customCheckThis" class="mb-2 mb-md-0 position-relative">
            <input type="checkbox" class="w-100 h-100 position-absolute" id="customCheckThis" value="pending" @if(in_array('pending', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Unpaid') }}</span>
          </label>
          <label for="paid" class="mb-2 mb-md-0 position-relative">
            <input type="checkbox" class="w-100 h-100 position-absolute" id="paid" value="paid" @if(in_array('paid', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Paid') }}</span>
          </label>
          <label for="expired" class="mb-2 mb-md-0 position-relative">
            <input type="checkbox" class="w-100 h-100 position-absolute" id="expired" value="expired" @if(in_array('expired', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Expired') }}</span>
          </label>
          <label for="canceled" class="mb-2 mb-md-0 position-relative">
            <input type="checkbox" class="w-100 h-100 position-absolute" id="canceled" value="canceled" @if(in_array('canceled', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Canceled') }}</span>
          </label>
          <label for="failed" class="mb-2 mb-md-0 position-relative">
          <input type="checkbox" class="w-100 h-100 position-absolute" id="failed" value="failed" @if(in_array('failed', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Failed') }}</span>
          </label>
          <label for="refunded" class="mb-2 mb-md-0 position-relative">
            <input type="checkbox" class="w-100 h-100 position-absolute" id="refunded" value="cn_refunded" @if(in_array('cn_refunded', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Refunded') }}</span>
          </label>
        </div><!-- checkboxArea -->
      </div><!-- rightCol -->
      <div class="leftCol d-flex align-items-end justify-content-between align-self-stretch flex-column">
        @if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0))
          <a href="{{ route('export.bills', request( )->input( ))}}" title="{{ __('Export bills')}}" class="excelBtn d-flex align-items-center justify-content-center mb-3">
            <svg xmlns="http://www.w3.org/2000/svg" width="110.037" height="110.037" viewBox="0 -1.27 110.037 110.037"><path d="M57.55 0h7.425v10c12.513 0 25.025.025 37.537-.038 2.113.087 4.438-.062 6.275 1.2 1.287 1.85 1.138 4.2 1.225 6.325-.062 21.7-.037 43.388-.024 65.075-.062 3.638.337 7.35-.425 10.938-.5 2.6-3.625 2.662-5.713 2.75-12.95.037-25.912-.025-38.875 0v11.25h-7.763c-19.05-3.463-38.138-6.662-57.212-10V10.013C19.188 6.675 38.375 3.388 57.55 0z" fill="#207245"/><path d="M64.975 13.75h41.25V92.5h-41.25V85h10v-8.75h-10v-5h10V62.5h-10v-5h10v-8.75h-10v-5h10V35h-10v-5h10v-8.75h-10v-7.5z" fill="#fff"/><path d="M79.975 21.25h17.5V30h-17.5v-8.75z" fill="#207245"/><path d="M37.025 32.962c2.825-.2 5.663-.375 8.5-.512a2607.344 2607.344 0 0 1-10.087 20.487c3.438 7 6.949 13.95 10.399 20.95a716.28 716.28 0 0 1-9.024-.575c-2.125-5.213-4.713-10.25-6.238-15.7-1.699 5.075-4.125 9.862-6.074 14.838-2.738-.038-5.476-.15-8.213-.263C19.5 65.9 22.6 59.562 25.912 53.312c-2.812-6.438-5.9-12.75-8.8-19.15 2.75-.163 5.5-.325 8.25-.475 1.862 4.888 3.899 9.712 5.438 14.725 1.649-5.312 4.112-10.312 6.225-15.45z" fill="#fff"/><path d="M79.975 35h17.5v8.75h-17.5V35zm0 13.75h17.5v8.75h-17.5v-8.75zm0 13.75h17.5v8.75h-17.5V62.5zm0 13.75h17.5V85h-17.5v-8.75z" fill="#207245"/></svg>
          </a>
        @endif
        <div class="searchField position-relative">
          <input id="keyword" class="bg-white border rounded-3 text-body" value="{{request()->get('keyword')}}" placeholder="{{__('Search')}}" >
        </div><!-- searchField -->
      </div><!-- leftCol -->
    </div><!-- filterArea -->

    @if($bills->count())
      <div class="billsArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th scope="col">{{__('Name') }}</th>
                <th scope="col" class="text-center">{{__('Values') }}</th>
                <th scope="col" class="text-center">{{__('Date created') }}</th>
                <th scope="col" class="text-center" width="10%">{{__('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($bills as $bill)
                @include('bills.item')
              @endforeach
            </tbody>
          </table>
        </div><!-- table-responsive -->
        {{ $bills->appends($_GET)->links() }}
      </div><!-- billsArea -->
    @else
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 p-3">
        <i class="fal fa-file-invoice-dollar"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</span>
      </div><!-- no_bills_yet -->
    @endif
  </section><!-- billIndexPage -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('new/js/daterangepicker/moment.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script src="{{ asset('new/js/daterangepicker/daterangepicker.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script type="text/javascript">

      function oldParams(type) {
        var params = ''
        if(getUrlParameter('date_start') && type != 2){
          params += '&date_start='+getUrlParameter('date_start')
        }
        if(getUrlParameter('date_to') && type != 2){
          params += '&date_to='+getUrlParameter('date_to')
        }
        if(getParams()['statuses[]'] && type != 1){
          getParams()['statuses[]'].forEach(element => params += '&statuses[]='+element );
        }
        if(getUrlParameter('keyword') && type != 3){
          params += '&keyword='+getUrlParameter('keyword')
        }
        return params;
      }

      function getParams() {
          var url = window.location.href;
          var regex = /([^=&?]+)=([^&#]*)/g, params = {}, parts, key, value;

          while((parts = regex.exec(url)) != null) {

              key = parts[1], value = parts[2];
              var isArray = /\[\]$/.test(key);

              if(isArray) {
                  params[key] = params[key] || [];
                  params[key].push(value);
              }
              else {
                  params[key] = value;
              }
          }
          return params;
      }

      $('label').on('change', function() {
          var names = [];
          $('input:checked').each(function() {
              names.push('statuses[]='+this.value);
          });

          var dateParam = '?'+names.join('&')+oldParams(1);
          window.history.pushState('', '', dateParam);
          location.reload();
      });


      //watch Keword
      var searchTimer = null,
      minLength = 3,
      searchDelay = 1000;
      $('#keyword').on("input", function() {
        clearTimeout(searchTimer);
        var searchVal = this.value;
        // start new timer
        searchTimer = setTimeout(function() {
          var dateParam = '?'+'keyword='+searchVal+oldParams(3);
          window.history.pushState('', '', dateParam);
          location.reload();

        }, searchDelay);
      });
      //focus in search
      var q = $('#keyword').val();
      $('#keyword').focus().val('').val(q);


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
          weekStart: 6,
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
            var dateParam = '?date_start=' + start.format('MM/DD/YYYY') + '&date_to='+end.format('MM/DD/YYYY')+oldParams(2);
            window.history.pushState('', '', dateParam);
            location.reload();
        });
      });

  </script>
@endpush
