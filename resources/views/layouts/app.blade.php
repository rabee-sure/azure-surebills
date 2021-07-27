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
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    @if(app()->getLocale() == 'ar')
      <link rel="stylesheet" href="/css/bootstrap.rtl.only.min.css?v={{ config('app.asset_version') }}" />
    @endif
    <link rel="stylesheet" href="/css/all.css?v={{ config('app.asset_version') }}" />
    @yield('css_styles')
    @stack('header-css')



    <style type="text/css" media="print">
  @media print {
    #paymentslog {display: none;}
  }
</style>
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
  <body id="app-container" class=" @if(app()->getLocale() == 'ar') rtl @else ltr @endif 
    @if(auth()->user()->is_complete_profile) 
        menu-default 
    @else
     rounded menu-sub-hidden main-hidden sub-hidden 
     @endif show-spinner">
    @include('layouts.navbar')
    @if(auth()->user()->is_complete_profile)
        @include('layouts.sidebar')
    @endif

    <main>
      <div class="container-fluid" id="app">
        @yield('content')
      </div>
    </main>
    <script type="text/javascript">
         if (typeof Storage !== "undefined") {
          localStorage.setItem("dore-direction", @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr"@endif);
        }   
    </script>

    @include('layouts.footer')
    @stack('footer-scripts')

@if (env('APP_ENV') == 'production')
<!-- Google Tag Manager (noscript) -->
<noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-K4WN2GW"
height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<!-- End Google Tag Manager (noscript) -->
@endif

</body>

</html>
