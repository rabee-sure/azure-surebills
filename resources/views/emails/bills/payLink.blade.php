<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
      @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;400;700&display=swap');
      body {
        padding: 0;
        margin: 0;
      }
      @media screen {
        @import url('https://fonts.googleapis.com/css2?family=Nunito:wght@200;400;700&display=swap');
        #mail_wrapper {
          background: #edf2f8;
          padding: 10px;
          font-family: "Nunito";
        }
        .mail_content {
          min-width: 50%;
          margin: 0 auto;
          background: #fff;
          padding: 20px;
          border-radius: 5px;
          border: 1px solid #ddd;
          max-width: 100%;
        }
        .logo {
          margin-bottom: 50px;
        }
        .logo img {
          max-width: 100%;
          max-height: 80px;
        }
        .block_1 {
          display: flex;
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
          align-items: center;
          justify-content: space-between;
        }
        .block_1 span {
          display: block;
          font-size: 20px;
          font-weight: bold;
          color: #000000;
        }
        .block_1 p {
          display: block;
          font-size: 14px;
          margin: 0 auto 2px;
          color: #333;
        }
        .block_1 small {
          display: block;
          font-size: 14px;
          margin: 0;
          color: #333;
        }
        .block_2 {
          display: flex;
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
          align-items: center;
          justify-content: space-between;
        }
        .block_2 span {
          display: block;
          font-size: 15px;
          font-weight: bold;
          color: #000000;
        }
        .block_2 p {
          display: block;
          font-size: 14px;
          margin: 0 auto 2px;
          color: #333;
        }
        .block_2 small {
          display: block;
          font-size: 14px;
          margin: 0;
          color: #333;
        }
        .block_3 {
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
        }
        .block_3 .title {
          display: block;
          font-size: 20px;
          font-weight: bold;
          margin: 0 auto 30px;
          color: #000;
        }
        .block_3 .cart_details {
          display: flex;
          align-items: flex-start;
          justify-content: space-between;
        }
        .block_3 .cart_details .info p {
          display: block;
          font-size: 14px;
          margin: 0 auto 5px;
          color: #000;
        }
        .block_3 .cart_details .price {
          display: block;
          font-size: 15px;
          color: #000;
        }
        .total_area {
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
          text-align: right;
        }
        .total_area p {
          display: block;
          margin: 0 auto 8px;
          font-size: 15px;
          color: #000;
        }
        .total_area b {
          display: block;
          margin: 0;
          font-size: 15px;
          color: #000;
        }
        .block_4 {
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
        }
        .block_4 .title {
          display: block;
          font-size: 20px;
          font-weight: bold;
          margin: 0 auto 30px;
          color: #000;
        }
        .block_4 p {
          display: block;
          font-size: 15px;
          text-align: center;
          margin: 0 auto 6px;
        }
        .block_4 p:last-child {margin: 0;}
        .pay_button {
          display: flex;
          align-items: center;
          justify-content: center;
          padding: 10px 0;
        }
        .pay_button a {
          display: block;
          border-radius: 100px;
          background: #00D595;
          padding: 0 30px;
          min-width: 150px;
          height: 40px;
          line-height: 40px;
          text-align: center;
          color: #fff;
          font-weight: bold;
          font-size: 18px;
          text-decoration: none;
        }
        .pay_button a:hover {
          background: #02c288;
        }
        .copyrights {
          display: block;
          text-align: center;
          font-size: 14px;
          margin: 20px auto;
          color: #999;
        }
      }
    </style>
    <!--[if mso]>
    <style>
      table { border-collapse: collapse; }
      .o_col { float: left; }
    </style>
    <xml>
      <o:OfficeDocumentSettings>
        <o:PixelsPerInch>96</o:PixelsPerInch>
      </o:OfficeDocumentSettings>
    </xml>
    <![endif]-->
  </head>
  <body>
    <div id="mail_wrapper">
      <div class="mail_content">
        <div class="logo">
          <img src="https://sure-bills.sure-lab.com/images/logo-black.svg" alt="#">
        </div><!-- logo -->
        <div class="block_1">
          <span> {{ $bill->business_name}}</span>
          <div>
            <p>Riadh, Saudi Arabia</p>
            <small>0551234567</small>
          </div>
        </div><!-- block_1 -->
        <div class="block_2">
          <span>Due On {{ $bill->due_date->format('M d Y')}}</span>
          <div>
            <p>Bill # : {{ $bill->number}}</p>
            <small>{{ $bill->created_at->format('Y-m-d') }}</small>
          </div>
        </div><!-- block_2 -->
        <div class="block_3">
          <div class="title">Shopping Cart</div>
		        @foreach($bill->items as $item)
		          <div class="cart_details">
		            <div class="info">
		              <p>{{ $item->product_name }}</p>
		              <p>price : {{ $item->product_price  }}</p>
		              <p>quantity : {{ $item->quantity  }}</p>
		            </div><!-- info -->
		            <div class="price">{{ $item->total }} SAR</div>
		          </div><!-- cart_details -->
		        @endforeach
        </div><!-- block_3 -->
        <div class="total_area">
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
        </div><!-- total_area -->
        <div class="block_4">
          <div class="title">Customer Information</div>
          <p>Billed to, {{ $bill->customer_name }}</p>
          <p>+966{{ $bill->customer_mobile }}</p>
          <p>{{ $bill->customer_email }}</p>
        </div><!-- block_4 -->
        <div class="pay_button">
          <a href="{{ $bill->pay_url }}" target="_blank" title="PAY">Pay</a>
        </div><!-- pay_button -->
      </div><!-- mail_content -->
      <div class="copyrights">
        © 2020 SureBills. All rights reserved
      </div><!-- copyrights -->
    </div><!-- mail_wrapper -->
  </body>
</html>