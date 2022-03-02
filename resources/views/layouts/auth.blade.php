<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="UTF-8">
    <title>@yield('title') - Sure Bills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    @yield('css_styles')

    <!-- Slick Slider -->
    <link rel="stylesheet" href="{{ asset('new/css/plugins/slick/slick.css') }}">
    <link rel="stylesheet" href="{{ asset('new/css/plugins/slick/slick-theme.css') }}">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ asset('new/css/auth.css') }}?v={{ config('app.asset_version') }}">

    @if (env('APP_ENV') == 'production')
      <!-- Google Tag Manager -->
      <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-K4WN2GW');</script>
      <!-- End Google Tag Manager -->
    @endif

  </head>
  <body>

    <main class="d-flex align-items-center justify-content-center min-vh-100 py-3">
      <div class="container">
        <section class="rounded-3 shadow d-flex align-items-start justify-content-between bg-white overflow-hidden flex-wrap flex-md-nowrap">
          @yield('content')
        </section><!-- section -->
        <div class="copyrights mt-3 d-flex align-items-center justify-content-center text-white">
          صُنع بـ <div class="heart d-block"></div> في <div class="ksa d-block"></div>
        </div><!-- copyrights -->
      </div><!-- container -->
    </main><!-- main -->

    @stack('footer-scripts')

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


    <!-- Jquery -->
    <script src="{{ asset('new/js/jquery-3.6.0.min.js') }}"></script>

    <!-- Slick Slider -->
    <script src="{{ asset('new/js/slick/slick.min.js') }}"></script>

    <!-- Script -->
    <script src="{{ asset('new/js/auth_main.js') }}"></script>

    <!--[if lt IE 8 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
    <script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
    <![endif]-->

  </body>
</html>
