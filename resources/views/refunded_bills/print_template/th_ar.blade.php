@extends('layouts.print')

<style>
  @media print {
    @page {
      size: 80mm 280mm;
      margin: 0;
      padding: 0;
    }
  }
</style>

<div class="billPrint">
  <div class="aboutUser d-flex align-items-center justify-content-center flex-column">
    @if($refundedBill->bill->user->logo)
      <figure class="my-2">
        <img src="{{ $refundedBill->bill->user->logo_url }}" alt="{{ $refundedBill->bill->user->business_name }}" class="mw-100">
      </figure><!-- figure -->
    @endif
    @if($refundedBill->bill->user->settings->add_tax_invoice)
      <div class="taxInvoiceText text-secondary">{{ __('Simplified Tax Invoice', [], $lang) }}</div>
    @endif
    <span class="d-block fw-bold mt-3">{{ $refundedBill->bill->user->business_name }}</span>
    <p class="d-block mb-0">{{  $refundedBill->bill->user->business_address }}</p>
    <b class="d-block fw-normal" dir="ltr">{{  $refundedBill->bill->user->business_mobile }}</b>
  </div><!-- aboutUser -->

  <div id="status" class="my-3">
    @if($refundedBill->method == 'online')
      <div class="alertMsg text-center fw-bold refunded"> {{ __('Refunded', [], $lang) }}</div>
    @elseif($refundedBill->method == 'cash')
      <div class="alertMsg text-center fw-bold refunded"> {{ __('Refunded Cash', [], $lang) }}</div>
    @elseif($refundedBill->method == 'bank_transfer')
      <div class="alertMsg text-center fw-bold refunded"> {{ __('Refunded Bank Transfer', [], $lang) }}</div>
    @endif
  </div><!-- status -->

  <div class="billInfo p-2 mt-2 d-flex flex-column gap-2 borderTop">
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block">{{ __('Credit Note Date', [], $lang) }}</span>
      <span class="d-block">{{ $refundedBill->created_at->format('d/m/Y')}}</span>
    </div><!-- d-flex -->
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block">{{ __('Credit Note No.', [], $lang) }}</span>
      <span class="d-block">CN{{ $refundedBill->number }}</span>
    </div><!-- d-flex -->
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block">{{ __('Invoice Date', [], $lang) }}</span>
      <span class="d-block">{{ $refundedBill->bill->created_at->format('d/m/Y')}}</span>
    </div><!-- d-flex -->
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block">{{ __('Invoice Number', [], $lang) }}</span>
      <span class="d-block">{{ $refundedBill->bill->number }}</span>
    </div><!-- d-flex -->
    @if($refundedBill->bill->user->settings->display_customer_details && $refundedBill->bill->customer_mobile != 555555555)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block">{{ __('Customer Name', [], $lang) }}</span>
        <span class="d-block">{{ $refundedBill->bill->customer_name }}</span>
      </div><!-- d-flex -->
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block">{{ __('Mobile Number', [], $lang) }}</span>
        <span class="d-block">{{ $refundedBill->bill->customer_mobile }}</span>
      </div><!-- d-flex -->
    @endif
  </div><!-- billInfo -->

  <div class="billInfo p-2 mt-2 borderTop">
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block fw-bold">{{ __('Refund Amount', [], $lang) }}</span>
      <span class="d-block">
        <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
            {{ $refundedBill->amount }}  <span class="riyal-symbol-font">$</span>
        </div><!-- d-flex -->
      </span>
    </div><!-- d-flex -->
  </div><!-- bill_info -->


  @if($refundedBill->bill->user->settings->add_tax_invoice)
    <div class="qrCode mt-2 p-2 borderTop">
      <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $refundedBill->bill->pay_id])}}">
        {!! generateQRcode($refundedBill->bill) !!}
        <span class="d-block text-body">{{ __('Tax Invoice', [], $lang) }}</span>
      </a>
    </div><!-- qrCode -->
  @endif
</div><!-- showBill -->

<script>
  window.onload = function() {
    window.print();
  }
</script>
