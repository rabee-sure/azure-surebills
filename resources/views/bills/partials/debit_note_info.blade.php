<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">{{ __('Debit Note Date') }}</span>
    <span class="d-block mb-2">{{ $bill->created_at->format('d/m/Y')}}</span>
</div><!-- d-flex -->
<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">{{ __('Debit Note Number') }}</span>
    <span class="d-block mb-2">{{ $bill->number }}</span>
</div><!-- d-flex -->

<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">{{ __('Date') }}</span>
    <span class="d-block mb-2">{{ $bill->mainBill->created_at->format('d/m/Y')}}</span>
</div><!-- d-flex -->

<div class="d-flex align-items-center justify-content-between">
    <span class="d-block mb-2">
        @if($bill->user->settings->add_tax_invoice)
        {{ __('Bill No.') }}
        @else
        {{ __('No.') }}
        @endif
    </span>
    <a href="{{route('bills.show', $bill->mainBill)}}" title="{{__('Bill')}} {{ $bill->mainBill->number }}" target="_blank"><span class="d-block mb-2">{{ $bill->mainBill->number }}</span></a>
</div><!-- d-flex -->