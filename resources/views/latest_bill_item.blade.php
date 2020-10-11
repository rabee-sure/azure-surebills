<tr>
  <td><a href="{{ route('bills.show', $bill) }}" title="{{ __('Bill')}} {{ $bill->number }} - {{ $bill->customer_name }}">{{ __('Bill')}} {{ $bill->number }} - {{ $bill->customer_name }}</a></td>
  <td>{{ $bill->total }} {{ __('SAR')}}</td>
  <td>{{ $bill->created_at }}</td>
  <td>
    @if($bill->status == 'pending')
      <span id="status-{{$bill->id}}" class="badge badge-pill badge-info bill_status_badge">{{ __('Pending')}}</span>
    @endif
    @if($bill->status == 'paid')
      <span id="status-{{$bill->id}}"  class="badge badge-pill badge-success bill_status_badge">{{ __('Paid')}}</span>
    @endif             
    @if($bill->status == 'canceled')
      <span id="status-{{$bill->id}}"  class="badge badge-pill badge-danger bill_status_badge">{{ __('Canceled')}}</span>
    @endif              
    @if($bill->status == 'expired')
      <span id="status-{{$bill->id}}"  class="badge badge-pill badge-light bill_status_badge">{{ __('Expired')}}</span>
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