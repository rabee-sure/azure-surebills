<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">{{ __('Debit Note Date') }}</p>
    <p class="mb-0">{{ $bill->created_at->format('d/m/Y')}}</p>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">{{ __('Debit Note Number') }}</p>
    <p class="mb-0">{{ $bill->number }}</p>
</div><!-- d-flex -->

<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">{{ __('Date') }}</p>
    <p class="mb-0">{{ $bill->mainBill->created_at->format('d/m/Y')}}</p>
</div><!-- d-flex -->

<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">
        @if($bill->user->settings->add_tax_invoice)
        {{ __('Bill No.') }}
        @else
        {{ __('No.') }}
        @endif
    </p>
    <a href="{{route('bills.show', $bill->mainBill)}}" title="{{__('Bill')}} {{ $bill->mainBill->number }}" target="_blank"><p class="mb-0">{{ $bill->mainBill->number }}</p></a>
</div><!-- d-flex -->
@if($bill->user->settings->add_tax_invoice && $bill->user->vat_registration_number)
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">{{ __('Organization VAT Registration Number') }}</p>
    <p class="mb-0">{{ $bill->user->vat_registration_number }}</p>
</div><!-- d-flex -->
@endif
@if($bill->user->settings->display_customer_details)
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">{{ __('Customer Name') }}</p>
    <p class="mb-0">{{ $bill->customer_name }}</p>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-between gap-2">
    <p class="mb-0">{{ __('Mobile Number') }}</p>
    <p class="mb-0">{{ $bill->customer_mobile }}</p>
</div><!-- d-flex -->
@endif
