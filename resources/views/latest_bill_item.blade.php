<tr>
  <td>
    <a href="@if(get_class($bill) == 'App\Models\Bill'){{route('bills.show', $bill)}} @elseif (get_class($bill) == 'App\Models\RefundedBill'){{route('refundedbills.show', $bill->id)}} @endif" title="@if(get_class($bill) == 'App\Models\Bill' && $bill->debit_note_bill_id == null){{__('Bill')}} @elseif(get_class($bill) == 'App\Models\Bill' && $bill->debit_note_bill_id) {{__('DN')}} @elseif (get_class($bill) == 'App\Models\RefundedBill') {{__('CN')}} @endif {{ $bill->number }} - {{ $bill->customer_name}}">
    @if(get_class($bill) == 'App\Models\Bill' && $bill->debit_note_bill_id == null){{__('Bill')}} @elseif(get_class($bill) == 'App\Models\Bill' && $bill->debit_note_bill_id) {{__('DN')}} @elseif (get_class($bill) == 'App\Models\RefundedBill') {{__('CN')}} @endif {{ $bill->number }} @if($bill->customer_name != null) - @endif {{ $bill->customer_name}}
    </a>
  </td>
  <td class="text-center">
    <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl">
      {{ $bill->fixed_total }}  <span class="riyal-symbol-font">$</span>
    </div>
  </td>
  <td class="text-center">{{ $bill->created_at }}</td>
  <td class="text-center">@include('bills.status_badge', ['status' => $bill->status, 'id' => $bill->id])</td>
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