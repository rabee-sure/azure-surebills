@extends('layouts.print')

<div class="billPrintThermal">
  <div class="aboutUser d-flex align-items-center justify-content-center flex-column">
    @if($bill->user->logo)
      <figure class="my-2">
        <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="mw-100">
      </figure><!-- figure -->
    @endif
    @if($bill->user->settings->add_tax_invoice)
      <div class="taxInvoiceText text-secondary">{{ __('Simplified Tax Invoice', [], $lang) }}</div>
    @endif
    <span class="d-block fw-bold mt-3">{{ $bill->user->business_name }}</span>
    @if(isset($bill->user->settings->header_bill))
      <p class="d-block mb-0">{{ $bill->user->settings->header_bill }}</p>
    @endif
    <p class="d-block mb-0">{{  $bill->user->business_address }}</p>
    <b class="d-block fw-normal" dir="ltr">{{  $bill->user->business_mobile }}</b>
  </div><!-- aboutUser -->
  <div id="status" class="my-3">
    @if($bill->status == 'expired')
      <div class="alertMsg text-center fw-bold expired"> {{ __('this bill has been expired', ['number' => $bill->number ], $lang) }}</div>
    @elseif($bill->status == 'paid')
      <div class="alertMsg text-center fw-bold paid">
        @if ($bill->depositTransaction)
          {{ __('Paid', [], $lang) }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
        @else
          {{ __('this bill has been successfully', ['number' => $bill->number ], $lang) }}
        @endif
      </div>
    @elseif($bill->status == 'paid_cash')
      <div class="alertMsg text-center fw-bold paid"> {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ], $lang) }}</div>
    @elseif($bill->status == 'paid_bank_transfer')
      <div class="alertMsg text-center fw-bold paid"> {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ], $lang) }}</div>
    @elseif($bill->status == 'canceled')
      <div class="alertMsg text-center fw-bold canceled"> {{ __('this bill has been canceled', ['number' => $bill->number ], $lang) }}</div>
    @elseif($bill->status == 'failed')
      <div class="alertMsg text-center fw-bold canceled"> {{ __('this bill has been failed', ['number' => $bill->number ], $lang) }}</div>
    @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
      <div class="alertMsg text-center fw-bold refunded"> {{ __('this bill has been refunded', ['number' => $bill->number ], $lang) }}</div>
    @endif
  </div><!-- status -->
  <div class="billInfo pt-2 mt-2 borderTop">
    @if($bill->user->settings->add_tax_invoice)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Bill No.', [], $lang) }}</span>
        <span class="d-block mb-2">{{ $bill->number }}</span>
      </div><!-- d-flex -->
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Date', [], $lang) }}</span>
        <span class="d-block mb-2">{{ $bill->created_at->format('d/m/Y')}}</span>
      </div><!-- d-flex -->
      @if($bill->user->vat_registration_number)
        <div class="d-flex align-items-center justify-content-between">
          <span class="d-block mb-2">{{ __('Organization VAT Registration Number', [], $lang) }}</span>
          <span class="d-block mb-2">{{ $bill->user->vat_registration_number }}</span>
        </div><!-- d-flex -->
      @endif
    @else
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('No.', [], $lang) }}</span>
        <span class="d-block mb-2">{{ $bill->number }}</span>
      </div><!-- d-flex -->
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Date', [], $lang) }}</span>
        <span class="d-block mb-2">{{ $bill->created_at->format('d/m/Y')}}</span>
      </div><!-- d-flex -->
    @endif
    @if($bill->user->settings->display_customer_details)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Customer Name', [], $lang) }}</span>
        <span class="d-block mb-2">{{ $bill->customer->name }}</span>
      </div><!-- d-flex -->
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Mobile Number', [], $lang) }}</span>
        <span class="d-block mb-2">{{ $bill->customer->mobile }}</span>
      </div><!-- d-flex -->
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
    @if( $bill->refund_amount)
      <div class="d-flex align-items-center justify-content-between">
        <span class="d-block mb-2">{{ __('Refund Amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
        <span class="d-block mb-2">{{ $bill->refund_amount }}</span>
      </div><!-- d-flex -->
    @endif
    <div class="d-flex align-items-center justify-content-between">
      <span class="d-block mb-2">{{ __('Total amount', [], $lang) }}({{ __('SAR', [], $lang) }})</span>
      <span class="d-block mb-2">{{ $bill->total}}</span>
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