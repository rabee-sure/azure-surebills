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
<div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm d-print-none">
  <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
  <i>/</i>
  <a href="/bills" title="{{ __('Bills') }}">{{ __('Bills') }}</a>
  <i>/</i>
  <a href="/bills/{{ $bill->id }}" title="{{__('Bill No.')}} {{ $bill->number }}">{{__('Bill No.')}} {{ $bill->number }}</a>
  <i>/</i>
  <span>{{ $log->id }}</span>
</div><!-- breadcrump -->

<section id="billLogPage">
  <div class="title mb-4 d-print-none">
    <h1 class="d-block fw-bold m-0 fs-5">{{ __('Bill') }}</h1>
  </div><!-- title -->
  <div class="row justify-content-center">
    <div class="col-12 col-lg-8">
      <div class="blockArea p-3 bg-white rounded-3 mb-3 shadow-sm d-flex align-items-center justify-content-center flex-column">
        <figure class="mb-3 rounded-3 d-flex align-items-center justify-content-center text-center border">
          @if($brand == 'MADA')
            <img src="{{ asset('/images/payments/mada.png') }}" alt="mada">
          @elseif($brand == 'VISA')
            <img src="{{ asset('/images/payments/visa.png') }}" alt="visa">
          @elseif($brand == 'MASTERCARD')
            <img src="{{ asset('/images/payments/card.png') }}" alt="mastercard">
          @elseif($brand == 'APPLEPAY')
            <img src="{{ asset('/images/payments/pay.png') }}" alt="apple pay">
          @endif
        </figure>
        @if($log->payment_method == 'mastercard_refund')
          <div class="refundAmount d-block fw-bold mb-3">
            <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
              {{ $refund_amount }} <span class="riyal-symbol-font">$</span>
            </div><!-- d-flex -->
          </div>
        @else
          <div class="refundAmount d-block fw-bold mb-3">
            <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
              {{ $total_amount }} <span class="riyal-symbol-font">$</span>
            </div><!-- d-flex -->
          </div>
        @endif
        @if($log->payment_method == 'mastercard_refund')
          <span class="billStatusBadge badge badge-pill badge-warning d-flex align-items-center justify-content-center fw-bold px-2 mb-3">{{ __('Refund') }}</span>
        @elseif($log->status == true)
          <span class="billStatusBadge badge badge-pill badge-success d-flex align-items-center justify-content-center fw-bold px-2 mb-3">{{ __('Paid') }}</span>
        @else
          <span class="billStatusBadge badge badge-pill badge-danger d-flex align-items-center justify-content-center fw-bold px-2 mb-3">{{ __('Failed') }}</span>
        @endif
        <div class="logId text-center mb-5">{{__('ID') }} : {{ $log->id }}</div>
        <div class="name mb-2 d-flex align-items-center justify-content-start w-100"><i class="fal fa-credit-card"></i>{{__('Operation Info') }}</div>
        <div class="table-responsive w-100">
          <table class="table table-striped table-bordered">
            <tbody>
              <tr>
                <td>{{__('Amount') }}</td>
                <td>
                  @if($log->payment_method == 'mastercard_refund')
                    <div class="d-flex align-items-center gap-1 fw-bold rtl flex-shrink-0 @if(app()->getLocale() == 'ar') justify-content-start @else justify-content-end @endif">
                      {{ $refund_amount }} <span class="riyal-symbol-font">$</span>
                    </div><!-- d-flex -->
                  @else
                    <div class="d-flex align-items-center gap-1 fw-bold rtl flex-shrink-0 @if(app()->getLocale() == 'ar') justify-content-start @else justify-content-end @endif">
                      {{ $total_amount }} <span class="riyal-symbol-font">$</span>
                    </div><!-- d-flex -->
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
      </div><!-- blockArea -->
    </div><!-- col-12 -->
  </div><!-- row -->
</section><!-- billLogPage -->
@endsection