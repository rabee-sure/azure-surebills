<div class="card d-flex flex-row mb-3">
    <div class="d-flex flex-grow-1 min-width-zero">
        <div class="card-body align-self-center d-flex flex-column flex-md-row justify-content-between min-width-zero align-items-md-center">
            <span class="list-item-heading mb-0 truncate w-40 w-xs-100 text-secondary">
                <a href="{{route('bills.show', $bill)}}">
                    {{ $bill->business_name}} - {{ $bill->customer_name}}
                </a>
            </span>
            <p class="mb-0 text-muted text-small w-15 w-xs-100">{{ $bill->total}} SAR</p>
            <p class="mb-0 text-muted text-small w-15 w-xs-100">{{ $bill->created_at}} PM</p>
            <div class="w-15 w-xs-100 text-center">
                @if($bill->status == 'pending')
                <span class="badge badge-pill badge-info d-inline-block">Pending</span>
                @endif
                @if($bill->status == 'paid')
                <span class="badge badge-pill badge-success d-inline-block">Paid</span>
                @endif             
                @if($bill->status == 'canceled')
                <span class="badge badge-pill badge-light d-inline-block">Canceled</span>
                @endif
            </div>
            <a href="{{ $bill->pay_url}}"  target="_blank" class="badge  badge-secondary d-inline-block"> <i class="simple-icon-link"></i></a>
        </div>
    </div>
</div>