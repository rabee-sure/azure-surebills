 @extends('layouts.app')

@section('title', __('Bills'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm border-bottom">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Bills')}}</span>
  </div><!-- breadcrump -->

  <section id="billIndexPage">
    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0">{{ __('Bills') }}</h1>
      <a href="{{ route('bills.create')}}" title="{{ __('Create a bill')}}" class="d-flex align-items-center justify-content-center fw-bold text-white rounded-pill">{{ __('Create a bill')}}</a>
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
            <input type="checkbox" class="w-100 h-100 position-absolute" id="refunded" value="refunded" @if(in_array('refunded', request()->get('statuses', [])) ) checked @endif>
            <span class="d-flex align-items-center justify-content-start">{{ __('Refunded') }}</span>
          </label>
        </div><!-- checkboxArea -->
      </div><!-- rightCol -->
      <div class="leftCol position-relative">
        <input id="keyword" class="bg-white border rounded-3 text-body" value="{{request()->get('keyword')}}" placeholder="{{__('Search')}}" >
      </div><!-- leftCol -->
    </div><!-- filterArea -->

    @if($bills->count())
      <div class="billsArea bg-white border shadow-sm rounded-3 overflow-hidden mb-3">
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
      <div class="no_bills_yet border d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 p-3">
        <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 480 480" width="512" xmlns:v="https://vecta.io/nano"><path d="M215 164c0 57.897 47.103 105 105 105s105-47.103 105-105S377.897 59 320 59s-105 47.103-105 105zm194 0c0 49.075-39.925 89-89 89s-89-39.925-89-89 39.925-89 89-89 89 39.925 89 89zm-89-54a8 8 0 0 1 8 8v3.376c9.31 3.303 16 12.195 16 22.624a8 8 0 1 1-16 0 8.01 8.01 0 0 0-8-8 8.01 8.01 0 0 0-8 8v3.237c0 3.518 2.256 6.586 5.614 7.636l9.544 2.982C337.232 161.004 344 170.2 344 180.763V184c0 11.52-8.16 21.166-19 23.473V210a8 8 0 1 1-16 0v-4.68c-7.714-3.996-13-12.05-13-21.32a8 8 0 1 1 16 0 8.01 8.01 0 0 0 8 8 8.01 8.01 0 0 0 8-8v-3.237c0-3.518-2.256-6.586-5.614-7.636l-9.544-2.982C302.768 166.996 296 157.8 296 147.237V144c0-10.43 6.69-19.32 16-22.624V118a8 8 0 0 1 8-8zm130 212v102c0 30.88-25.122 56-56 56H86c-30.878 0-56-25.12-56-56V32C30 14.355 44.355 0 62 0h260c17.645 0 32 14.355 32 32a8 8 0 1 1-16 0c0-8.822-7.178-16-16-16H62c-8.822 0-16 7.178-16 16v392c0 22.056 17.944 40 40 40h268.862C344.467 453.828 338 439.658 338 424V299a8 8 0 1 1 16 0v125c0 22.056 17.944 40 40 40s40-17.944 40-40V322c0-8.822-7.178-16-16-16h-34a8 8 0 1 1 0-16h34c17.645 0 32 14.355 32 32zM100 215a8 8 0 0 1 8-8h68a8 8 0 1 1 0 16h-68a8 8 0 0 1-8-8zm0-90a8 8 0 0 1 8-8h68a8 8 0 1 1 0 16h-68a8 8 0 0 1-8-8zm184 180a8 8 0 0 1-8 8H108a8 8 0 1 1 0-16h168a8 8 0 0 1 8 8zm0 84a8 8 0 0 1-8 8H108a8 8 0 1 1 0-16h168a8 8 0 0 1 8 8z" fill="#999"/></svg>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</span>
      </div><!-- no_bills_yet -->
    @endif
  </section><!-- billIndexPage -->
 
@endsection

@push('footer-scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
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