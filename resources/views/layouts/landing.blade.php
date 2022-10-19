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
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
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
<body>
    <header>
      <div class="container insideDiv">
        <div class="logo">
          <a href="{{ url('/') }}" title="شور بيلز">
            <img src="{{ asset('images/logo.png') }}" alt="شور بيلز">
          </a>
        </div><!-- logo -->
        <div class="main_menu">
          <a href="{{ url('/') }}" title="شور بيلز">شور بيلز</a>
          <a href="{{ url('/') }}#start_work" title="المميزات">المميزات</a>
          <!-- <a href="{{ url('/') }}#prices" title="الأسعار">الأسعار</a> -->
          <a href="{{ url('/') }}/contact" title="اتصل بنا">اتصل بنا</a>

          <a href="{{ url('/') }}/register" class="register" title="تسجيل"><i class="far fa-user"></i><span>تسجيل</span></a>
          <a href="{{ url('/') }}/login" class="login" title="دخول"><i class="fas fa-sign-in-alt"></i><span>دخول</span></a>

        </div><!-- main_menu -->
      </div><!-- container -->
    </header><!-- header -->

    @yield('content')

    <footer>
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-sm-6 col-md-7 col-lg-8 col-xl-8">
            <div class="footer_menu">
              <span><a href="{{ url('/') }}" title="شور بيلز">شور بيلز</a></span>
              <span><a href="{{ url('/') }}#start_work" title="انضم لتجارنا">انضم لتجارنا</a></span>
              <span><a href="{{ url('/') }}/privacy" title="الخصوصية">الخصوصية</a></span>
              <span><a href="{{ url('/') }}#how_work" title="كيف نعمل">كيف نعمل</a></span>
              <span><a href="{{ url('/') }}/contact" title="اتصل بنا">اتصل بنا</a></span>
              <span><a href="{{ url('/') }}/terms" title="الشروط والاحكام">الشروط والاحكام</a></span>
              <span><a href="{{ url('/') }}#faq" title="الاسئلة الشائعة">الاسئلة الشائعة</a></span>
            </div><!-- footer_menu -->
          </div><!-- col-12 -->
        </div><!-- row -->
        <div class="footer_bottom">
          <div class="copyrights">
            جميع الحقوق محفوظة © 2020
          </div><!-- copyrights -->
          <div class="socialmedia">
            <a href="https://twitter.com/SurePay_sa" title="twitter" target="_blank"><i class="fab fa-twitter"></i></a>
            <a href="#" title="linkedin" target="_blank"><i class="fab fa-linkedin-in"></i></a>
            <a href="#" title="facebook" target="_blank"><i class="fab fa-facebook-square"></i></a>
            <a href="#" title="youtube" target="_blank"><i class="fab fa-youtube"></i></a>
          </div><!-- socialmedia -->
        </div><!-- footer_bottom -->
      </div><!-- container -->
    </footer><!-- footer -->

    <button type="button" class="scrollup"><i class="fas fa-long-arrow-alt-up"></i></button> 
    <!--[if lt IE 8 ]>
    <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.2/CFInstall.min.js"></script>
    <script>window.attachEvent("onload",function(){CFInstall.check({mode:"overlay"})})</script>
    <![endif]-->
    <script type="text/javascript" src="{{ asset('js/landing.js') }}"></script>

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
