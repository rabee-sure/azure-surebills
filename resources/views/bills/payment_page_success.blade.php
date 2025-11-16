<!DOCTYPE html>
<!--[if IE 8 ]><html class="ie ie8" lang="en"> <![endif]-->
<!--[if IE 9 ]><html class="ie ie9" lang="en"> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!-->
<html lang="{{ App::getLocale() }}" dir="{{ App::isLocale('ar') ? 'rtl' : 'ltr' }}">
    <!--<![endif]-->
    <head>
        <!-- Basic Page Needs -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge" />
        <title>{{ __('Payment Success') }} - {{__('SureBills')}}</title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
        <!-- App Css -->
        <link rel="stylesheet" href="{{asset('css/payment_page.css')}}">

        <style>
            .rtl {
            direction: rtl !important;
            }
            
            @font-face {
            font-family: "A Jannat LT";
            src: url("{{rtrim(config('payment.invoice_subdomain_url'), '/')}}/fonts/AJannatLT-Bold/AJannatLT-Bold_1.ttf") format("truetype");
            font-weight: normal;
            font-style: normal;
            }
            
            .riyal-symbol-font {
            font-family: "A Jannat LT", sans-serif;
            }
            .gap-1 {
            gap: .25rem !important;
        }
        .fw-bold {
            font-weight: bold !important;
        }
        </style>

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    </head>
    <body>
        <div class="container">
            <div class="row align-items-center justify-content-center">
                <div class="col-md-4 mt-4 p-0">
                    <div class="pay_apple">
                        <div id="status">
                            <div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>
                        </div>
                    </div>
                </div><!-- col-12 -->
            </div><!-- row -->
        </div><!-- container -->
    </body>
</html>
