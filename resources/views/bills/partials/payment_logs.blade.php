<div id="paymentslog" class="col-12 col-md-6 col-lg-6 col-xl-6">
    <div class="card">
        <div class="card-body">
            <h2 class="mb-3">{{__('Payment Transactions')}}</h2>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col" width="5%"></th>
                            <th scope="col">{{__('ID') }}</th>
                            <th scope="col">{{__('Values') }}</th>
                            <th scope="col">{{__('Date created') }}</th>
                            <th scope="col" width="10%">{{__('Status') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($bill->payment_logs as $log)
                            <tr>
                                <td>
                                    @if(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'MADA')
                                        <img src="{{ asset('/images/payments/mada.png') }}" alt="mada" height="25px">
                                    @elseif(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'VISA')
                                        <img src="{{ asset('/images/payments/visa.png') }}" alt="visa" height="25px">
                                    @elseif(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'MASTERCARD')
                                        <img src="{{ asset('/images/payments/card.png') }}" alt="mastercard" height="25px">
                                    @elseif(isset($log->results['response']) && isset($log->results['response']['paymentBrand']) && $log->results['response']['paymentBrand'] == 'APPLEPAY')
                                        <img src="{{ asset('/images/payments/pay.png') }}" alt="apple pay" height="25px">
                                    @else
                                        <img src="{{ asset('/images/payments/cardnon.png') }}" alt="apple pay" height="25px">
                                    @endif
                                </td>

                                {{-- ID --}}
                                <td>
                                    <a href="/logs/{{$log->id}}" title="{{ $log->id }}">
                                        {{ $log->id }}
                                    </a>
                                </td>

                                {{-- Values --}}
                                <td>
                                    @if($log->payment_method == 'mastercard_refund')
                                        @if(isset($log->results['transaction']['amount']))
                                            {{ $log->results['transaction']['amount']}} {{__('SAR') }}
                                        @else
                                            {{ $log->refund_amount}} {{__('SAR') }}
                                        @endif
                                    @else
                                        @if(isset($log->results['bill']['total']))
                                            {{ $log->results['bill']['total']}} {{__('SAR') }}
                                        @else
                                            ---
                                        @endif
                                    @endif
                                </td>

                                {{-- Date created --}}
                                <td>{{$log->created_at}}</td>

                                {{-- Status --}}
                                <td>
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
        </div><!-- card-body -->
    </div><!-- card -->
</div><!-- col-12 -->