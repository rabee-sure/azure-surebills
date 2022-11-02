<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">
        @if($bill->user->settings->add_tax_invoice)
        {{ __('Bill No.', [], $lang) }}
        @else
        {{ __('No.', [], $lang) }}
        @endif
    </span>
    <span class="d-block mb-2">{{ $bill->number }}</span>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">{{ __('Date', [], $lang) }}</span>
    <span class="d-block mb-2">{{ $bill->created_at->format('d/m/Y')}}</span>
</div><!-- d-flex -->

@if($bill->user->settings->add_tax_invoice && $bill->user->vat_registration_number)
<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">{{ __('Organization VAT Registration Number', [], $lang) }}</span>
    <span class="d-block mb-2">{{ $bill->user->vat_registration_number }}</span>
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