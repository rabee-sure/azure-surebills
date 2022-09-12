@extends('layouts.print')

<style>
  @media print {
    @page {
      size: A4 portrait;
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
  
  <div class="billInfo pt-2 mt-2 borderTop">
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Credit Note Date', [], $lang) }}</span>
      <span class="d-block mb-2">{{ $refundedBill->created_at->format('d/m/Y')}}</span>
    </div><!-- d-flex -->
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Credit Note No.', [], $lang) }}</span>
      <span class="d-block mb-2">{{ $refundedBill->number }}</span>
    </div><!-- d-flex -->
    
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Invoice Date', [], $lang) }}</span>
      <span class="d-block mb-2">{{ $refundedBill->bill->created_at->format('d/m/Y')}}</span>
    </div><!-- d-flex -->
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Invoice Number', [], $lang) }}</span>
      <span class="d-block mb-2">{{ $refundedBill->bill->number }}</span>
    </div><!-- d-flex -->

  </div><!-- billInfo -->
  
  <div class="billInfo pt-2 mt-2 borderTop">
    
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Refund Amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
      <span class="d-block mb-2">{{ $refundedBill->amount }}</span>
    </div><!-- d-flex -->
    
  </div><!-- bill_info -->
 
  @if($refundedBill->bill->user->settings->add_tax_invoice)
    <div class="qrCode mt-2 pt-2 borderTop">
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