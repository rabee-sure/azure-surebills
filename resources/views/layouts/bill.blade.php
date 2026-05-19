<!doctype html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  dir="{{app()->getLocale() == 'en' ? 'ltr' : 'rtl'}}"
  data-skin="default"
  data-assets-path="{{ asset('assets/v2') }}/"
  data-template="horizontal-menu-template"
  data-bs-theme="light"
>
  <head>
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />
    <title>@yield('title') - Sure Bills</title>
    <!-- Favicon -->
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('assets/v2/img/favicons/apple-icon-57x57.png') }}">
    <link rel="apple-touch-icon" sizes="60x60" href="{{ asset('assets/v2/img/favicons/apple-icon-60x60.png') }}">
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('assets/v2/img/favicons/apple-icon-72x72.png') }}">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('assets/v2/img/favicons/apple-icon-76x76.png') }}">
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('assets/v2/img/favicons/apple-icon-114x114.png') }}">
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('assets/v2/img/favicons/apple-icon-120x120.png') }}">
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('assets/v2/img/favicons/apple-icon-144x144.png') }}">
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('assets/v2/img/favicons/apple-icon-152x152.png') }}">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('assets/v2/img/favicons/apple-icon-180x180.png') }}">
    <link rel="icon" type="image/png" sizes="192x192"  href="{{ asset('assets/v2/img/favicons/android-icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('assets/v2/img/favicons/favicon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="96x96" href="{{ asset('assets/v2/img/favicons/favicon-96x96.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('assets/v2/img/favicons/favicon-16x16.png') }}">
    <link rel="manifest" href="{{ asset('assets/v2/img/favicons/manifest.json') }}">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="{{ asset('assets/v2/img/favicons/ms-icon-144x144.png') }}">
    <meta name="theme-color" content="#ffffff">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <!-- Icons -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/fonts/fontawesome.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/fonts/tabler-icons.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/fonts/flag-icons.css') }}" />

    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/css/demo.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/css/custom.css') }}" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />

    <!-- endbuild -->

    <!-- Page CSS -->
    @stack('css_styles')

    @stack('header-css')

    @php
      $settings = $bill->user->settings;
      $bgColor = $settings->background_color_body ?? '#fafafa';
      $bgImage = bill_background_image_url($settings->background_image_file ?? null);
      $textColor = $settings->text_color_body ?? '#000000';
      $btnBgColor = $settings->background_color_payment_button ?? '#00d595';
      $btnTextColor = $settings->text_color_payment_button ?? '#ffffff';
    @endphp

    <style>
      .singlebBillSimple_page,
      .simple_bill_page {
        @if($bgImage)
          background-image: url({!! json_encode($bgImage) !!});
          background-size: cover;
          background-position: center;
          background-repeat: no-repeat;
        @else
          background-color: {{ $bgColor }};
        @endif
      }

      .singlebBillSimple_page a,
      .singlebBillSimple_page .card-body,
      .singlebBillSimple_page .card-body .text-heading,
      .singlebBillSimple_page .card-body .table tr th,
      .singlebBillSimple_page .card-body .table tr td,
      .simple_bill_page .card-body,
      .simple_bill_page .card-body .text-heading {
        color: {{ $textColor }} !important;
      }

      #payButton, .payment_area button[type="button"], .payment_area .btn-success {
        background-color: {{ $btnBgColor }} !important;
        color: {{ $btnTextColor }} !important;
        border-color: {{ $btnBgColor }} !important;
      }

      #payButton:hover, .payment_area button[type="button"]:hover, .payment_area .btn-success:hover {
        background-color: {{ $btnBgColor }} !important;
        color: {{ $btnTextColor }} !important;
        opacity: 0.9;
      }
    </style>

    <!-- Helpers -->
    <script src="{{ asset('assets/v2/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/v2/js/config.js') }}"></script>

  </head>
  <body>

    @yield('content')

    @if (env('APP_ENV') == 'production')
      <!-- Google Tag Manager (noscript) -->
      <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K4WN2GW"
      height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
      <!-- End Google Tag Manager (noscript) -->
      <!-- Global site tag (gtag.js) - Google Analytics -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=G-WRYZ8313H2"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());

        gtag('config', 'G-WRYZ8313H2');
      </script>
    @endif

    <script src="{{ asset('assets/v2/vendor/libs/jquery/jquery.js') }}"></script>

    <!-- Count Down -->
    <script src="{{ asset('assets/v2/js/jquery.countdownTimer.min.js') }}"></script>
    <!-- <script src="https://code.jquery.com/jquery-migrate-1.2.1.js"></script> -->
    <script type='text/javascript'>
      $(function(){
        $("#hm_timer").countdowntimer({
          minutes : {{ $bill->remaining_time_minutes}},
          seconds : {{ $bill->remaining_time_seconds}},
          size : "lg",
          timeUp : timeisUp
        });

        function timeisUp() {
          $("#new_countdown").remove();
          $("#payment_area").remove();
          $("#status").empty();
          $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
        }
      });
    </script>
    <!-- Count Down -->

    <!-- Page JS -->
    @stack('footer-scripts')

</body>
</html>
