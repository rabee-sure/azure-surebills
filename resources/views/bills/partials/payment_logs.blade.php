<div class="card">
  <h5 class="card-title p-4 m-0">{{__('Payment Transactions')}}</h5>
  <div class="table-responsive text-nowrap">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th scope="col" width="5%" class="fw-bold"></th>
          <th scope="col" class="fw-bold">{{__('ID') }}</th>
          <th scope="col" class="fw-bold">{{__('Values') }}</th>
          <th scope="col" class="fw-bold">{{__('Date created') }}</th>
          <th scope="col" width="10%" class="fw-bold">{{__('Type') }}</th>
          <th scope="col" width="10%" class="fw-bold">{{__('Status') }}</th>
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
                $refund_amount = $log->refunded_amount;
            }

            // total amount
            if (isset($log->results['bill']['total'])) {
                $total_amount = $log->results['bill']['total'];
            } else if (isset($log->results['transaction']['amount'])) {
                $total_amount = $log->results['transaction']['amount'];
            } else if(isset($log->results['orderInformation']['amountDetails']['totalAmount'])) {
                $total_amount = $log->results['orderInformation']['amountDetails']['totalAmount'];
            } else {
                $total_amount = $bill->total;
            }
          @endphp
          <tr>
            <td>
              @if ($brand == 'VISA')
                <img alt="visa" src="{{ asset('assets/v2/img/payments/visa.png') }}">
              @elseif ($brand == 'MASTER')
                <img alt="mastercard" src="{{ asset('assets/v2/img/payments/mastercard.png') }}">
              @elseif ($brand == 'MADA')
                <img alt="mada" src="{{ asset('assets/v2/img/payments/mada.png') }}">
              @elseif ($brand == 'APPLEPAY')
                <img alt="applepay" src="{{ asset('assets/v2/img/payments/applepay.png') }}">
              @else
                <img src="{{ asset('assets/v2/img/payments/cardnon.png') }}" alt="card non">
              @endif
            </td>
            <td>
              <a href="/logs/{{$log->id}}" title="{{ $log->id }}">{{ $log->id }}</a>
            </td>
            <td>
              <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                @if($log->payment_method == 'mastercard_refund')
                  {{ $refund_amount }} <i class="sar-icon"></i>
                @else
                  {{ $total_amount }} <i class="sar-icon"></i>
                @endif
              </span>
            </td>
            <td>{{$log->created_at}}</td>
            <td>
              @if($log->payment_method == 'mastercard_refund')
                <span class="badge bg-label-warning">{{ __('Refund') }}</span>
              @elseif($log->payment_method != 'mastercard_refund')
                <span class="badge bg-label-success">{{ __('Paid') }}</span>
              @endif
            </td>
            <td>
              @if($log->webhook_response_received == true)
                @if($log->status == true)
                  <span class="badge bg-label-success">{{ __('Successfull') }}</span>
                @else
                  <span class="badge bg-label-danger">{{ __('Failed') }}</span>
                @endif
              @else
                @if($log->payment_method != 'mastercard_refund' && $log->results['success'] == false)
                  <span class="badge bg-label-danger">{{ __('Failed') }}</span>
                @else
                  <span class="badge bg-label-warning">{{ __('Waiting') }}</span>
                @endif
              @endif
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div><!-- table-responsive -->
</div><!-- card -->
