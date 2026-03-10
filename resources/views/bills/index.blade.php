@extends('layouts.app')

@section('title', __('Bills'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <h4 class="m-0 flex-grow-1">{{ __('Bills')}}</h4>
    @can('create bills')
      @if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0))
        <a href="{{ route('bills.create')}}" title="{{ __('Create a bill')}}" class="btn btn-primary waves-effect waves-light">
          <span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Create a bill')}}
        </a>
      @endif
    @endcan
  </div><!-- d-flex -->

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if(session()->has('success'))
    <div class="alert alert-success d-flex align-items-center mb-6" role="alert">
      <span class="alert-icon rounded">
        <i class="icon-base ti ti-check icon-md"></i>
      </span>
      {{ session()->get('success') }}
    </div>
  @endif

  <div class="card mb-6">
    <div class="card-body p-3">
      <div class="row row-cols-2 row-cols-md-3 g-3">
        <div class="col">
          <input id="keyword" class="form-control" value="{{request()->get('keyword')}}" placeholder="{{__('Search')}}" >
        </div><!-- col -->
        <div class="col">
          <select name="statuses[]" id="paymentStatus" class="select2 form-select" multiple>
            <option value="pending" @if(in_array('pending', request()->get('statuses', [])) ) selected @endif>{{ __('Unpaid') }}</option>
            <option value="paid" @if(in_array('paid', request()->get('statuses', [])) ) selected @endif>{{ __('Paid') }}</option>
            <option value="expired" @if(in_array('expired', request()->get('statuses', [])) ) selected @endif>{{ __('Expired') }}</option>
            <option value="canceled" @if(in_array('canceled', request()->get('statuses', [])) ) selected @endif>{{ __('Canceled') }}</option>
            <option value="failed" @if(in_array('failed', request()->get('statuses', [])) ) selected @endif>{{ __('Failed') }}</option>
            <option value="cn_refunded" @if(in_array('cn_refunded', request()->get('statuses', [])) ) selected @endif>{{ __('Refunded') }}</option>
          </select>
        </div><!-- col -->
        <div class="col">
          <input type="text" name="dates" id="dateRangePicker" placeholder="Search by day" class="form-control" readonly="readonly" />
        </div><!-- col -->
      </div><!-- row -->
    </div><!-- card-body -->
  </div><!-- card -->

  @if((!auth()->user()->mainStoreUser && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0))
    <div class="d-flex align-items-center justify-content-end mb-3">
      <a href="{{ route('export.bills', request( )->input( ))}}" title="{{ __('Export bills')}}" class="excelBtn d-flex align-items-center justify-content-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 -1.27 110.037 110.037"><path d="M57.55 0h7.425v10c12.513 0 25.025.025 37.537-.038 2.113.087 4.438-.062 6.275 1.2 1.287 1.85 1.138 4.2 1.225 6.325-.062 21.7-.037 43.388-.024 65.075-.062 3.638.337 7.35-.425 10.938-.5 2.6-3.625 2.662-5.713 2.75-12.95.037-25.912-.025-38.875 0v11.25h-7.763c-19.05-3.463-38.138-6.662-57.212-10V10.013C19.188 6.675 38.375 3.388 57.55 0z" fill="#207245"/><path d="M64.975 13.75h41.25V92.5h-41.25V85h10v-8.75h-10v-5h10V62.5h-10v-5h10v-8.75h-10v-5h10V35h-10v-5h10v-8.75h-10v-7.5z" fill="#fff"/><path d="M79.975 21.25h17.5V30h-17.5v-8.75z" fill="#207245"/><path d="M37.025 32.962c2.825-.2 5.663-.375 8.5-.512a2607.344 2607.344 0 0 1-10.087 20.487c3.438 7 6.949 13.95 10.399 20.95a716.28 716.28 0 0 1-9.024-.575c-2.125-5.213-4.713-10.25-6.238-15.7-1.699 5.075-4.125 9.862-6.074 14.838-2.738-.038-5.476-.15-8.213-.263C19.5 65.9 22.6 59.562 25.912 53.312c-2.812-6.438-5.9-12.75-8.8-19.15 2.75-.163 5.5-.325 8.25-.475 1.862 4.888 3.899 9.712 5.438 14.725 1.649-5.312 4.112-10.312 6.225-15.45z" fill="#fff"/><path d="M79.975 35h17.5v8.75h-17.5V35zm0 13.75h17.5v8.75h-17.5v-8.75zm0 13.75h17.5v8.75h-17.5V62.5zm0 13.75h17.5V85h-17.5v-8.75z" fill="#207245"/></svg>
      </a>
    </div>
  @endif

  <div class="card">
    @if($bills->count())
      <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th class="fw-bold">{{__('Name') }}</th>
              <th class="fw-bold">{{__('Values') }}</th>
              <th class="fw-bold">{{__('Date created') }}</th>
              <th class="fw-bold">{{__('Status') }}</th>
              <th width="5%" class="fw-bold">{{__('Actions')}}</th>
            </tr>
          </thead>
          <tbody class="table-border-bottom-0">
            @foreach($bills as $bill)
              @include('bills.item')
            @endforeach
          </tbody>
        </table>
      </div><!-- table-responsive -->
      @if($bills->hasPages())
        <div class="d-flex align-items-center justify-content-center mt-3">
          {{ $bills->appends($_GET)->links() }}
        </div><!-- d-flex -->
      @endif
    @else
      <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column py-5">
        <i class="ti ti-receipt-2 ti-xl"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</span>
      </div><!-- no_bills_yet -->
    @endif
  </div><!-- card -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/libs/moment/moment.js') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/bootstrap-daterangepicker/bootstrap-daterangepicker.js') }}"></script>
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}"></script>
  <script type="text/javascript">
    // --------------------------------------------------------------------
    // Bootrap Daterange Picker
    // --------------------------------------------------------------------
    function oldParams(type) {
      var params = '';
      if (getUrlParameter('date_start') && type != 2) {
        params += '&date_start=' + getUrlParameter('date_start');
      }
      if (getUrlParameter('date_to') && type != 2) {
        params += '&date_to=' + getUrlParameter('date_to');
      }
      if (getParams()['statuses[]'] && type != 1) {
        getParams()['statuses[]'].forEach(element => params += '&statuses[]=' + element);
      }
      if (getUrlParameter('keyword') && type != 3) {
        params += '&keyword=' + getUrlParameter('keyword');
      }
      return params;
    }

    function getParams() {
      var url = window.location.href;
      var regex = /([^=&?]+)=([^&#]*)/g,
        params = {},
        parts,
        key,
        value;

      while ((parts = regex.exec(url)) != null) {
        key = parts[1], value = parts[2];
        var isArray = /\[\]$/.test(key);

        if (isArray) {
          params[key] = params[key] || [];
          params[key].push(value);
        } else {
          params[key] = value;
        }
      }
      return params;
    }

    $('label').on('change', function() {
      var names = [];
      $('input:checked').each(function() {
        names.push('statuses[]=' + this.value);
      });

      var dateParam = '?' + names.join('&') + oldParams(1);
      window.history.pushState('', '', dateParam);
      location.reload();
    });

    // Watch keyword
    var searchTimer = null,
      minLength = 3,
      searchDelay = 1000;
    $('#keyword').on("input", function() {
      clearTimeout(searchTimer);
      var searchVal = this.value;
      // Start new timer
      searchTimer = setTimeout(function() {
        var dateParam = '?' + 'keyword=' + searchVal + oldParams(3);
        window.history.pushState('', '', dateParam);
        location.reload();
      }, searchDelay);
    });

    // Focus in search
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
          format: 'DD/MM/YYYY',
          daysOfWeek: [
            '{{ __('Sun') }}',
            '{{ __('Mon') }}',
            '{{ __('Tue') }}',
            '{{ __('Wed') }}',
            '{{ __('Thu') }}',
            '{{ __('Fri') }}',
            '{{ __('Sat') }}'
          ],
          monthNames: [
            '{{ __('January') }}',
            '{{ __('February') }}',
            '{{ __('March') }}',
            '{{ __('April') }}',
            '{{ __('May') }}',
            '{{ __('June') }}',
            '{{ __('July') }}',
            '{{ __('August') }}',
            '{{ __('September') }}',
            '{{ __('October') }}',
            '{{ __('November') }}',
            '{{ __('December') }}'
          ],
          fromLabel: '{{ __('from') }}',
          toLabel: '{{ __('to') }}',
          applyLabel: '{{ __('apply') }}',
          cancelLabel: '{{ __('cancel') }}',
          customRangeLabel: '{{ __('custom Range') }}',
          weekLabel: '{{ __('week') }}',
        },
        ranges: {
          '{{ __('Today') }}': [moment(), moment()],
          '{{ __('Yesterday') }}': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
          '{{ __('Last 7 Days') }}': [moment().subtract(6, 'days'), moment()],
          '{{ __('Last 30 Days') }}': [moment().subtract(29, 'days'), moment()],
          '{{ __('This Month') }}': [moment().startOf('month'), moment().endOf('month')],
          '{{ __('Last Month') }}': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        },
        startDate: getUrlParameter('date_start') ? moment(getUrlParameter('date_start'), 'MM/DD/YYYY') : moment().startOf('month'),
        endDate: getUrlParameter('date_to') ? moment(getUrlParameter('date_to'), 'MM/DD/YYYY') : moment(),
      }, function(start, end) {
        var dateParam = '?date_start=' + start.format('MM/DD/YYYY') + '&date_to=' + end.format('MM/DD/YYYY') + oldParams(2);
        window.history.pushState('', '', dateParam);
        location.reload();
      });
    });

    // --------------------------------------------------------------------
    // Select2
    // --------------------------------------------------------------------
    $(document).ready(function() {
      $('#paymentStatus').select2({
        placeholder: '{{ __('Select Payment Status') }}',
        allowClear: true
      });

      // Monitor change on select2 dropdown
      $('#paymentStatus').on('change', function() {
        var selectedValues = $(this).val(); // Get selected values
        var names = selectedValues.map(function(status) {
          return 'statuses[]=' + encodeURIComponent(status);
        });

        // Combine with other parameters
        var dateParam = '?' + names.join('&') + oldParams(1);

        // Update URL and reload
        window.history.pushState('', '', dateParam);
        location.reload();
      });
    });
  </script>
@endpush
