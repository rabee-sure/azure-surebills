@extends('layouts.bill')

@section('title', 'Page Title')

@section('content')
  <div class="single_bill_page">
    <div class="container">
      <div class="row  justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-6">
          <div class="logo">
            <img src="https://www.sure.com.sa/wp-content/uploads/2019/10/21.png" alt="logo">
          </div><!-- logo -->
          <div class="title">
            <span>{{ $bill->business_name}}</span>
            <div>
              <p>Riyadh, Saudi Arabia</p>
              <b>0551234567</b>
            </div>
          </div><!-- title -->
          <div class="date_time">
            <span>Due on {{ $bill->due_date->format('M d Y')}}</span>
            <div>
              <p>Bill # : {{ $bill->number}}</p>
              <b>2020/04/05</b>
            </div>
          </div><!-- date_time -->
          <div class="shopping_cart">
            <div class="name">Shopping Cart</div>
            @foreach($bill->items as $item)
              <div class="details_pay">
                <div class="info">
                  <p>{{ $item->product_name }}</p>

                  <p>price : <time>{{ $item->product_price  }}</time></p>
                  <p>quantity : <time>{{ $item->quantity  }}</time></p>
                </div><!-- info -->
                <span>{{ $item->total }}</span>
              </div><!-- details_pay -->
            @endforeach
          </div><!-- shopping_cart -->
          <div class="total_bill">
            @if( $bill->add_tax && $bill->add_discount)
              <p>Subtotal : {{ $bill->sub_total }} SAR</p>
            @endif
            @if( $bill->add_discount)
              <p>Discount : {{ $bill->discount }} SAR</p>
              <p>Subtotal - Discount : {{ $bill->sub_total- $bill->discount }} SAR</p>
            @endif
            @if( $bill->add_tax)
              <p>Tax : {{ $bill->vat }} SAR</p>
            @endif
            <b>Total : {{ $bill->total}} SAR</b>
          </div><!-- total_bill -->
          <div class="customer_information">
            <div class="name">Customer Information</div>
            <p>Billed to, {{ $bill->customer_name}}</p>
            <p>+966{{ $bill->customer_mobile}}</p>
            <p>{{ $bill->customer_email}}</p>
          </div><!-- customer_information -->
          <div class="payment_method">
            <div class="name">Payment Method</div>
            <div class="bill_payment">
              <div class="item">
                <input type="radio" id="visa_pay" name="pay">
                <label for="visa_pay">
                  <p>Credit Card - Made</p>
                  <div class="icon_mada"></div>
                  <div class="checkmark"></div>
                </label>
                <div class="visa_pay_content d-none">
                  <div class='card-wrapper'></div>
                  <div class="form_card">
                    <form method="POST" action="{{ route('bills.bay', ['id' => $id]) }}" class="repeater" id="bill_bay">
                        @csrf
                      <p><input type="text" name="number" placeholder="Card Number" /></p>
                      <p><input type="text" name="name" placeholder="Full Name" /></p>
                      <span><input type="text" name="expiry" placeholder="MM/YY" /></span>
                      <span><input type="text" name="cvc" placeholder="CVC" /></span>
                      <span><input type="submit" name="pay" value="Pay" /></span>
                    </form>
                  </div><!-- form_card -->
                </div><!-- visa_pay_content -->
              </div><!-- item -->
              <div class="item">
                <input type="radio" id="pay_2" name="pay">
                <label for="pay_2">
                  <p>Apple Pay</p>
                  <div class="icon_apple"></div>
                  <div class="checkmark"></div>
                </label>
              </div><!-- item -->
              <div class="item">
                <input type="radio" id="pay_3" name="pay">
                <label for="pay_3">
                  <p>STC Pay</p>
                  <div class="icon_stc"></div>
                  <div class="checkmark"></div>
                </label>
              </div><!-- item -->
            </div><!-- bill_payment -->
          </div><!-- payment_method -->
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- single_bill_page -->
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endsection
