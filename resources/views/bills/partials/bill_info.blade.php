<div class="d-flex align-items-center justify-content-start gap-2">
  <span class="d-block flex-shrink-0">
    @if($bill->user->settings->add_tax_invoice)
      {{ __('Bill No.') }}
    @else
      {{ __('No.') }}
    @endif :
  </span>
  <span class="d-block flex-shrink-0">{{ $bill->number }}</span>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-start gap-2">
  <span class="d-block flex-shrink-0">{{ __('Date') }} :</span>
  <span class="d-block flex-shrink-0">{{ $bill->created_at->format('d/m/Y')}}</span>
</div><!-- d-flex -->

@if($bill->user->settings->add_tax_invoice && $bill->user->vat_registration_number)
<div class="d-flex align-items-center justify-content-start gap-2">
  <span class="d-block flex-shrink-0">{{ __('Organization VAT Registration Number') }} :</span>
  <span class="d-block flex-shrink-0">{{ $bill->user->vat_registration_number }}</span>
</div><!-- d-flex -->
@endif
@if($bill->user->settings->display_customer_details)
<div class="d-flex align-items-center justify-content-start gap-2">
  <span class="d-block flex-shrink-0">{{ __('Customer Name') }} :</span>
  <span class="d-block flex-shrink-0">{{ $bill->customer_name }}</span>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-start gap-2">
  <span class="d-block flex-shrink-0">{{ __('Mobile Number') }} :</span>
  <span class="d-block flex-shrink-0">{{ $bill->customer_mobile }}</span>
</div><!-- d-flex -->
@endif
