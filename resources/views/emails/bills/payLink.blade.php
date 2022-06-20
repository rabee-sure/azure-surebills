<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
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
          display: flex;
          align-items: center;
          justify-content: center;
        }
        .logo img {
          max-width: 100%;
          margin: 0 auto;
          display: block;
          max-height: 100px;
        }
        .block_1 {
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
        } /* alert */
        .block_2 {
          display: block;
          margin: 0 auto 20px;
          padding: 0 0 20px;
          border-bottom: 1px solid #ddd;
        }
        .billInfoItem {
          display: block;
          clear: both;
          margin-bottom: 5px;
          height: 20px;
        } /* billInfoItem */
        .block_2 .billInfoItem span {
          display: block;
          float: left;
          font-size: 14px;
          color: #444444;
        }
        .block_2 .billInfoItem p {
          display: block;
          float: right;
          font-size: 14px;
          margin: 0;
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
        .block_3 table {
          width: 100%;
          caption-side: bottom;
          border-collapse: collapse;
        }
        .block_3 table th {
          text-align: center;
          padding: 5px;
          vertical-align: middle;
          font-size: 13px;
        }
        .block_3 table th:first-child {
          text-align: right;
        }
        .block_3 table th:last-child {
          text-align: left;
        }
        .block_3 table td {
          text-align: center;
          padding: 5px;
          vertical-align: middle;
          font-size: 13px;
        }
        .block_3 table td:first-child {
          text-align: right;
        }
        .block_3 table td:last-child {
          text-align: left;
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
        .total_area .totalAreaItem {
          display: block;
          clear: both;
          margin-bottom: 5px;
          height: 20px;
        }
        .total_area .totalAreaItem span {
          display: block;
          float: left;
          font-size: 14px;
          color: #444444;
        }
        .total_area .totalAreaItem p {
          display: block;
          float: right;
          font-size: 14px;
          margin: 0;
          color: #444444;
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
          padding: 10px 0;
          display: block;
          text-align: center;
        }
        .pay_button a {
          border-radius: 100px;
          background: #00d595;
          padding: 0 30px;
          height: 40px;
          line-height: 40px;
          text-align: center;
          color: #fff;
          font-weight: bold;
          font-size: 18px;
          text-decoration: none;
          max-width: 100%;
          min-width: 200px;
          display: table;
          margin: 0 auto;
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
        @if($bill->user->logo)
          <div class="logo">
            <img src="{{ $bill->user->logo_url }}" alt="logo">
          </div><!-- logo -->
        @endif
        <div class="block_1">
          @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
            <div class="taxInvoiceText">{{ __('Simplified Tax Invoice') }}</div>
          @endif
          <div class="block1Item">
            <span> {{ $bill->user->business_name}}</span>
            @if(isset($bill->user->settings->header_bill))
              <p>{{ $bill->user->settings->header_bill }}</p>
            @endif
          </div><!-- block1Item -->
          <div class="block1Item">
          <span>{{ $bill->user->business_address }}</span>
          <small>{{  $bill->user->business_mobile }}</small>
          </div><!-- block1Item -->
          @if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
            <div class="countdown alert alert-warning" id="new_countdown">
              <p class="mb-0">{{ __('the bill will expire in')}}</p>
              <span id="hm_timer"></span>
            </div><!-- countdown -->
          @endif
        </div><!-- block_1 -->
        <div class="block_2">
          @if($bill->user->settings->add_tax_invoice)
            <div class="billInfoItem">
              <span>{{ __('Bill No.') }}</span>
              <p>{{ $bill->number }}</p>
            </div><!-- d-flex -->
            <div class="billInfoItem">
              <span>{{ __('Date') }}</span>
              <p>{{ $bill->created_at->format('d/m/Y')}}</p>
            </div><!-- d-flex -->
            @if($bill->user->vat_registration_number)
              <div class="billInfoItem">
                <span>{{ __('Organization VAT Registration Number') }}</span>
                <p>{{ $bill->user->vat_registration_number }}</p>
              </div><!-- d-flex -->
            @endif
          @else
          <div class="billInfoItem">
            <span>{{ __('No.') }}</span>
            <p>{{ $bill->number }}</p>
          </div><!-- d-flex -->
          <div class="billInfoItem">
            <span>{{ __('Date') }}</span>
            <p>{{ $bill->created_at->format('d/m/Y')}}</p>
          </div><!-- d-flex -->
          @endif
          @if($bill->user->settings->display_customer_details)
            <div class="billInfoItem">
              <span>{{ __('Customer Name') }}</span>
              <p>{{ $bill->customer->name }}</p>
            </div><!-- d-flex -->
            <div class="billInfoItem">
              <span>{{ __('Mobile Number') }}</span>
              <p>{{ $bill->customer->mobile }}</p>
            </div><!-- d-flex -->
          @endif
        </div><!-- block_2 -->

        <div class="block_3">
          <table>
            <thead>
              <tr>
                <th>{{ __('Description') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                @if($bill->add_tax)
                <th>{{ __('Total include added tax') }}</th>
                @else
                <th>{{ __('Total') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($bill->items as $item)
              <tr>
                <td>{!! $item->product_name !!}</td>
                <td>{{ $item->product_price  }}</td>
                <td>{{ $item->quantity  }}</td>
                @if( $bill->add_tax)
                <td>{{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }}</td>
                @else
                <td>{{ $item->product_price * $item->quantity }}</td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- block_3 -->
        
        <div class="total_area">
          @if( $bill->add_tax || $bill->add_discount)
            <div class="totalAreaItem">
              <div>
                <span>{{ __('Total amount') }} ({{ __('SAR') }})</span>
                @if( $bill->add_tax)
                <small>( {{ __('Exclude added tax') }} )</small>
                @endif
              </div>
              <p>{{ $bill->sub_total }}</p>
            </div><!-- totalAreaItem -->
          @endif
          @if( $bill->add_discount)
          <div class="totalAreaItem">
            <span>{{ __('Discount amount') }} ({{ __('SAR') }})</span>
            <p>{{ $bill->discount }}</p>
          </div><!-- totalAreaItem -->
          @endif
          @if( $bill->user->pay_fees == 'client')
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('payment fees') }} ({{ __('SAR') }})</span>
              <p>{{ $bill->payment_fees }}</p>
            </div><!-- totalAreaItem -->
          @endif
          @if( $bill->add_tax)
            <div class="totalAreaItem">
              <span>{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value]) }}</span>
              <p>{{ $bill->vat }}</p>
            </div><!-- totalAreaItem -->
          @endif
          @if( $bill->channel_extra_amount)
            <div class="totalAreaItem">
              <span>{{$bill->channel_extra_title}} ({{ __('SAR') }})</span>
              <p>{{ $bill->channel_extra_amount }}</p>
            </div><!-- totalAreaItem -->
          @endif
          @if( $bill->channel_extra_vat)
            <div class="totalAreaItem">
              <span>{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</span>
              <p>{{ $bill->channel_extra_vat }}</p>
            </div><!-- totalAreaItem -->
          @endif
          <div class="totalAreaItem">
            <span>{{ __('Total amount') }} ({{ __('SAR') }})</span>
            <p>{{ $bill->total}}</p>
          </div><!-- totalAreaItem -->
        </div><!-- total_area -->
        
        @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
        
        <div class="block_4">
          <!-- <div class="title">Customer Information</div> -->
          <p>{{ $bill->customer_name }}</p>
          <p>+966{{ $bill->customer_mobile }}</p>
          <p>{{ $bill->customer_email }}</p>

          @if($bill->user->settings->add_tax_invoice)
            <div class="qrCode_area">
              <a class="d-flex justify-content-center flex-column align-items-center" target="_blank" href="{{route('invoice', ['id' => $bill->pay_id])}}">
                {!! generateQRcode($bill) !!}
                <!-- <p>تم إنشاء كود الاستجابة السريعة بواسطة حل الفوترة الإلكترونية لدافعي الضرائب وفقاً لمواصفات ZATCA.</p> -->
                <span>{{ __('Tax Invoice') }}</span>
              </a>
            </div><!-- qrCode_area -->
          @endif
          
          @if(isset($bill->user->settings->footer_bill))
            <p>{{ $bill->user->settings->footer_bill }}</p>
          @endif
        </div><!-- block_4 -->
        <div class="pay_button">
          <a href="{{ $bill->pay_url }}" target="_blank" title="PAY">{{ __('Pay') }}</a>
        </div><!-- pay_button -->
      </div><!-- mail_content -->
      <div class="copyrights">
        © 2020 SureBills. All rights reserved
      </div><!-- copyrights -->
    </div><!-- mail_wrapper -->
  </body>
</html>
