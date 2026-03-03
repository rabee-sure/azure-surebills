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
      $refund_amount = $log->refunded_amount;
  }

  // total amount
  if (isset($log->results['bill']['total'])) {
      $total_amount = $log->results['bill']['total'];
  } else if (isset($log->results['transaction']['amount'])) {
      $total_amount = $log->results['transaction']['amount'];
  } else if(isset($log->results['orderInformation']['amountDetails']['totalAmount'])) {
      $total_amount = $log->results['orderInformation']['amountDetails']['totalAmount'];
  } else {
      $total_amount = $log->bill->total;
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

<h4 class="mb-1">{{ __('Bill') }}</h4>

<nav aria-label="breadcrumb">
  <ol class="breadcrumb breadcrumb-custom-icon mb-6">
    <li class="breadcrumb-item">
      <a href="{{ url('bills') }}" title="{{ __('Bills') }}">{{ __('Bills')}}</a>
      <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
    </li>
    <li class="breadcrumb-item">
      <a href="/bills/{{ $bill->id }}" title="{{__('Bill No.')}} {{ $bill->number }}">{{__('Bill No.')}} {{ $bill->number }}</a>
      <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
    </li>
    <li class="breadcrumb-item active">{{ $log->id }}</li>
  </ol>
</nav>

<div class="row justify-content-center">
  <div class="col-12 col-md-8 col-lg-5 d-flex flex-column gap-5">

    <div class="card">
      <div class="card-body d-flex alig-items-center justify-content-center flex-column gap-2">

        <div class="imgthumb d-flex align-items-center justify-content-center flex-shrink-0">
          @if ($brand == 'VISA')
            <img alt="visa" src="{{ asset('assets/v2/img/payments/visa_lg.png') }}">
          @elseif($brand == 'MASTERCARD' || $brand == 'MASTER')
            <img alt="mastercard" src="{{ asset('assets/v2/img/payments/mastercard_lg.png') }}">
          @elseif ($brand == 'MADA')
            <img alt="mada" src="{{ asset('assets/v2/img/payments/mada_lg.png') }}">
          @elseif ($brand == 'APPLEPAY')
            <img alt="applepay" src="{{ asset('assets/v2/img/payments/applepay_lg.png') }}">
          @else
            <img alt="card non" src="{{ asset('assets/v2/img/payments/cardnon.png') }}">
          @endif
        </div><!-- imgthumb -->

        @if($log->payment_method == 'mastercard_refund')
          <p class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-center' : 'justify-content-center'}} gap-1 m-0 fw-medium text-heading">
            {{ $refund_amount }} <i class="sar-icon"></i>
          </p>
        @else
          <p class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-center' : 'justify-content-center'}} gap-1 m-0 fw-medium text-heading">
            {{ $total_amount }} <i class="sar-icon"></i>
          </p>
        @endif
        @if($log->payment_method == 'mastercard_refund')
          <span class="badge bg-label-warning mx-auto">{{ __('Refund') }}</span>
        @elseif($log->status == true)
          <span class="badge bg-label-success mx-auto">{{ __('Paid') }}</span>
        @else
          <span class="badge bg-label-danger mx-auto">{{ __('Failed') }}</span>
        @endif
        <p class="text-center text-muted m-0">{{__('ID') }} : {{ $log->id }}</p>
      </div><!-- card-body -->
    </div><!-- card -->

    <div class="card">
      <div class="card-body">
        <h5 class="card-title mb-5 d-flex align-items-center justify-content-start gap-2"><i class="icon-base ti ti-credit-card"></i> {{__('Operation Info') }}</h5>
        <div class="table-responsive w-100">
          <table class="table table-striped table-bordered">
            <tbody>
              <tr>
                <td>{{__('Amount') }}</td>
                <td>
                  @if($log->payment_method == 'mastercard_refund')
                    <p class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 fw-medium">
                      {{ $refund_amount }} <i class="sar-icon"></i>
                    </p>
                  @else
                    <p class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 fw-medium">
                      {{ $total_amount }} <i class="sar-icon"></i>
                    </p>
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
      </div><!-- card-body -->
    </div><!-- card -->

  </div><!-- col -->
</div><!-- row -->

@endsection
