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
            <img src="{{ $bill->user->logo_url }}" alt="logo">
          </div><!-- logo -->
        @endif
        
        <div class="block_1">
          @if($bill->status == 'paid' && $bill->user->settings->add_tax_invoice)
            <div class="taxInvoiceText">{{ __('Simplified Tax Invoice') }}</div>
          @endif
          <span> {{ $bill->user->business_name}}</span>
          @if(isset($bill->user->settings->header_bill))
              <p>{{ $bill->user->settings->header_bill }}</p>
          @endif
          <p>{{ $bill->user->business_address }}</p>
          <small>{{  $bill->user->business_mobile }}</small>
          @if($bill->application_id && !$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
            <div class="countdown alert alert-warning d-flex align-items-center justify-content-center" id="new_countdown">
              <p class="mb-0">{{ __('the bill will expire in')}}</p>
              <span id="hm_timer"></span>
            </div><!-- countdown -->
          @endif
        </div><!-- block_1 -->
        
        @if($bill->status == 'expired')
          <div class="alert alert-danger" role="alert">
            {{ __('this bill has been expired', ['number' => $bill->number ]) }}
          </div>
        @endif
        @if($bill->status == 'paid')
          <div class="alert alert-success" role="alert">
            @if ($bill->depositTransaction)
              {{ __('Paid') }} - {{ $bill->depositTransaction->card_brand }} {{ $bill->depositTransaction->card }} {{ $bill->depositTransaction->receipt }}
            @else
            {{ __('this bill has been successfully', ['number' => $bill->number ]) }}
            @endif
          </div>
        @endif
        @if($bill->status == 'canceled')
          <div class="alert alert-danger" role="alert">
            {{ __('this bill has been canceled', ['number' => $bill->number ]) }}
          </div>
        @endif

        <div class="block_2">
          @if($bill->user->settings->add_tax_invoice)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Bill No.') }}</span>
              <span>{{ $bill->number }}</span>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Date') }}</span>
              <span>{{ $bill->created_at->format('d/m/Y')}}</span>
            </div><!-- d-flex -->
            @if($bill->user->vat_registration_number)
              <div class="d-flex align-items-center justify-content-between">
                <span>{{ __('Organization VAT Registration Number') }}</span>
                <span>{{ $bill->user->vat_registration_number }}</span>
              </div><!-- d-flex -->
            @endif
          @else
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('No.') }}</span>
            <span>{{ $bill->number }}</span>
          </div><!-- d-flex -->
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Date') }}</span>
            <span>{{ $bill->created_at->format('d/m/Y')}}</span>
          </div><!-- d-flex -->
          @endif
          @if($bill->user->settings->display_customer_details)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Customer Name') }}</span>
              <span>{{ $bill->customer->name }}</span>
            </div><!-- d-flex -->
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Mobile Number') }}</span>
              <span>{{ $bill->customer->mobile }}</span>
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
                <th width="35%">{{ __('Total include added tax') }}</th>
                @else
                <th width="35%">{{ __('Total') }}</th>
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
            <div class="d-flex align-items-center justify-content-between">
              <div class="d-flex align-items-start justify-content-between flex-column">
                <span>{{ __('Total amount') }} ({{ __('SAR') }})</span>
                @if( $bill->add_tax)
                <small>( {{ __('Exclude added tax') }} )</small>
                @endif
              </div>
              <span>{{ $bill->sub_total }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->add_discount)
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Discount amount') }} ({{ __('SAR') }})</span>
            <span>{{ $bill->discount }}</span>
          </div><!-- d-flex -->
          @endif
          @if( $bill->user->pay_fees == 'client')
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('payment fees') }} ({{ __('SAR') }})</span>
              <span>{{ $bill->payment_fees }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->add_tax)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Added tax value (:percentge %)', ['percentge'=>$bill->tax_value]) }}</span>
              <span>{{ $bill->vat }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->channel_extra_amount)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{$bill->channel_extra_title}} ({{ __('SAR') }})</span>
              <span>{{ $bill->channel_extra_amount }}</span>
            </div><!-- d-flex -->
          @endif
          @if( $bill->channel_extra_vat)
            <div class="d-flex align-items-center justify-content-between">
              <span>{{ __('Vat') }} ({{$bill->channel_extra_title}} ({{ $bill->tax_value }}%))</span>
              <span>{{ $bill->channel_extra_vat }}</span>
            </div><!-- d-flex -->
          @endif
          <div class="d-flex align-items-center justify-content-between">
            <span>{{ __('Total amount') }} ({{ __('SAR') }})</span>
            <span>{{ $bill->total}}</span>
          </div><!-- d-flex -->
        </div><!-- total_area -->

        @if($bill->customer_notes)<div class="customer_notes">{{$bill->customer_notes}}</div> @endif
        <div class="block_4">
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
      </div><!-- mail_content -->
      <div class="copyrights">
        © 2020 SureBills. All rights reserved
      </div><!-- copyrights -->
    </div><!-- mail_wrapper -->
  </body>
</html>
