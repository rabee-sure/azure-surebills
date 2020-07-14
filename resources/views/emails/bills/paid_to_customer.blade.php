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
          margin-bottom: 10px;
        }
        .logo img {
          max-width: 100%;
          margin: 0 auto;
          display: block;
          max-height: 100px;
        }
        .block_1 {
          display: block;
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
          text-align: center;
        }
        .block_1 span {
          display: block;
          font-weight: bold;
          font-size: 18px;
          text-transform: capitalize;
          margin: 0 auto 9px;
        }
        .block_1 p {
          display: block;
          margin: 0 auto 5px;
          font-size: 13px;
          line-height: 1.1;
          color: #444;
        }
        .block_1 small {
          display: block;
          margin: 0 auto;
          font-size: 13px;
          color: #444;
          font-weight: normal;
        }
        .alert {
          text-align: center;
          font-weight: bold;
          font-size: 14px;
          text-transform: capitalize;
          margin: 0 auto 30px;
          background: #d7f3e3;
          color: #1d643b;
          border: 1px solid #c7eed8;
          display: table;
          max-width: 100%;
          padding: 15px;
          border-radius: 4px;
          box-sizing: border-box;
          min-width: 50%;
        } /* alert */
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
          font-size: 14px;
          color: #444444;
        }
        .block_2 span .vat_reg {
          display: block;
          font-weight: normal;
          font-size: 14px;
          color: #444;
          margin: 5px auto 0;
        } /* vat_reg */
        .block_2 p {
          display: block;
          font-size: 14px;
          margin: 0 auto 2px;
          color: #333;
          text-align: right;
        }
        .block_2 small {
          display: block;
          font-size: 14px;
          margin: 0;
          color: #333;
          text-align: right;
        }
        .block_3 {
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
        }
        .block_3 .details_pay {
          display: flex;
          align-items: center;
          justify-content: space-between;
          margin: 0 auto 10px;
          color: #000;
        }
        .block_3 .details_pay:last-child {
          margin: 0;
        }
        .block_3 .details_pay p {
          display: block;
          margin: 0;
          min-width: 60%;
          color: #000;
          font-size: 14px;
        }
        .block_3 .details_pay b {
          display: block;
          margin: 0;
          font-size: 14px;
          color: #222;
          font-weight: normal;
          min-width: 20%;
          text-align: center;
        }
        .block_3 .details_pay b:last-child {
          text-align: right;
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
        .customer_notes {
          margin: 0 0 15px;
          padding: 0 0 15px;
          text-align: center;
          font-size: 16px;
          text-transform: capitalize;
          color: #000000;
        } /* customer_notes */
        .block_4 {
          margin: 0 auto 20px;
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
        @if($bill->user->logo)
          <div class="logo">
            <img src="{{ url($bill->user->logo) }}" alt="logo">
          </div><!-- logo -->
        @endif
        <div class="block_1">
          <span> {{ $bill->business_name}}</span>
          <p>{{ $bill->user->business_address }}</p>
          <small>{{  $bill->user->business_mobile }}</small>
        </div><!-- block_1 -->
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
        <div class="block_2">
          <span>
            {{ __('Due On') }} {{ $bill->due_date->format('M d Y')}}
            @if($bill->user->vat_registration_number)
              <div class="vat_reg">{{ __('VAT Registration Number') }} : {{ $bill->user->vat_registration_number }}</div>
            @endif
          </span>
          <div>
            <p>{{ __('Bill') }} # : {{ $bill->number}}</p>
            <small>{{ $bill->created_at->format('Y-m-d') }}</small>
          </div>
        </div><!-- block_2 -->
        <div class="block_3">
          @foreach($bill->items as $item)
            <div class="details_pay">
              <p>{{ $item->product_name }}</p>
              <b>X {{ $item->quantity  }}</b>
              <b>{{ $item->product_price  }} SAR</b>
            </div><!-- details_pay -->
          @endforeach
        </div><!-- block_3 -->
        <div class="total_area">
            @if( $bill->add_tax && $bill->add_discount)
              <p>{{ __('Subtotal') }} : {{ $bill->sub_total }} SAR</p>
            @endif
            @if( $bill->add_discount)
              <p>{{ __('Discount') }} : {{ $bill->discount }} SAR</p>
              <p>{{ __('Subtotal - Discount') }} : {{ $bill->sub_total- $bill->discount }} SAR</p>
            @endif
            @if( $bill->add_tax)
              <p>{{ __('Tax') }} : {{ $bill->vat }} SAR</p>
            @endif
            <b>{{ __('Total') }} : {{ $bill->total}} SAR</b>
        </div><!-- total_area -->
        @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
        <div class="block_4">
          <p>{{ __('Billed to,') }} {{ $bill->customer_name }}</p>
          <p>+966{{ $bill->customer_mobile }}</p>
          <p>{{ $bill->customer_email }}</p>
        </div><!-- block_4 -->
      </div><!-- mail_content -->
      <div class="copyrights">
        © 2020 SureBills. All rights reserved
      </div><!-- copyrights -->
    </div><!-- mail_wrapper -->
  </body>
</html>