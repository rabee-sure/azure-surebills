@if($bill->status == 'expired')
  <div class="alert alert-danger" role="alert">
    {{ __('this bill has been expired', ['number' => 'DN'.$bill->number ]) }}
  </div>
@endif
@if($bill->status == 'paid')
  <div class="alert alert-success" role="alert">
    @if ($bill->depositTransaction)
      {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
    @else
    {{ __('this bill has been successfully', ['number' => 'DN'.$bill->number ]) }}
    @endif
  </div>
@endif
@if($bill->status == 'canceled')
  <div class="alert alert-danger" role="alert">
    {{ __('this bill has been canceled', ['number' => 'DN'.$bill->number ]) }}
  </div>
@endif