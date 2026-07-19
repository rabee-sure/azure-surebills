<li class="{{ $loop->last ? '' : 'mb-6' }}">
  <div class="row g-2 align-items-center justify-content-between">
    <div class="col-12 col-md-8">
      <div class="d-flex align-items-center justify-content-start gap-2">
        <div class="badge bg-label-secondary text-body p-2 me-4 rounded">
          <i class="icon-base ti ti-file-invoice icon-md"></i>
        </div>
        <div class="me-2 flex-grow-1" style="min-width: 0;">
          <h6 class="mb-0">
            <a class="d-block text-truncate" href="@if(in_array($bill->type, ['bill', 'debit_note'])){{route('bills.show', $bill)}} @elseif ($bill->type == 'credit_note'){{route('refundedbills.show', $bill->id)}} @endif" title="@if($bill->type == 'bill'){{__('Bill')}} @elseif($bill->type == 'debit_note') {{__('DN')}} @elseif ($bill->type == 'credit_note') {{__('CN')}} @endif {{ $bill->number }} - {{ $bill->customer_name}}">
              @if($bill->type == 'bill'){{__('Bill')}} @elseif($bill->type == 'debit_note') {{__('DN')}} @elseif ($bill->type == 'credit_note') {{__('CN')}} @endif {{ $bill->number }} @if($bill->customer_name != null) - @endif {{ $bill->customer_name}}
            </a>
          </h6>
          <small class="text-body">{{ $bill->created_at }}</small>
        </div>
      </div><!-- d-flex -->
    </div><!-- col-6 -->
    <div class="col-6 col-md-2">
      <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 m-0 text-nowrap" style="min-width: 6rem;">
        {{ $bill->fixed_total }} <i class="sar-icon"></i>
      </span>
    </div><!-- col-6 -->
    <div class="col-6 col-md-2">
      <div class="ms-4 flex-shrink-0" style="min-width: 8rem;">@include('bills.status_badge', ['status' => $bill->status, 'id' => $bill->id, 'method' => $bill->method])</div>
    </div><!-- col-6 -->
  </div><!-- row -->
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
