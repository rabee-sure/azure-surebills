@extends('layouts.bill')

@section('title', 'Page Title')

@section('content')
  <div class="single_bill_page">
    <div class="container">
      <div class="row  justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-6">
          <div class="single_bill_content">
            @if($bill->user->logo)
              <div class="logo">
                <img src="{{ url($bill->user->logo) }}" alt="logo">
              </div><!-- logo -->
            @endif
            <div class="title">
              <span>{{ $bill->business_name }}</span>
              <p>{{  $bill->user->business_address }}</p>
              <b>{{  $bill->user->business_mobile }}</b>
            </div><!-- title -->
            @if($bill->status == 'expired')
              <div class="alert alert-secondary" role="alert">
                this bill #{{ $bill->number }} has been expired
              </div>
            @endif
            @if($bill->status == 'paid')
              <div class="alert alert-success" role="alert">
                this bill #{{ $bill->number }} paid successfully
              </div>
            @endif
            @if($bill->status == 'canceled')
              <div class="alert alert-danger" role="alert">
                this bill #{{ $bill->number }} has been canceled
              </div>
            @endif
            <div class="date_time">
              <span>
                {{__('Due on')}} {{ $bill->due_date->format('M d Y')}}
                @if($bill->user->vat_registration_number)
                  <div class="vat_reg"> {{ __('VAT Registration Number') }} : {{ $bill->user->vat_registration_number }}</div>
                @endif
              </span>
              <div>
                <p>{{ __('Bill') }} #{{ $bill->number }}</p>
                <b>{{ $bill->created_at->format('Y/m/d')}}</b>
              </div>
            </div><!-- date_time -->
            <div class="shopping_cart">
              @foreach($bill->items as $item)
                <div class="details_pay">
                  <p>{{ $item->product_name }}</p>
                  <b>X {{ $item->quantity  }}</b>
                  <b>{{ $item->product_price  }} {{ __('SAR') }}</b>
                </div><!-- details_pay -->
              @endforeach
            </div><!-- shopping_cart -->
            <div class="total_bill">
              @if( $bill->add_tax && $bill->add_discount)
                <p>{{ __('Subtotal') }} : {{ $bill->sub_total }} {{ __('SAR') }}</p>
              @endif
              @if( $bill->add_discount)
                @if($bill->discount_type == 'percentage')
                  <p>{{ __('Discount') }} ({{ $bill->discount_value }}%) : {{ $bill->discount }} {{ __('SAR') }}</p>
                @else
                  <p>{{ __('Discount') }}  ({{ $bill->discount_value }} {{ __('SAR') }}) : {{ $bill->discount }} {{ __('SAR') }}</p>
                @endif
                <p>{{ __('Subtotal - Discount') }} : {{ $bill->sub_total- $bill->discount }} {{ __('SAR') }}</p>
              @endif
              @if( $bill->add_tax)
                <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }} {{ __('SAR') }}</p>
              @endif
              <b>{{ __('Total') }} : {{ $bill->total}} {{ __('SAR') }}</b>
            </div><!-- total_bill -->
            @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
            <div class="customer_information">
              <!-- <div class="name">Customer Information</div> -->
              <p>{{ __('Billed to,') }} {{ $bill->customer_name}}</p>
              <p>+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>
            </div><!-- customer_information -->
          </div><!-- single_bill_content -->
          <a href="https://bills.surepay.sa" target="_blank" title="Sure Bills" class="logo_bills"></a>
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- single_bill_page -->
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endsection
