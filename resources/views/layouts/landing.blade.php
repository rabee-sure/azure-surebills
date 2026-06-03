<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif class="scroll-smooth">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <title>@yield('title') - {{ __('landing.site_name') }}</title>
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
    <link rel="stylesheet" href="{{ asset('assets/landing/css/homepage.css') }}">
    @yield('css_styles')
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
  <body class="flex flex-col min-h-screen bg-[radial-gradient(circle_at_top_right,rgba(0,79,254,0.14),_transparent_26%),radial-gradient(circle_at_bottom_left,rgba(69,80,91,0.12),_transparent_26%),linear-gradient(180deg,#f9fbff_0%,#f3f8ff_46%,#ffffff_100%)] show-spinner">

    <header class="sticky top-0 py-4 !transition-all !duration-300 backdrop-blur-lg z-20">
      <div class="container flex items-center justify-between gap-3">
        <a href="{{ url('/') }}" title="{{ __('landing.site_name') }}" class="flex items-center justify-start outline-none">
          <img src="{{ asset('assets/landing/images/logo.webp') }}" alt="{{ __('landing.site_name') }}" class="max-h-8 lg:max-h-12 outline-none">
        </a>
        <div class="flex items-center justify-end gap-6 self-stretch">
          <ul class="hidden lg:flex items-center justify-end gap-6 self-stretch">
            <li class="self-stretch flex items-center justify-center">
              <a href="{{ url('/') }}" title="{{ __('landing.nav.home') }}" class="hover:text-[--PrimaryColor] flex items-center justify-center text-base transition-all duration-300 text-[--MainColor] self-stretch">{{ __('landing.nav.home') }}</a>
            </li>
            <li class="self-stretch flex items-center justify-center">
              <a href="{{ url('/#about') }}" title="{{ __('landing.nav.features') }}" class="hover:text-[--PrimaryColor] flex items-center justify-center text-base transition-all duration-300 text-[--MainColor] self-stretch">{{ __('landing.nav.features') }}</a>
            </li>
            <li class="self-stretch flex items-center justify-center">
              <a href="{{ url('/#how') }}" title="{{ __('landing.nav.how_it_works') }}" class="hover:text-[--PrimaryColor] flex items-center justify-center text-base transition-all duration-300 text-[--MainColor] self-stretch">{{ __('landing.nav.how_it_works') }}</a>
            </li>
            <li class="self-stretch flex items-center justify-center">
              <a href="{{ url('/#faqs') }}" title="{{ __('landing.nav.faqs') }}" class="hover:text-[--PrimaryColor] flex items-center justify-center text-base transition-all duration-300 text-[--MainColor] self-stretch">{{ __('landing.nav.faqs') }}</a>
            </li>
            <li class="self-stretch flex items-center justify-center">
              <a href="{{ url('/#contact') }}" title="{{ __('landing.nav.contact') }}" class="hover:text-[--PrimaryColor] flex items-center justify-center text-base transition-all duration-300 text-[--MainColor] self-stretch">{{ __('landing.nav.contact') }}</a>
            </li>
          </ul>
          <div class="flex items-center justify-end gap-3 shrink-0">
            <div class="flex items-center gap-1 text-sm font-medium">
              @if(App::isLocale('en'))
                <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي" class="btn btn-sm btn-label-primary waves-effect">AR</a>
              @else
                <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English" class="btn btn-sm btn-label-primary waves-effect">EN</a>
              @endif
            </div>
            <a href="{{ url('/register') }}" title="{{ __('landing.nav.register') }}" class="flex items-center justify-center gap-2 text-sm font-medium bg-[rgba(var(--PrimaryColorRGB),0.19)] text-[--PrimaryColor] rounded px-3 py-2 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 w-5 h-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" /><path d="M16 19h6" /><path d="M19 16v6" /><path d="M6 21v-2a4 4 0 0 1 4 -4h4" /></svg>
              <span class="hidden lg:block">{{ __('landing.nav.register_full') }}</span>
              <span class="block lg:hidden">{{ __('landing.nav.register') }}</span>
            </a>
            <a href="{{ url('/login') }}" title="{{ __('landing.nav.login') }}" class="flex items-center justify-center gap-2 text-sm font-medium bg-[--PrimaryColor] text-white rounded px-3 py-2 transition-all duration-300 hover:shadow-xl hover:-translate-y-1">
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 w-5 h-5"><path stroke="none" d="M0 0h24v24H0z" fill="none" /><path d="M15 8v-2a2 2 0 0 0 -2 -2h-7a2 2 0 0 0 -2 2v12a2 2 0 0 0 2 2h7a2 2 0 0 0 2 -2v-2" /><path d="M21 12h-13l3 -3" /><path d="M11 15l-3 -3" /></svg>
              <span class="hidden lg:block">{{ __('landing.nav.login_full') }}</span>
              <span class="block lg:hidden">{{ __('landing.nav.login') }}</span>
            </a>
          </div>
        </div>
      </div><!-- container -->
    </header><!-- header -->

    @yield('content')

    <footer class="border-t border-gray-300">
      <div class="container flex items-center justify-between flex-col lg:flex-row gap-3 py-4">
        <p class="text-sm text-[--MainColor]">{{ __('landing.footer.copyright') }}</p>
        <div class="flex items-center gap-2">
          <a href="{{ url('/privacy') }}" title="{{ __('landing.footer.privacy') }}" class="text-sm text-[--MainColor]">{{ __('landing.footer.privacy') }}</a>
          <span class="text-base text-[--MainColor]">-</span>
          <a href="{{ url('/terms') }}" title="{{ __('landing.footer.terms') }}" class="text-sm text-[--MainColor]">{{ __('landing.footer.terms') }}</a>
        </div>
      </div><!-- container -->
    </footer><!-- footer -->

    <script type="text/javascript">
      document.querySelectorAll("body > *").forEach(el => {
        el.style.opacity = 0;
      });
      setTimeout(() => {
        document.body.classList.remove("show-spinner");
        document.querySelectorAll("body > *").forEach(el => {
          el.style.transition = "opacity 0.1s ease";
          el.style.opacity = 1;
        });
      }, 300);

      document.addEventListener('DOMContentLoaded', function() {
          const header = document.querySelector('header');
          const scrollThreshold = 0;
          window.addEventListener('scroll', function() {
            if (window.scrollY > scrollThreshold) {
              header.classList.add('shadow-md','!py-3','bg-white');
            } else {
            header.classList.remove('shadow-md','!py-3','bg-white');
          }
        });
      });
    </script>

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
      <!-- Google tag (gtag.js) -->
      <script async src="https://www.googletagmanager.com/gtag/js?id=G-5GHCLW7TQK"></script>
      <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', 'G-5GHCLW7TQK');
      </script>
    @endif

  </body>
</html>
