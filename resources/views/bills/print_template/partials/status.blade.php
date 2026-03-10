@if($bill->status == 'expired')
    <div class="alertMsg text-center fw-bold expired">
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been expired', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been expired', ['number' => $bill->number ], $lang) }}
        @endif
    </div>
@elseif(in_array($bill->status, ['paid', 'refunded']))
    <div class="alertMsg text-center fw-bold paid">
    @if ($bill->depositTransaction)
        {{ __('Paid', [], $lang) }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
    @else
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been successfully', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been successfully', ['number' => $bill->number ], $lang) }}
        @endif
    @endif
    </div>
@elseif(in_array($bill->status, ['paid_cash', 'refunded_cash']))
    <div class="alertMsg text-center fw-bold paid">
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been Paid Cash successfully', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been Paid Cash successfully', ['number' => $bill->number ], $lang) }}
        @endif
    </div>
@elseif(in_array($bill->status, ['paid_bank_transfer', 'refunded_bank_transfer']))
    <div class="alertMsg text-center fw-bold paid">
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been Paid Bank Transfer successfully', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been Paid Bank Transfer successfully', ['number' => $bill->number ], $lang) }}
        @endif
    </div>
@elseif(in_array($bill->status, ['paid_machine', 'refunded_machine']))
    <div class="alertMsg text-center fw-bold paid">
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been Paid Machine successfully', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been Paid Machine successfully', ['number' => $bill->number ], $lang) }}
        @endif
    </div>
@elseif($bill->status == 'canceled')
    <div class="alertMsg text-center fw-bold canceled">
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been canceled', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been canceled', ['number' => $bill->number ], $lang) }}
        @endif
    </div>
@elseif($bill->status == 'failed')
    <div class="alertMsg text-center fw-bold canceled">
        @if($bill->debit_note_bill_id == null)
        {{ __('this bill has been failed', ['number' => $bill->number ], $lang) }}
        @else
        {{ __('this debit note has been failed', ['number' => $bill->number ], $lang) }}
        @endif
    </div>
{{-- @elseif(in_array($bill->status, ['refunded', 'refunded_cash', 'refunded_bank_transfer']))
    <div class="alertMsg text-center fw-bold refunded"> {{ __('this bill has been refunded', ['number' => $bill->number ],$lang) }}</div> --}}
@endif
