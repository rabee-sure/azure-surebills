 @extends('layouts.app')

@section('title', __('Bills'))

@section('content')


@php
  // brand
  if (isset($log->results['response']) && isset($log->results['response']['paymentBrand'])) {
      $brand = $log->results['response']['paymentBrand'];
  } else {
      $brand = $log->brand;
  }

  // refund amount
  if (isset($log->results['transaction']['amount'])) {
      $refund_amount = $log->results['transaction']['amount'];
  } else {
      $refund_amount = $log->refund_amount;
  }

  // total amount
  if (isset($log->results['bill']['total'])) {
      $total_amount = $log->results['bill']['total'];
  } else if (isset($log->results['transaction']['amount'])) {
      $total_amount = $log->results['transaction']['amount'];
  } else {
      $total_amount = '---';
  }

  // last 4 digits
  if (isset($log->results['response']['card'])) {
    $last4digits = "xxxx-xxxx-xxxx-" . $log->results['response']['card']['last4Digits'];
  } else {
    $last4digits = $log->card_number;
  }

  // bank message
  if (isset($log->results['description'])) {
    $bank_message = $log->results['description'];
  } else {
    $bank_message = $log->bank_message;
  }
@endphp

  <div class="row">
    <div class="col-12">
      <h1>{{ __('Bills') }}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item"><a href="/bills" title="{{__('Bills')}}">{{__('Bills')}}</a></li>
          <li class="breadcrumb-item"><a href="/bills/{{ $bill->id }}" title="{{__('Bills')}}">{{__('Bill No.')}} #{{ $bill->number }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            {{ $log->id }}
          </li>
        </ol>
      </nav>
      <div class="separator mb-5"></div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card mb-5">
        <div class="card-body">
          <div class="payment_block">
            <div class="title">
                  @if($brand == 'MADA')
                    <img src="{{ asset('/images/payments/mada.png') }}" alt="mada">
                  @elseif($brand == 'VISA')
                    <img src="{{ asset('/images/payments/visa.png') }}" alt="visa">
                  @elseif($brand == 'MASTERCARD')
                    <img src="{{ asset('/images/payments/card.png') }}" alt="mastercard">
                  @elseif($brand == 'APPLEPAY')
                    <img src="{{ asset('/images/payments/pay.png') }}" alt="apple pay">
                  @endif
              <p>           
                  @if($log->payment_method == 'mastercard_refund')
                    {{ $refund_amount }} {{__('SAR') }}
                  @else
                    {{ $total_amount }} {{__('SAR') }}
                  @endif
              </p>
              @if($log->payment_method == 'mastercard_refund')
                <td><span class="badge badge-pill badge-warning bill_status_badge">{{ __('Refund') }}</span></td>
              @elseif($log->status == true)
                <span class="badge badge-pill badge-success ">{{ __('Paid') }}</span>
              @else
                <span class="badge badge-pill badge-danger ">{{ __('Failed') }}</span>
              @endif
            </div><!-- title -->
            <div class="desc">{{__('ID') }} : {{ $log->id }}</div>
            <div class="separator mb-5"></div>
            <div class="table_block">
              <div class="name"><div class="glyph-icon simple-icon-credit-card"></div> {{__('Operation Info') }}</div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <tbody>
                    <tr>
                      <td>{{__('Amount') }}</td>
                      <td>
                        @if($log->payment_method == 'mastercard_refund')
                          {{ $refund_amount }} {{__('SAR') }}
                        @else
                          {{ $total_amount }} {{__('SAR') }}
                        @endif
                      </td>
                    </tr>
                    <tr>
                      <td>{{__('Card Type') }}</td>
                      <td>{{ $brand }}</td>
                    </tr>
                    <tr>
                      <td>{{__('Card Number') }}</td>
                      <td>{{ $last4digits }}</td>
                    </tr>
                    <tr>
                      <td>{{ __('Bank Transaction ID') }}</td>
                      <td>{{ $log->bank_transaction_id }}</td>
                    </tr>
                    <tr>
                      <td>{{ __('Bank Message') }}</td>
                      <td>{{ $bank_message }}</td>
                    </tr>
                    <tr>
                      <td>{{__('Date created') }}</td>
                      <td>{{ $log->created_at}}</td>
                    </tr>
                    <tr>
                      <td>{{__('Last Update') }}</td>
                      <td>{{ $log->updated_at}}</td>
                    </tr>
                  </tbody>
                </table>
              </div><!-- table-responsive -->
            </div><!-- table_block -->
          </div><!-- payment_block -->
        </div>
      </div>
    </div>
  </div>
@endsection
