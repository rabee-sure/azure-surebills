<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <style type="text/css">
      @media screen {
        .billMailWrapper {
          font-family: 'tahoma', 'verdana';
          width: 500px;
          max-width: calc(100% - 15px);
          margin: 15px auto;
        }
        .billMailWrapper .mailContent {
          background-color: #fff;
          border: 2px solid #aaa;
          border-radius: 10px;
          color: #000;
          padding: 15px;
        }
        .billMailWrapper .mailContent .clientLogo {
          text-align: center;
          margin-bottom: 15px;
        }
        .billMailWrapper .mailContent .clientLogo img {
          max-width: 100%;
          max-height: 50px;
          width: auto;
          height: auto;
        }
        .billMailWrapper .mailContent .taxInvoiceText {
          text-align: center;
          color: #6c757d;
        }
        .billMailWrapper .mailContent .clientMail {
          margin-bottom: 5px;
          color: #000000;
          font-weight: bold;
          text-align: center;
          font-size: 16px;
        }
        .billMailWrapper .mailContent .clientMail a {
          color: #000000;
          text-decoration: none;
        }
        .billMailWrapper .mailContent .headerBillText {
          text-align: center;
        }
        .billMailWrapper .mailContent .businessAddress {
          text-align: center; 
        }
        .billMailWrapper .mailContent .businessMobile {
          text-align: center;
        }
        .billMailWrapper .mailContent .billWillExpire {
          background-color: #fff3cd;
          color: #664d03;
          margin: 15px 0;
          text-align: center;
          padding: 10px;
          border-radius: 8px;
        }
        .billMailWrapper .mailContent .billInfo {
          border-top: 1px dashed #dee2e6;
          padding-top: 0.5rem;
          margin-top: 0.5rem;
        }
        .billMailWrapper .mailContent .billInfo .item {
          display: block;
          margin-bottom: 0.5rem;
        }
        .billMailWrapper .mailContent .billInfo .item p {
          margin: 0;
        }
        .billMailWrapper .mailContent .clearfix {
          clear: both;
        }
        .billMailWrapper .mailContent .blockTable {
          border-top: 1px dashed #dee2e6;
          padding-top: 0.5rem;
          margin-top: 0.5rem;
        }
        .billMailWrapper .mailContent .blockTable table {
          width: 100%;
          border-collapse: collapse;
        }
        .billMailWrapper .mailContent .blockTable table th {
          text-align: center;
          padding: 0.25rem;
          font-weight: bold;
          vertical-align: middle;
        }
        .billMailWrapper .mailContent .blockTable table td {
          text-align: center;
          padding: 0.25rem;
          vertical-align: middle;
        }
        .billMailWrapper .mailContent .totalArea {
          border-top: 1px dashed #dee2e6;
          padding-top: 0.5rem;
          margin-top: 0.5rem;
        }
        .billMailWrapper .mailContent .totalArea .item {
          display: block;
          margin-bottom: 0.5rem;
        }
        .billMailWrapper .mailContent .totalArea .item.Discount {
          color: #00d595;
        }
        .billMailWrapper .mailContent .totalArea .item.Discount small {
          color: #00d595;
        }
        .billMailWrapper .mailContent .totalArea .item.Total {
          font-weight: bold;
          font-size: 14px;
        }
        .billMailWrapper .mailContent .totalArea .item span small {
          color: #6c757d;
        }
        .billMailWrapper .mailContent .totalArea .item span .excludeTax {
          display: block;
          margin-top: 2px;
          color: #6c757d;
          font-size: 11px;
        }
        .billMailWrapper .mailContent .totalArea .item p {
          margin: 0;
        }
        .billMailWrapper .mailContent .customerNotes {
          border-top: 1px dashed #dee2e6;
          padding-top: 0.5rem;
          margin-top: 0.5rem;
        }
        .billMailWrapper .mailContent .clientInfo {
          border-top: 1px dashed #dee2e6;
          padding-top: 0.5rem;
          margin-top: 0.5rem;
        }
        .billMailWrapper .mailContent .clientInfo span {
          display: block;
          text-align: center;
        }
        .billMailWrapper .mailContent .clientInfo span a {
          color: #000000;
          text-decoration: none;
        }
        .billMailWrapper .mailContent .qrCodeArea {
          border-top: 1px dashed #dee2e6;
          padding-top: 0.5rem;
          margin-top: 0.5rem;
          text-align: center;
        }
        .billMailWrapper .mailContent .qrCodeArea img {
          max-height: 100px;
        }
        .billMailWrapper .mailContent .qrCodeArea span {
          margin-top: -7px;
          display: block;
        }
        .billMailWrapper .mailContent .footerBillText {
          text-align: center;
          margin: 1rem auto;
        }
        .billMailWrapper .mailContent .payBtn {
          display: block;
          text-align: center;
          margin-top: 0.5rem;
          background-color: #00d595;
          height: 45px;
          line-height: 45px;
          font-weight: bold;
          color: #fff;
          text-decoration: none;
          border-radius: 100px;
        }
        .billMailWrapper .sureCopyrights {
          text-align: center;
          color: #777;
          margin-top: 10px;
        }
      }
    </style>
  </head>
  <body>
    <div class="billMailWrapper">
      <div class="mailContent" @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>

        @if($bill->user->logo)
          <div class="clientLogo">
            <img src="{{ $bill->user->logo_url }}" alt="logo">
          </div><!-- clientLogo -->
        @endif

        @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
          <div class="taxInvoiceText">{{ __('Simplified Tax Invoice') }}</div>
        @endif

        <div class="clientMail">{{ $bill->user->business_name}}</div>

        @if(isset($bill->user->settings->header_bill))
          <div class="headerBillText">{{ $bill->user->settings->header_bill }}</div>
        @endif

        <div class="businessAddress">{{ $bill->user->business_address }}</div>

        <div class="businessMobile" dir="ltr">{{  $bill->user->business_mobile }}</div>

        @if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
          <div class="billWillExpire">{{ __('the bill will expire in')}}</div>
        @endif

        <div class="billInfo">
          @if($bill->user->settings->add_tax_invoice)
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Bill No.') }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->number }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Date') }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->created_at->format('d/m/Y')}}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
            @if($bill->user->vat_registration_number)
              <div class="item">
                <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Organization VAT Registration Number') }}</span>
                <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->user->vat_registration_number }}</p>
              <div class="clearfix"></div>
              </div><!-- item -->
            @endif
          @else
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('No.') }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->number }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Date') }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->created_at->format('d/m/Y')}}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          @if($bill->user->settings->display_customer_details)
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Customer Name') }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->customer->name }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Mobile Number') }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->customer->mobile }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
        </div><!-- billInfo -->

        <div class="blockTable" @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
          <table>
            <thead>
              <tr>
                <th @if(app()->getLocale() == 'ar') style="text-align: right;" @else style="text-align: left;" @endif>{{ __('Description') }}</th>
                <th>{{ __('Price') }}</th>
                <th>{{ __('Quantity') }}</th>
                @if($bill->add_tax)
                  <th width="35%" @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ __('Total include added tax') }}</th>
                @else
                  <th width="35%" @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ __('Total') }}</th>
                @endif
              </tr>
            </thead>
            <tbody>
              @foreach($bill->items as $item)
              <tr>
                <td @if(app()->getLocale() == 'ar') style="text-align: right;" @else style="text-align: left;" @endif>{!! $item->product_name !!}</td>
                <td>{{ $item->product_price  }}</td>
                <td>{{ $item->quantity  }}</td>
                @if( $bill->add_tax)
                  <td @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ ($item->product_price * $item->quantity) + (($item->product_price * $item->quantity) * $bill->tax_value / 100)  }}</td>
                @else
                  <td @if(app()->getLocale() == 'ar') style="text-align: left;" @else style="text-align: right;" @endif>{{ $item->product_price * $item->quantity }}</td>
                @endif
              </tr>
              @endforeach
            </tbody>
          </table>
        </div><!-- blockTable -->
        
        <div class="totalArea">
          @if( $bill->add_tax || $bill->add_discount)
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>
                {{ __('Total amount') }} 
                <small>( {{ __('SAR') }} )</small> 
                @if( $bill->add_tax)
                  <div class="excludeTax">( {{ __('Exclude added tax') }} )</div>
                @endif
              </span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->sub_total }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          @if( $bill->add_discount)
            <div class="item Discount">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Discount amount') }} <small>( {{ __('SAR') }} )</small></span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->discount }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          @if( $bill->user->pay_fees == 'client')
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('payment fees') }} <small>( {{ __('SAR') }} )</small></span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->payment_fees }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          @if( $bill->add_tax)
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Added tax value (:percentge%)', ['percentge'=>$bill->tax_value]) }}</span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->vat }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          @if( $bill->channel_extra_amount)
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{$bill->channel_extra_title}} <small>( {{ __('SAR') }} )</small></span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->channel_extra_amount }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          @if( $bill->channel_extra_vat)
            <div class="item">
              <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Vat') }} <small>( {{$bill->channel_extra_title}} ( {{ $bill->tax_value }} % ) )</small></span>
              <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->channel_extra_vat }}</p>
              <div class="clearfix"></div>
            </div><!-- item -->
          @endif
          <div class="item Total">
            <span @if(app()->getLocale() == 'ar') style="float: right;" @else style="float: left;" @endif>{{ __('Total amount') }} <small>( {{ __('SAR') }} )</small></span>
            <p @if(app()->getLocale() == 'ar') style="float: left;" @else style="float: right;" @endif>{{ $bill->total}}</p>
            <div class="clearfix"></div>
          </div><!-- item -->
        </div><!-- totalArea -->
        
        @if($bill->customer_notes)<div class="customerNotes">{{$bill->customer_notes}}</div> @endif

        <div class="clientInfo">
          <span>{{ $bill->customer_name }}</span>
          <span dir="ltr">+966{{ $bill->customer_mobile }}</span>
          <span dir="ltr">{{ $bill->customer_email }}</span>
        </div><!-- clientInfo -->

        @if($bill->user->settings->add_tax_invoice)
          <div class="qrCodeArea">
            {!! generateQRcode($bill) !!}
            <span>{{ __('Tax Invoice') }}</span>
          </div><!-- qrCodeArea -->
        @endif
          
        @if(isset($bill->user->settings->footer_bill))
          <div class="footerBillText">{{ $bill->user->settings->footer_bill }}</div>
        @endif

        <a href="{{ $bill->pay_url }}" target="_blank" title="PAY" class="payBtn">{{ __('Details') }}</a>

      </div><!-- mailContent -->
      <div class="sureCopyrights">
        © 2020 SureBills. All rights reserved
      </div><!-- sureCopyrights -->
    </div><!-- billMailWrapper -->
  </body>
</html>
