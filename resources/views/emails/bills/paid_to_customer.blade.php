<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    @include('emails.bills.partials.bill_style')
  </head>
  <body>

    <div class="billMailWrapper">
      <div class="mailContent" @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
    
        @include('emails.bills.partials.bill_header')

        @include('emails.bills.partials.bill_status')
        
        @include('emails.bills.partials.bill_info')

        @include('emails.bills.partials.bill_items')

        @include('emails.bills.partials.bill_totals')

        @include('emails.bills.partials.bill_footer')

      </div><!-- mailContent -->
      <div class="sureCopyrights">
        © 2020 SureBills. All rights reserved
      </div><!-- sureCopyrights -->
    </div><!-- billMailWrapper -->

  </body>
</html>
