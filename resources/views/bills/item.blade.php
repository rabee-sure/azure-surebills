<tr>
  <td>
    <a href="@if($bill->model == 'bills'){{route('bills.show', $bill)}}@elseif ($bill->model == 'refundedbills'){{route('refundedbills.show', $bill->id)}} @endif" title="@if($bill->model == 'bills' && $bill->debit_note_bill_id == null){{__('Bill')}}@endif {{ $bill->number }} - {{ $bill->customer_name}}">
      @if($bill->model == 'bills' && $bill->debit_note_bill_id == null){{__('Bill')}}@endif {{ $bill->number }} @if($bill->customer_name != null) - @endif {{ $bill->customer_name}}
    </a>
  </td>
  <td class="text-center">
    <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl">
      {{ $bill->sub_total + $bill->vat - $bill->discount}}  <span class="riyal-symbol-font">$</span>
    </div>
  </td>
  <td class="text-center">{{ $bill->created_at}}</td>
  <td class="text-center">@include('bills.status_badge', ['status' => $bill->status, 'method' => $bill->method,'id' => $bill->id])</td>
</tr>

@push('footer-scripts')
<script type="text/javascript">
  console.log('bill status: {{$bill->status}} _ id : {{$bill->id}}');

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
          case "paid_cash":
            className = " badge-success";
            break;
          case "paid_bank_transfer":
            className = " badge-success";
            break;
          case "paid_machine":
            className = " badge-success";
            break;
          case "canceled":
            className = "badge-danger";
            break;          
          case "expired":
            className = "badge-light";
            break;
          case "refunded":
            className = "badge-warning";
            break;
          case "refunded_cash":
            className = "badge-warning";
            break;
          case "refunded_bank_transfer":
            className = "badge-warning";
            break;
          case "refunded_machine":
            className = "badge-warning";
            break;
          case "failed":
            className = "badge-danger";
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
