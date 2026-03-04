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
      size: 80mm 280mm;
      margin: 0;
      padding: 0;
      -webkit-print-color-adjust: exact;
    }
  </style>
@endpush

<div class="d-flex flex-column gap-2 min-vh-100">

  <div class="bill-header p-2 flex-shrink-0">
    @if($bill->user->logo)
      <span class="app-brand-logo d-flex align-items-center justify-content-center mb-4">
        <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="w-auto" height="32px">
      </span>
    @endif
    <div class="text-heading mb-xl-0 mb-5 d-flex flex-column gap-2 text-center">
      @if($bill->user->settings->add_tax_invoice)
        <p class="m-0">@if($bill->debit_note_bill_id == null) {{ __('Simplified Tax Invoice') }} @else {{ __('Tax debit note') }} @endif</p>
      @endif
      <p class="m-0">{{ $bill->user->business_name }}</p>
      @if(isset($bill->user->settings->header_bill))
        <p class="m-0">{{ $bill->user->settings->header_bill }}</p>
      @endif
      <p class="m-0">{{  $bill->user->business_address }}</p>
      <p class="m-0">{{  $bill->user->business_mobile }}</p>
    </div>
    <div id="status">
      @include('bills.print_template.partials.status',['bill' => $bill, 'lang' => $lang])
    </div><!-- status -->
  </div><!-- bill-header -->

  <div class="flex-grow-1">

    <div class="p-2 d-flex flex-column gap-2">
      @if($bill->debit_note_bill_id == null)
        @include('bills.print_template.partials.bill_info',['bill' => $bill, 'lang' => $lang])
      @else
        @include('bills.print_template.partials.debit_note_info',['bill' => $bill, 'lang' => $lang])
      @endif
    </div><!-- p-2 -->

    @include('bills.print_template.partials.bill_items', ['bill' => $bill, 'lang' => $lang])

    <div class="d-flex flex-column gap-3 p-2">
      @if( $bill->add_tax || $bill->add_discount)
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="mb-0">
            {{ __('Total amount') }}
            @if( $bill->add_tax)
              <small class="d-block text-muted mt-1">( {{ __('Exclude added tax') }} )</small>
            @endif
          </p>
          <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
            {{ $bill->sub_total }} <i class="sar-icon"></i>
          </p>
        </div>
      @endif
      @if( $bill->add_discount)
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="mb-0">{{ __('Discount amount') }}</p>
          <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
            {{ $bill->discount }} <i class="sar-icon"></i>
          </p>
        </div>
      @endif
      @if( $bill->user->pay_fees == 'client')
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="mb-0">{{ __('payment fees') }}</p>
          <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
            {{ $bill->payment_fees }} <i class="sar-icon"></i>
          </p>
        </div>
      @endif
      @if( $bill->add_tax)
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="mb-0">{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value]) }}</p>
          <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
            {{ $bill->vat }} <i class="sar-icon"></i>
          </p>
        </div>
      @endif
      @if( $bill->channel_extra_amount)
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="mb-0">{{$bill->channel_extra_title}}</p>
          <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
            {{ $bill->channel_extra_amount }} <i class="sar-icon"></i>
          </p>
        </div>
      @endif
      @if( $bill->channel_extra_vat)
        <div class="d-flex align-items-center justify-content-between gap-2">
          <p class="mb-0">{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</p>
          <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading fw-medium">
            {{ $bill->channel_extra_vat }} <i class="sar-icon"></i>
          </p>
        </div>
      @endif
      {{-- @if( $bill->refund_amount)
        <div class="d-flex align-items-center justify-content-between">
          <span class="d-block mb-2">{{ __('Refund Amount') }}</span>
          <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0 text-heading">
            {{ $bill->refund_amount }}  <span class="riyal-symbol-font">$</span>
          </div><!-- d-flex -->
        </div><!-- d-flex -->
      @endif --}}
      <div class="d-flex align-items-center justify-content-between gap-2 border-top pt-3 fw-bold">
        <p class="mb-0">{{ __('Total amount') }}</p>
        <p class="mb-0 d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 text-heading">
          {{ $bill->sub_total + $bill->vat - $bill->discount}} <i class="sar-icon"></i>
        </p>
      </div>
    </div><!-- d-flex -->

  </div>

  <div class="flex-shrink-0">
    @if($bill->customer_notes)
      <hr class="my-0">
      <div class="card-body p-2 text-heading text-capitalize">{{$bill->customer_notes}}</div>
    @endif

    @if($bill->user->settings->add_tax_invoice)
      <hr class="my-0">
      <div class="card-body p-2 text-heading text-center text-capitalize">
        <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
          {!! generateQRcode($bill) !!}
          <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
          <span class="d-block text-body">{{ __('Tax Invoice') }}</span>
        </a>
      </div>
    @endif

    @if(isset($bill->user->settings->footer_bill))
      <hr class="my-0">
      <div class="card-body p-2 text-heading text-center text-capitalize">{{ $bill->user->settings->footer_bill }}</div>
    @endif
  </div>

</div>


<script>
  window.onload = function() {
    window.print();
  }
</script>
