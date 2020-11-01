 @extends('layouts.app')

@section('title', __('Bills'))


@section('content')
  <div class="row">
    <div class="col-12">
      <h1>{{ __('Bills') }}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item"><a href="/bills" title="{{__('Bills')}}">{{__('Bills')}}</a></li>
          <li class="breadcrumb-item"><a href="/bills/{{ $bill->id }}" title="{{__('Bills')}}">{{__('Bill')}} #{{ $bill->number }}</a></li>
          <li class="breadcrumb-item active" aria-current="page">
            {{ isset($log->results['response']) ? $log->results['response']['id'] : null }}
          </li>
        </ol>
      </nav>
      <div class="separator mb-5"></div>
    </div>
  </div>
  <div class="row">
    <div class="col-12">
      <div class="card mb-5">
        <div class="card-body">
          <div class="payment_block">
            <div class="title">
                  @if(isset($log->results['response']) && $log->results['response']['paymentBrand'] == 'MADA')
                    <img src="{{ asset('/payments/mada.png') }}" alt="mada">
                  @elseif(isset($log->results['response']) && $log->results['response']['paymentBrand'] == 'VISA')
                    <img src="{{ asset('/payments/visa.png') }}" alt="visa">
                  @elseif(isset($log->results['response']) && $log->results['response']['paymentBrand'] == 'MASTERCARD')
                    <img src="{{ asset('/payments/card.png') }}" alt="mastercard">
                  @elseif(isset($log->results['response']) && $log->results['response']['paymentBrand'] == 'APPLEPAY')
                    <img src="{{ asset('/payments/pay.png') }}" alt="apple pay">
                  @endif
              <p>{{ $bill->total}} {{__('SAR') }}</p>
              @if($log->status == true)
                <span class="badge badge-pill badge-success ">{{ __('Paid') }}</span>
              @else
                <span class="badge badge-pill badge-danger ">{{ __('Failed') }}</span>
              @endif
            </div><!-- title -->
            <div class="desc">{{__('ID') }} : {{ isset($log->results['response']) ? $log->results['response']['id'] : null }}</div>
            <div class="separator mb-5"></div>
            <div class="table_block mb-5">
              <div class="name"><div class="glyph-icon iconsminds-dollar"></div> {{__('Payment Details') }}</div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <tbody>
                    <tr>
                      <td>{{__('Amount') }}</td>
                      <td>{{ $bill->total}} {{__('SAR') }}</td>
                    </tr>
                    <tr>
                      <td>{{__('Descrption') }}</td>
                      <td>{{$bill->customer_notes}}</td>
                    </tr>
                    <tr>
                      <td>{{__('Date created') }}</td>
                      <td>{{ $log->created_at}}</td>
                    </tr>
                    <tr>
                      <td>{{__('Last Update') }}</td>
                      <td>{{ $log->updated_at}}</td>
                    </tr>
                  </tbody>
                </table>
              </div><!-- table-responsive -->
            </div><!-- table_block -->
            <div class="table_block">
              <div class="name"><div class="glyph-icon simple-icon-credit-card"></div> {{__('Payment Method') }}</div>
              <div class="table-responsive">
                <table class="table table-striped table-bordered">
                  <tbody>
                    @if(isset($log->results['response']) && isset($log->results['response']['card']) && isset($log->results['response']['card']['holder']))
                    <tr >
                      <td>{{__('Name On Card') }}</td>
                      <td>{{  $log->results['response']['card']['holder'] }}</td>
                    </tr>
                    @endif
                    @if(isset($log->results['response']))
                    <tr>
                      <td>{{__('Card Type') }}</td>
                      <td>{{ isset($log->results['response']['paymentBrand']) ? $log->results['response']['paymentBrand'] : null }}</td>
                    </tr>
                    @endif
                    @if(isset($log->results['response']))
                    <tr>
                      <td>{{__('Card Number') }}</td>
                      <td>xxxx-xxxx-xxxx-{{ isset($log->results['response']['card']) ? $log->results['response']['card']['last4Digits'] : null }}</td>
                    </tr>

                    @endif
                    @if(isset($log->results['description']))
                    <tr>
                      <td>{{ __('Message') }}</td>
                      <td>{{ $log->results['description'] }}</td>
                    </tr>
                    @endif
                  </tbody>
                </table>
              </div><!-- table-responsive -->
            </div><!-- table_block -->
          </div><!-- payment_block -->
        </div>
      </div>
    </div>
  </div>
@endsection
