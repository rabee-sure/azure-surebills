<div class="paymentsLog bg-white shadow-sm rounded-3 p-2 mb-3">
  <div class="titleBlock mb-3 text-body fw-bold">{{__('Payment Transactions')}}</div>
    <div class="table-responsive">
      <table class="table table-hover text-nowrap">
        <thead>
          <tr>
            <th scope="col" width="5%" class="text-center border p-2 bg-light fw-normal"></th>
            <th scope="col" class="text-center border p-2 bg-light fw-normal">{{__('ID') }}</th>
            <th scope="col" class="text-center border p-2 bg-light fw-normal">{{__('Values') }}</th>
            <th scope="col" class="text-center border p-2 bg-light fw-normal">{{__('Date created') }}</th>
            <th scope="col" width="10%" class="text-center border p-2 bg-light fw-normal">{{__('Status') }}</th>
          </tr>
        </thead>
        <tbody>
          @foreach($bill->payment_logs as $log)
            @php
              // brand
              if (isset($log->results['response']) && isset($log->results['response']['paymentBrand'])) {
                  $brand = $log->results['response']['paymentBrand'];
              } else {
                  $brand = $log->brand;
              }

              // refund amount
              if (isset($log->results['transaction']['amount'])) {
                  $refund_amount = $log->results['transaction']['amount'];
              } else {
                  $refund_amount = $log->refund_amount;
              }

              // total amount
              if (isset($log->results['bill']['total'])) {
                  $total_amount = $log->results['bill']['total'];
              } else if (isset($log->results['transaction']['amount'])) {
                  $total_amount = $log->results['transaction']['amount'];
              } else {
                  $total_amount = '---';
              }
            @endphp
            <tr>
            <td class="text-center p-2 border">
              @if($brand == 'MADA')
                <img src="{{ asset('/images/payments/mada.png') }}" alt="mada" height="25px">
              @elseif($brand == 'VISA')
                <img src="{{ asset('/images/payments/visa.png') }}" alt="visa" height="25px">
              @elseif($brand == 'MASTERCARD')
                <img src="{{ asset('/images/payments/card.png') }}" alt="mastercard" height="25px">
              @elseif($brand == 'APPLEPAY')
                <img src="{{ asset('/images/payments/pay.png') }}" alt="apple pay" height="25px">
              @else
                <img src="{{ asset('/images/payments/cardnon.png') }}" alt="apple pay" height="25px">
              @endif
            </td>
            <td class="text-center p-2 border">
              <a href="/logs/{{$log->id}}" title="{{ $log->id }}">{{ $log->id }}</a>
            </td>
            <td class="text-center p-2 border">
              @if($log->payment_method == 'mastercard_refund')
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{ $refund_amount }} <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              @else
                <div class="d-flex align-items-center justify-content-center gap-1 fw-bold rtl flex-shrink-0">
                  {{ $total_amount }} <span class="riyal-symbol-font">$</span>
                </div><!-- d-flex -->
              @endif
            </td>
            <td class="text-center p-2 border">{{$log->created_at}}</td>
            <td class="text-center p-2 border">
              @if($log->payment_method == 'mastercard_refund')
                <span class="badge badge-pill badge-warning bill_status_badge">{{ __('Refund') }}</span>
              @elseif($log->status == true)
                <span class="badge badge-pill badge-success bill_status_badge">{{ __('Paid') }}</span> 
              @else
                <span class="badge badge-pill badge-danger bill_status_badge">{{ __('Failed') }}</span>
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div><!-- table-responsive -->
</div><!-- paymentsLog -->