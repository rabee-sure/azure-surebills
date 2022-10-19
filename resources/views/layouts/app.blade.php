<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/favicon.ico') }}" type="image/x-icon" />
    <link rel="apple-touch-icon" href="{{ asset('images/apple-touch-icon.png') }}" />
    <link rel="apple-touch-icon" sizes="57x57" href="{{ asset('images/apple-touch-icon-57x57.png') }}" />
    <link rel="apple-touch-icon" sizes="72x72" href="{{ asset('images/apple-touch-icon-72x72.png') }}" />
    <link rel="apple-touch-icon" sizes="76x76" href="{{ asset('images/apple-touch-icon-76x76.png') }}" />
    <link rel="apple-touch-icon" sizes="114x114" href="{{ asset('images/apple-touch-icon-114x114.png') }}" />
    <link rel="apple-touch-icon" sizes="120x120" href="{{ asset('images/apple-touch-icon-120x120.png') }}" />
    <link rel="apple-touch-icon" sizes="144x144" href="{{ asset('images/apple-touch-icon-144x144.png') }}" />
    <link rel="apple-touch-icon" sizes="152x152" href="{{ asset('images/apple-touch-icon-152x152.png') }}" />
    <link rel="apple-touch-icon" sizes="180x180" href="{{ asset('images/apple-touch-icon-180x180.png') }}" />
    <title>@yield('title') - Sure Bills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">

    @yield('css_styles')

    @stack('header-css')

    <link rel="stylesheet" href="/new/css/main.css?v={{ config('app.asset_version') }}" />
    
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
  <body id="app-container" class="show-spinner">

    @include('layouts.header')

    @if(auth()->user()->is_complete_profile)
      @include('layouts.sidebar')
    @endif

    <main id="app" @if(!auth()->user()->is_complete_profile) class="isCompleteProfile" @endif>
      @yield('content')
    </main><!-- main -->

    @include('layouts.footer')

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
      <!-- Global site tag (gtag.js) - Google Analytics -->
    @endif

    <!-- Jquery -->
    <script src="{{ asset('new/js/jquery-3.6.0.min.js') }}"></script>

    @stack('footer-scripts')

    @if(in_array(request()->route()->getName(), ['channels.show', 'integration','mobile_verify', 'home' ]))
      <script src="{{ asset('new/js/app.js') }}?v={{ config('app.asset_version') }}" defer></script>
    @endif

    <script>
      window._locale = '{{ app()->getLocale() }}';
      window._translations = {!! cache('translations') !!};
    </script>

    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js') }}?v={{ config('app.asset_version')}}"></script>

    <!-- Script -->
    <script src="{{ asset('new/js/main.js') }}?v={{ config('app.asset_version') }}"></script>

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
      <!-- Global site tag (gtag.js) - Google Analytics -->
    @endif

    <!-- Hotjar Tracking Code for https://www.bills.surepay.sa --> 
    <script>
      (function(h,o,t,j,a,r){ 
        h.hj=h.hj||function(){(h.hj.q=h.hj.q||[]).push(arguments)}; 
        h._hjSettings={hjid:3209183,hjsv:6}; 
        a=o.getElementsByTagName('head')[0]; 
        r=o.createElement('script');r.async=1; 
        r.src=t+h._hjSettings.hjid+j+h._hjSettings.hjsv; 
        a.appendChild(r); 
      })(window,document,'https://static.hotjar.com/c/hotjar-','.js?sv='); 
    </script>
  
  </body>
</html>