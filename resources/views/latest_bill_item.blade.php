<tr>
    <td class="py-2">
      <a href="{{ route('bills.show', $bill) }}">
          <p class="font-weight-bold">{{ __('Bill')}} {{ $bill->number }} - {{ $bill->customer_name }}</p>
          <p class="font-weight-normal">{{ $bill->total }} {{ __('SAR')}}</p>
          <time class="text-muted text-small mb-0 font-weight-light">{{ $bill->created_at }}</time>
      </a>
    </td>
    <td class="py-2">
      @if($bill->status == 'pending')
        <span id="status-{{$bill->id}}" class="badge badge-pill badge-info d-inline-block">{{ __('Pending')}}</span>
      @endif
      @if($bill->status == 'paid')
        <span id="status-{{$bill->id}}"  class="badge badge-pill badge-success d-inline-block">{{ __('Paid')}}</span>
      @endif             
      @if($bill->status == 'canceled')
        <span id="status-{{$bill->id}}"  class="badge badge-pill badge-danger d-inline-block">{{ __('Canceled')}}</span>
      @endif              
      @if($bill->status == 'expired')
        <span id="status-{{$bill->id}}"  class="badge badge-pill badge-light d-inline-block">{{ __('Expired')}}</span>
      @endif
    </td>
</tr>
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