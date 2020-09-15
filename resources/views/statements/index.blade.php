@extends('layouts.app')

@section('title', __('Statement'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="mb-2">
        <h1>{{ __('Statement') }}</h1>
        <div class="top-right-button-container">
         <h3>{{ __('Balance') }} : {{ round(auth()->user()->balance, 2) }} {{ __('SAR')}}</h3>
        </div>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/')}}">{{ __('Home') }}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Statement')}}</li>
          </ol>
        </nav>

        <div class="collapse dont-collapse-sm" id="displayOptions">
          <div class="d-block d-md-inline-block">
            <div class="search-sm calendar-sm d-inline-block float-md-left mr-1 mb-1 align-top">
              <input class="form-control" name="dates" placeholder="Search by day">
            </div>
          </div>  
        </div>
      </div>
       
      <div class="separator mb-5"></div>
    </div>

  </div>
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
                <th>{{ __('Auth ID') }}</th>
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
                  <td>{{ $transaction->auth_id }}</td>
                  <td>
                    @if ($transaction->card_brand == 'VISA')
                      <img alt="mastercard" src="img/cards/visa.gif" class="mr-1" width="18px"> 
                    @elseif ($transaction->card_brand == 'MASTER')
                      <img alt="mastercard" src="img/cards/mastercard.gif" class="mr-1" width="18px"> 
                    @elseif ($transaction->card_brand == 'MADA')
                      <img alt="mastercard" src="img/cards/mada.gif" class="mr-1" width="18px"> 
                    @elseif ($transaction->card_brand == 'APPLEPAY')
                      <img alt="mastercard" src="img/cards/applepay.gif" class="mr-1" width="18px"> 
                    @endif
                    {{ $transaction->card }}
                  </td>
                  <td class="text-danger">{{ $transaction->type == 'debit' ? round($transaction->amount, 2) : '-' }}</td>
                  <td class="text-success">{{ $transaction->type == 'credit' ? round($transaction->amount, 2) : '-' }}</td>
                  <td>{{ round($transaction->balance, 2) }}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
      </div>
    </div>
  @else
    <div class="no_bills_yet">
      <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 480 480" width="512" xmlns:v="https://vecta.io/nano"><path d="M215 164c0 57.897 47.103 105 105 105s105-47.103 105-105S377.897 59 320 59s-105 47.103-105 105zm194 0c0 49.075-39.925 89-89 89s-89-39.925-89-89 39.925-89 89-89 89 39.925 89 89zm-89-54a8 8 0 0 1 8 8v3.376c9.31 3.303 16 12.195 16 22.624a8 8 0 1 1-16 0 8.01 8.01 0 0 0-8-8 8.01 8.01 0 0 0-8 8v3.237c0 3.518 2.256 6.586 5.614 7.636l9.544 2.982C337.232 161.004 344 170.2 344 180.763V184c0 11.52-8.16 21.166-19 23.473V210a8 8 0 1 1-16 0v-4.68c-7.714-3.996-13-12.05-13-21.32a8 8 0 1 1 16 0 8.01 8.01 0 0 0 8 8 8.01 8.01 0 0 0 8-8v-3.237c0-3.518-2.256-6.586-5.614-7.636l-9.544-2.982C302.768 166.996 296 157.8 296 147.237V144c0-10.43 6.69-19.32 16-22.624V118a8 8 0 0 1 8-8zm130 212v102c0 30.88-25.122 56-56 56H86c-30.878 0-56-25.12-56-56V32C30 14.355 44.355 0 62 0h260c17.645 0 32 14.355 32 32a8 8 0 1 1-16 0c0-8.822-7.178-16-16-16H62c-8.822 0-16 7.178-16 16v392c0 22.056 17.944 40 40 40h268.862C344.467 453.828 338 439.658 338 424V299a8 8 0 1 1 16 0v125c0 22.056 17.944 40 40 40s40-17.944 40-40V322c0-8.822-7.178-16-16-16h-34a8 8 0 1 1 0-16h34c17.645 0 32 14.355 32 32zM100 215a8 8 0 0 1 8-8h68a8 8 0 1 1 0 16h-68a8 8 0 0 1-8-8zm0-90a8 8 0 0 1 8-8h68a8 8 0 1 1 0 16h-68a8 8 0 0 1-8-8zm184 180a8 8 0 0 1-8 8H108a8 8 0 1 1 0-16h168a8 8 0 0 1 8 8zm0 84a8 8 0 0 1-8 8H108a8 8 0 1 1 0-16h168a8 8 0 0 1 8 8z" fill="#999"/></svg>
      <span>{{ __('No Bill Matched The Given Criteria.') }}</span>
    </div><!-- no_bills_yet -->
  @endif
@endsection

@push('footer-scripts')
  <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
  <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
  <script type="text/javascript">
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
        $('input[name="dates"]').daterangepicker({
          opens: 'left',
          startDate: getUrlParameter('date_start')?getUrlParameter('date_start'): moment().startOf('month').format("MM/DD/YYYY"), 
          endDate: getUrlParameter('date_to')?getUrlParameter('date_to'):moment(new Date()).format("MM/DD/YYYY"),
        }, function(start, end, label) {
            var dateParam = '?date_start=' + start.format('MM/DD/YYYY') + '&date_to='+end.format('MM/DD/YYYY');
            window.history.pushState('', '', dateParam);
            location.reload();
        });
      });
  </script>
@endpush
