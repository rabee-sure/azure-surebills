@extends('layouts.bill')

@section('title', 'Page Title')

@section('content')

<div class="single_bill_page">
  <div class="container"  id="app">
    <div class="row  justify-content-center">
      <div class="col-12 col-md-8 col-lg-6 col-xl-6">
        <div class="single_bill_content">
          <div class="change-lang">
            @if(App::isLocale('en'))
              <a href="{{ $bill->pay_url }}/ar" title="عربي">عربي</a>
            @else
              <a href="{{ $bill->pay_url }}/en" title="English">English</a>
            @endif
          </div>
          @if($bill->user->logo)
            <div class="logo">
              <img src="{{ url($bill->user->logo) }}" alt="logo">
            </div><!-- logo -->
          @endif
          <div class="title">
            <span>{{ $bill->business_name }}</span>
            <p>{{  $bill->user->business_address}}</p>
            <b>{{  $bill->user->business_mobile }}</b>
          </div><!-- title -->
          @if($errors->any())
            <div class="alert alert-danger" role="alert">
              {{$errors->first()}}
            </div>
          @endif
            <div class="date_time">
              <span>
                {{__('Due on')}} {{ $bill->due_date->format('M d Y')}}
                @if($bill->user->vat_registration_number)
                  <div class="vat_reg"> {{ __('VAT Registration Number') }}: {{ $bill->user->vat_registration_number }}</div>
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
                  <b>{{ $item->product_price  }}  {{ __('SAR') }}</b>
                </div><!-- details_pay -->
              @endforeach
            </div><!-- shopping_cart -->
            <div class="total_bill">
              @if( $bill->add_tax && $bill->add_discount)
                <p>{{ __('Subtotal') }} : {{ $bill->sub_total }} {{ __('SAR') }}</p>
              @endif
              @if( $bill->add_discount)
                @if($bill->discount_type == 'percentage')
                  <p>{{ __('Discount') }} ({{ $bill->discount_value }}%) : {{ $bill->discount }}  {{ __('SAR') }}</p>
                @else
                  <p>{{ __('Discount') }} ({{ $bill->discount_value }}  {{ __('SAR') }}) : {{ $bill->discount }}  {{ __('SAR') }}</p>
                @endif
                <p>{{ __('Subtotal - Discount') }}: {{ $bill->sub_total- $bill->discount }}  {{ __('SAR') }}</p>
              @endif
              @if( $bill->add_tax)
                <p>{{ __('Vat') }} ({{ $bill->tax_value }}%) : {{ $bill->vat }}  {{ __('SAR') }}</p>
              @endif
              <b>{{ __('Total') }} : {{ $bill->total}}  {{ __('SAR') }}</b>
            </div><!-- total_bill -->
            @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
            <div class="customer_information">
              <!-- <div class="name">Customer Information</div> -->
              <p>{{ __('Billed to,') }} {{ $bill->customer_name}}</p>
              <p>+966{{ $bill->customer_mobile}}</p>
              <p>{{ $bill->customer_email}}</p>
            </div><!-- customer_information -->
            <div class="payment_method">
              <div class="name">{{__('Payment Method')}}</div>
    <div class="bill_payment">
        <div class="item">
            <input type="radio" id="visa_pay" name="payment_method" value="visa_pay" >
            <label for="visa_pay">
            <p>Credit Card - made</p>
            <div class="icon_mada"></div>
            <div class="checkmark"></div>
            </label>
            <div class="visa_pay_content" id="visa_pay_content">

            </div><!-- visa_pay_content -->
        </div><!-- item -->
        <div class="item disable">
            <input type="radio" id="apple_pay" name="payment_method" value="apple_pay" >
            <label for="pay_2">
            <p>Apple Pay</p>
            <div class="icon_apple"></div>
            <div class="checkmark"></div>
            </label>
        </div><!-- item -->
        <div class="item disable">
            <input type="radio" id="stc_pay" name="payment_method" value="stc_pay" >
            <label for="pay_3">
                <p>STC Pay</p>
                <div class="icon_stc"></div>
                <div class="checkmark"></div>
            </label>
        </div><!-- item -->
    </div><!-- bill_payment -->
            </div><!-- payment_method -->
          </div><!-- single_bill_content -->
          <a href="https://bills.surepay.sa" target="_blank" title="Sure Bills" class="logo_bills"></a>
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- single_bill_page -->
@endsection


@section('footer-scripts')
<script type='text/javascript'>

jQuery(document).ready(function(){

$('input:radio[name="payment_method"]').change(
    function(){
        if (this.checked && this.value == 'visa_pay') {
          console.log('sss');
$.ajax({
    type: 'GET', //THIS NEEDS TO BE GET
    url: '/bills/payment_iframe/{{$bill->id}}',
    success: function (data) {
         $("#visa_pay_content").append(data); //// For Append
    },
    error: function() { 
         console.log(data);
    }
});

        }
    });

  });


</script>
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endsection
