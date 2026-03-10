<tr>
  <td>
    <a href="@if($bill->model == 'bills'){{route('bills.show', $bill)}}@elseif ($bill->model == 'refundedbills'){{route('refundedbills.show', $bill->id)}} @endif" title="@if($bill->model == 'bills' && $bill->debit_note_bill_id == null){{__('Bill')}}@endif {{ $bill->number }} - {{ $bill->customer_name}}">
      @if($bill->model == 'bills' && $bill->debit_note_bill_id == null){{__('Bill')}}@endif {{ $bill->number }} @if($bill->customer_name != null) - @endif {{ $bill->customer_name}}
    </a>
  </td>
  <td>
    <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
    {{ $bill->sub_total + $bill->vat - $bill->discount}} <i class="sar-icon"></i>
    </span>
  </td>
  <td>{{ $bill->created_at}}</td>
  <td>@include('bills.status_badge', ['status' => $bill->status, 'method' => $bill->method,'id' => $bill->id])</td>
  <td class="align-middle">
    <a href="@if($bill->model == 'bills'){{route('bills.show', $bill)}}@elseif ($bill->model == 'refundedbills'){{route('refundedbills.show', $bill->id)}} @endif" title="{{ __('Details') }}" data-bs-toggle="tooltip" data-bs-placement="top" class="btn btn-icon text-white btn-sm btn-primary waves-effect waves-light">
      <span class="icon-base ti ti-eye icon-18px"></span>
    </a>
  </td>
</tr>

@push('footer-scripts')
<script type="text/javascript">
  if (typeof window.Echo !== 'undefined') {
    Echo.channel('bill.{{$bill->id}}')
    .listen('BillStatusUpdated', (e) => {
        console.log(e.bill.id);
        var className;

        switch(e.bill.status) {
          case "pending":
            className = "bg-label-info";
            break;
          case "paid":
            className = " bg-label-success";
            break;
          case "paid_cash":
            className = " bg-label-success";
            break;
          case "paid_bank_transfer":
            className = " bg-label-success";
            break;
          case "paid_machine":
            className = " bg-label-success";
            break;
          case "canceled":
            className = "bg-label-danger";
            break;
          case "expired":
            className = "bg-label-light";
            break;
          case "refunded":
            className = "bg-label-warning";
            break;
          case "refunded_cash":
            className = "bg-label-warning";
            break;
          case "refunded_bank_transfer":
            className = "bg-label-warning";
            break;
          case "refunded_machine":
            className = "bg-label-warning";
            break;
          case "failed":
            className = "bg-label-danger";
            break;
          default:
            className = "bg-label-info";
        }
        $('#status-{{$bill->id}}')
          .text(e.bill.trans_status)
          .removeClass('bg-label-light bg-label-danger bg-label-success bg-label-info')
          .addClass(className);
    });
  }
</script>
@endpush
