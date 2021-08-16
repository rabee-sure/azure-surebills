<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="UTF-8">
    <title>@yield('title') - Sure Bills</title>

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

    <!-- Scripts -->
    <script src="{{ asset('js/jbootstrap.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}" defer></script>

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/slick.css') }}" rel="stylesheet">
    <link href="{{ asset('css/glide.core.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
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
  <body class=" @if(app()->getLocale() == 'ar') rtl @else ltr @endif background show-spinner no-footer">
    <div class="fixed-background"></div>
    <main>
      <div class="container">
        @yield('content')
      </div>
    </main>
<!-- Scripts -->

 <!-- Laravel Javascript Validation -->
      <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}"></script>
      @stack('footer-scripts')
      <script src="{{ asset('js/slick.min.js') }}"></script>
      {{-- <script src="{{ asset('js/glide.min.js') }}"></script> --}}


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
      
  </body>
</html>
