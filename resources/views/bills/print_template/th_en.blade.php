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

<div class="billPrintThermal">
  <div class="aboutUser d-flex align-items-center justify-content-center flex-column">
    @if($bill->user->logo)
      <figure class="my-2">
        <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="mw-100">
      </figure><!-- figure -->
    @endif
    @if($bill->user->settings->add_tax_invoice)
      <div class="taxInvoiceText text-secondary">@if($bill->debit_note_bill_id == null) {{ __('Simplified Tax Invoice', [], $lang) }} @else {{ __('Tax debit note', [], $lang) }} @endif</div>
    @endif
    <span class="d-block fw-bold mt-3">{{ $bill->user->business_name }}</span>
    @if(isset($bill->user->settings->header_bill))
      <p class="d-block mb-0">{{ $bill->user->settings->header_bill }}</p>
    @endif
    <p class="d-block mb-0">{{  $bill->user->business_address }}</p>
    <b class="d-block fw-normal" dir="ltr">{{  $bill->user->business_mobile }}</b>
  </div><!-- aboutUser -->
  <div id="status" class="my-3">
    @include('bills.print_template.partials.status',['bill' => $bill, 'lang' => $lang])
  </div><!-- status -->
  <div class="billInfo pt-2 mt-2 borderTop">
    @if($bill->debit_note_bill_id == null)
      @include('bills.print_template.partials.bill_info',['bill' => $bill, 'lang' => $lang])
    @else
      @include('bills.print_template.partials.debit_note_info',['bill' => $bill, 'lang' => $lang])
    @endif
  </div><!-- billInfo -->
  <div class="tableItems pt-2 borderTop">
    <table class="w-100">
      <thead>
        <tr>
          <th class="p-1 text-start">{{ __('Description', [], $lang) }}</th>
          <th class="p-1 text-center">{{ __('Price', [], $lang) }}</th>
          <th class="p-1 text-center">{{ __('Quantity', [], $lang) }}</th>
          @if($bill->add_tax)
            <th th width="35%" class="p-1 text-end">{{ __('Total include added tax', [], $lang) }}</th>
          @else
            <th width="35%" class="p-1 text-end">{{ __('Total', [], $lang) }}</th>
          @endif
        </tr>
      </thead>
      <tbody>
        @foreach($bill->items as $item)
        <tr>
          <td class="p-1 text-start">{!! $item->product_name !!}</td>
          <td class="p-1 text-center">{{ $item->product_price  }}</td>
          <td class="p-1 text-center">{{ $item->quantity  }}</td>
          @if( $bill->add_tax)
            <td class="p-1 text-end">{{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }}</td>
          @else
            <td class="p-1 text-end">{{ $item->product_price * $item->quantity }}</td>
          @endif
        </tr>
        @endforeach
      </tbody>
    </table>
  </div><!-- tableItems -->
  <div class="billInfo pt-2 mt-2 borderTop">
    @if( $bill->add_tax || $bill->add_discount)
      <div class="d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-start justify-content-between flex-column mb-2">
          <span class="d-block">{{ __('Total amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
          @if( $bill->add_tax)
            <small class="d-block text-muted mt-1">( {{ __('Exclude added tax', [], $lang) }} )</small>
          @endif
        </div>
        <span class="d-block mb-2">{{ $bill->sub_total }}</span>
      </div><!-- d-flex -->
    @endif
    @if( $bill->add_discount)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Discount amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
        <span class="d-block mb-2">{{ $bill->discount }}</span>
      </div><!-- d-flex -->
    @endif
    @if( $bill->user->pay_fees == 'client')
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('payment fees', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
        <span class="d-block mb-2">{{ $bill->payment_fees }}</span>
      </div><!-- d-flex -->
    @endif
    @if( $bill->add_tax)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value], $lang) }} ({{ __('SAR', [], $lang) }})</span>
        <span class="d-block mb-2">{{ $bill->vat }}</span>
      </div><!-- d-flex -->
    @endif
    @if( $bill->channel_extra_amount)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{$bill->channel_extra_title}}({{ __('SAR', [], $lang) }})</span>
        <span class="d-block mb-2">{{ $bill->channel_extra_amount }}</span>
      </div><!-- d-flex -->
    @endif
    @if( $bill->channel_extra_vat)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Vat', [], $lang) }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</span>
        <span class="d-block mb-2">{{ $bill->channel_extra_vat }} {{ __('SAR', [], $lang) }}</span>
      </div><!-- d-flex -->
    @endif
    {{-- @if( $bill->refund_amount)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Refund Amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
        <span class="d-block mb-2">{{ $bill->refund_amount }}</span>
      </div><!-- d-flex -->
    @endif --}}
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Total amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
      <span class="d-block mb-2">{{ $bill->sub_total + $bill->vat - $bill->discount}}</span>
    </div><!-- d-flex -->
  </div><!-- bill_info -->
  @if($bill->customer_notes)
    <div class="customer_notes pt-2 mt-2 borderTop">{{$bill->customer_notes}}</div>
  @endif
  @if($bill->user->settings->add_tax_invoice)
    <div class="qrCode mt-2 pt-2 borderTop">
      <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
        {!! generateQRcode($bill) !!}
        <span class="d-block text-body">{{ __('Tax Invoice', [], $lang) }}</span>
      </a>
    </div><!-- qrCode -->
  @endif
  @if(isset($bill->user->settings->footer_bill))
    <p class="d-block mb-0 mt-2 text-center">{{ $bill->user->settings->footer_bill }}</p>
  @endif
</div><!-- billPrintThermal -->

<script>
  window.onload = function() {
    window.print();
  }
</script>