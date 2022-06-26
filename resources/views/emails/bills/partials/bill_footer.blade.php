@if($bill->customer_notes)<div class="customerNotes">{{$bill->customer_notes}}</div> @endif

<div class="clientInfo">
  <span>{{ $bill->customer_name }}</span>
  <span dir="ltr">+966{{ $bill->customer_mobile }}</span>
  <span dir="ltr">{{ $bill->customer_email }}</span>
</div><!-- clientInfo -->

@if($bill->user->settings->add_tax_invoice)
  <div class="qrCodeArea">
    {!! generateQRcode($bill) !!}
    <span>{{ __('Tax Invoice') }}</span>
  </div><!-- qrCodeArea -->
@endif
  
@if(isset($bill->user->settings->footer_bill))
  <div class="footerBillText">{{ $bill->user->settings->footer_bill }}</div>
@endif