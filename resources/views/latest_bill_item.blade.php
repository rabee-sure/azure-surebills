<li class="{{ $loop->last ? '' : 'mb-6' }}">
  <div class="d-flex align-items-center">
    <div class="badge bg-label-secondary text-body p-2 me-4 rounded">
      <i class="icon-base ti ti-file-invoice icon-md"></i>
    </div>
    <div class="d-flex justify-content-between w-100 flex-wrap gap-2">
      <div class="me-2">
        <h6 class="mb-0">
          <a href="@if(in_array($bill->type, ['bill', 'debit_note'])){{route('bills.show', $bill)}} @elseif ($bill->type == 'credit_note'){{route('refundedbills.show', $bill->id)}} @endif" title="@if($bill->type == 'bill'){{__('Bill')}} @elseif($bill->type == 'debit_note') {{__('DN')}} @elseif ($bill->type == 'credit_note') {{__('CN')}} @endif {{ $bill->number }} - {{ $bill->customer_name}}">
            @if($bill->type == 'bill'){{__('Bill')}} @elseif($bill->type == 'debit_note') {{__('DN')}} @elseif ($bill->type == 'credit_note') {{__('CN')}} @endif {{ $bill->number }} @if($bill->customer_name != null) - @endif {{ $bill->customer_name}}
          </a>
        </h6>
        <small class="text-body">{{ $bill->created_at }}</small>
      </div>
      <div class="d-flex align-items-center">
        <p class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0">
          {{ $bill->fixed_total }} <i class="sar-icon"></i>
        </p>
        <div class="ms-4">@include('bills.status_badge', ['status' => $bill->status, 'id' => $bill->id, 'method' => $bill->method])</div>
      </div>
    </div>
  </div>
</li>

@push('footer-scripts')
<script type="text/javascript">
  console.log('bill.{{$bill->id}}');
  Echo.channel('bill.{{$bill->id}}')
    .listen('BillStatusUpdated', (e) => {
        console.log(e.bill.id);
        var className;

        switch(e.bill.status) {
          case "pending":
            className = "badge-info";
            break;
          case "paid":
            className = " badge-success";
            break;
          case "canceled":
            className = "badge-danger";
            break;
          case "expired":
            className = "badge-light";
            break;
          default:
            className = "badge-info";
        }
        $('#status-{{$bill->id}}')
          .text(e.bill.trans_status)
          .removeClass('badge-light badge-danger badge-success badge-info')
          .addClass(className);
    });
</script>
@endpush
