<div class="paymentsLog bg-white shadow-sm rounded-3 p-2 mb-3">
  <div class="titleBlock mb-3 text-body fw-bold">{{__('Credit Notes')}}</div>
    <div class="table-responsive">
      <table class="table table-hover text-nowrap">
        <thead>
          <tr>
            <th scope="col" class="text-center border p-2 bg-light fw-normal">{{__('Reference Number') }}</th>
            <th scope="col" class="text-center border p-2 bg-light fw-normal">{{__('Date created') }}</th>
            <th scope="col" width="10%" class="text-center border p-2 bg-light fw-normal">{{__('Amount') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bill->refundedBills->sortByDesc('created_at') as $refundedBill)
            <tr>
            <td class="text-center p-2 border"><a href="{{route('refundedbills.show', $refundedBill->id)}}">CN{{$refundedBill->number}}</a></td>
            <td class="text-center p-2 border">{{$refundedBill->created_at}}</td>
            <td class="text-center p-2 border">{{$refundedBill->amount}}</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div><!-- table-responsive -->
</div><!-- paymentsLog -->