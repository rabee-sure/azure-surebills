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
            <span>Orjwan hotel</span>
            <div>
              <p>Riyadh, Saudi Arabia</p>
              <b>0551234567</b>
            </div>
          </div><!-- title -->
          <div class="date_time">
            <span>Due on Tuseday, 2020/04/05</span>
            <div>
              <p>Bill # : 201</p>
              <b>2020/04/05</b>
            </div>
          </div><!-- date_time -->
          <div class="shopping_cart">
            <div class="name">Shopping Cart</div>
            <div class="details_pay">
              <div class="info">
                <p>Reservation #2154</p>
                <p>2 Night</p>
                <p>From : <time>Tuesday, 16/02/2020</time></p>
                <p>To : <time>Friday, 19/02/2020</time></p>
              </div><!-- info -->
              <span>144.32 SAR</span>
            </div><!-- details_pay -->
          </div><!-- shopping_cart -->
          <div class="total_bill">
            <p>Subtotal : 61.82 SAR</p>
            <p>Tax : 2.18 SAR</p>
            <b>Total : 68.14 SAR</b>
          </div><!-- total_bill -->
          <div class="customer_information">
            <div class="name">Customer Information</div>
            <p>Billed to, Saad Ahmed</p>
            <p>+966551231231</p>
            <p>sahmed@gmail.com</p>
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
                    <form>
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
