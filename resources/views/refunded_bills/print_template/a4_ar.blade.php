@extends('layouts.print')

@push('css_styles')
  <style>
    @media print {
      body {
        margin: 0; /* This removes default body margins */
        padding: 0; /* This removes padding */
        -webkit-print-color-adjust: exact;
      }
      header, footer {
        display: none; /* Hide headers and footers */
      }
    }
    @page {
      size: A4 portrait;
      margin: 0; /* This is for page margins */
      -webkit-print-color-adjust: exact;
    }
  </style>
@endpush

@section('content')
<div class="d-flex flex-column gap-2 min-vh-100">

  <div class="bill-header p-4 flex-shrink-0">
    @if($refundedBill->bill->user->logo)
      <span class="app-brand-logo d-flex align-items-center justify-content-center mb-4">
        <img src="{{ $refundedBill->bill->user->logo_url }}" alt="{{ $refundedBill->bill->user->business_name }}" class="w-auto" height="32px">
      </span>
    @endif
    <div class="text-heading mb-xl-0 mb-5 d-flex flex-column gap-2 text-center">
      @if($refundedBill->bill->user->settings->add_tax_invoice)
        <p class="m-0">{{ __('Simplified Tax Invoice', [], $lang) }}</p>
      @endif
      <p class="m-0">{{ $refundedBill->bill->user->business_name }}</p>
      <!-- comment business address and make it read from business_address_details until SAMA Demo -->
      <!-- <p class="m-0">{{  $refundedBill->bill->user->business_address }}</p> -->
      <p class="m-0">{{  $refundedBill->bill->user->business_address_details }}</p>
      <p class="m-0">{{  $refundedBill->bill->user->business_mobile }}</p>
    </div>
    <div id="status">
      @if($refundedBill->method == 'online')
        <div class="alertMsg text-center fw-bold refunded"> {{ __('Refunded', [], $lang) }}</div>
      @elseif($refundedBill->method == 'cash')
        <div class="alertMsg text-center fw-bold refunded"> {{ __('Refunded Cash', [], $lang) }}</div>
      @elseif($refundedBill->method == 'bank_transfer')
        <div class="alertMsg text-center fw-bold refunded"> {{ __('Refunded Bank Transfer', [], $lang) }}</div>
      @endif
    </div><!-- status -->
  </div><!-- bill-header -->

  <div class="flex-grow-1">

    <div class="p-4 d-flex flex-column gap-2">

      <div class="d-flex align-items-center justify-content-between gap-2">
        <p class="d-block mb-0">{{ __('Credit Note Date', [], $lang) }}</p>
        <p class="d-block mb-0">{{ $refundedBill->created_at->format('d/m/Y')}}</p>
      </div><!-- d-flex -->

      <div class="d-flex align-items-center justify-content-between gap-2">
        <p class="d-block mb-0">{{ __('Credit Note No.', [], $lang) }}</p>
        <p class="d-block mb-0">CN{{ $refundedBill->number }}</p>
      </div><!-- d-flex -->

      <div class="d-flex align-items-center justify-content-between gap-2">
        <p class="d-block mb-0">{{ __('Invoice Date', [], $lang) }}</p>
        <p class="d-block mb-0">{{ $refundedBill->bill->created_at->format('d/m/Y')}}</p>
      </div><!-- d-flex -->

      <div class="d-flex align-items-center justify-content-between gap-2">
        <p class="d-block mb-0">{{ __('Invoice Number', [], $lang) }}</p>
        <p class="d-block mb-0">{{ $refundedBill->bill->number }}</p>
      </div><!-- d-flex -->

      @if($refundedBill->bill->user->settings->display_customer_details && $refundedBill->bill->customer_mobile != 555555555)
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="d-block mb-0">{{ __('Customer Name', [], $lang) }}</p>
          <p class="d-block mb-0">{{ $refundedBill->bill->customer_name }}</p>
        </div><!-- d-flex -->
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="d-block mb-0">{{ __('Mobile Number', [], $lang) }}</p>
          <p class="d-block mb-0">{{ $refundedBill->bill->customer_mobile }}</p>
        </div><!-- d-flex -->
      @endif

    </div><!-- p-4 -->

    <div class="d-flex flex-column gap-3 p-4">
      <div class="d-flex align-items-center justify-content-between gap-2">
        <p class="mb-0">{{ __('Refund Amount', [], $lang) }}</p>
        <p class="mb-0 d-flex align-items-center {{$lang == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
        {{ $refundedBill->amount }} <i class="sar-icon"></i>
        </p>
      </div>
    </div>

  </div><!-- flex-grow-1 -->

  @if($refundedBill->bill->user->settings->add_tax_invoice)
    <div class="flex-shrink-0">
      <hr class="my-0">
      <div class="card-body p-4 text-heading text-center text-capitalize">
        <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $refundedBill->bill->pay_id])}}">
          {!! generateQRcode($refundedBill->bill) !!}
          <span class="d-block text-body">{{ __('Tax Invoice', [], $lang) }}</span>
        </a>
      </div>
    </div>
  @endif

</div><!-- showBill -->

<script>
  window.onload = function() {
    window.print();
  }
</script>
@endsection
