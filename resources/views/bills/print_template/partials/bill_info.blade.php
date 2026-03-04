<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="d-block mb-0">
        @if($bill->user->settings->add_tax_invoice)
        {{ __('Bill No.', [], $lang) }}
        @else
        {{ __('No.', [], $lang) }}
        @endif
    </p>
    <p class="d-block mb-0">{{ $bill->number }}</p>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="d-block mb-0">{{ __('Date', [], $lang) }}</p>
    <p class="d-block mb-0">{{ $bill->created_at->format('d/m/Y')}}</p>
</div><!-- d-flex -->

@if($bill->user->settings->add_tax_invoice && $bill->user->vat_registration_number)
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="d-block mb-0">{{ __('Organization VAT Registration Number', [], $lang) }}</p>
    <p class="d-block mb-0">{{ $bill->user->vat_registration_number }}</p>
</div><!-- d-flex -->
@endif
@if($bill->user->settings->display_customer_details)
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="d-block mb-0">{{ __('Customer Name', [], $lang) }}</p>
    <p class="d-block mb-0">{{ $bill->customer_name }}</p>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="d-block mb-0">{{ __('Mobile Number', [], $lang) }}</p>
    <p class="d-block mb-0">{{ $bill->customer_mobile }}</p>
</div><!-- d-flex -->
@endif
