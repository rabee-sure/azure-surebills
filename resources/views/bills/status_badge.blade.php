    @if($status == 'pending')
      <span id="status-{{$id}}" class="badge bg-label-info bill_status_badge">{{ __('Pending')}}</span>
    @elseif($status == 'paid')
      <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid')}}</span>

    @elseif($status == 'canceled')
      <span id="status-{{$id}}"  class="badge bg-label-danger bill_status_badge">{{ __('Canceled')}}</span>
    @elseif($status == 'expired')
      <span id="status-{{$id}}"  class="badge bg-label-light bill_status_badge">{{ __('Expired')}}</span>
    @elseif($status == 'refunded')
      {{-- <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded')}}</span> --}}
      <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid')}}</span>
    @elseif($status == 'failed')
      <span id="status-{{$id}}"  class="badge bg-label-danger bill_status_badge">{{ __('Failed')}}</span>
    @elseif($status == 'rejected')
      <span id="status-{{$id}}"  class="badge bg-label-danger bill_status_badge">{{ __('Rejected')}}</span>
    @elseif($status == 'paid_cash')
        <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid Cash')}}</span>
    @elseif($status == 'paid_bank_transfer')
        <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid Bank Transfer')}}</span>
    @elseif($status == 'paid_machine')
        <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid Machine')}}</span>
    @elseif($status == 'refunded_cash')
        {{-- <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded Cash')}}</span> --}}
        <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid Cash')}}</span>
    @elseif($status == 'refunded_bank_transfer')
        {{-- <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded Bank Transfer')}}</span> --}}
        <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid Bank Transfer')}}</span>
    @elseif($status == 'refunded_machine')
        <span id="status-{{$id}}"  class="badge bg-label-success bill_status_badge">{{ __('Paid Machine')}}</span>
    @elseif($status == 'cn_refunded')
        {{-- <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Credit Note')}}</span> --}}
        @if($method == 'online')
          <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded')}}</span>
        @elseif($method == 'cash')
          <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded Cash')}}</span>
        @elseif($method == 'bank_transfer')
          <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded Bank Transfer')}}</span>
        @elseif($method == 'payment_machine')
          <span id="status-{{$id}}"  class="badge bg-label-warning bill_status_badge">{{ __('Refunded Machine')}}</span>
        @endif
    @endif
