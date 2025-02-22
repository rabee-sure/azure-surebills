<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() == 'ar')dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Sure Bills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

    <link rel="stylesheet" href="{{asset('css/bill_details.css')}}" />
    @stack('styles')
    <style>
      @font-face {
        font-family: "A Jannat LT" !important;
        src: url("{{asset('fonts/AJannatLT-Bold/AJannatLT-Bold_1.ttf')}}") format("truetype") !important;
        font-weight: normal !important;
        font-style: normal !important;
      }
     
      .riyal-symbol-font {
        font-family: "A Jannat LT", sans-serif !important;
      }
    </style>
  </head>
  <body class="riyal-symbol-font">

    @yield('content')
    <script src="{{ asset('js/jbootstrap.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>

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

    @stack('footer-scripts')
</body>
</html>
