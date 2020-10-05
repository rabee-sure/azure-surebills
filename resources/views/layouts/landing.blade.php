<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="/images/favicon.ico" type="image/x-icon" />
    <link rel="apple-touch-icon" href="/images/apple-touch-icon.png" />
    <link rel="apple-touch-icon" sizes="57x57" href="/images/apple-touch-icon-57x57.png" />
    <link rel="apple-touch-icon" sizes="72x72" href="/images/apple-touch-icon-72x72.png" />
    <link rel="apple-touch-icon" sizes="76x76" href="/images/apple-touch-icon-76x76.png" />
    <link rel="apple-touch-icon" sizes="114x114" href="/images/apple-touch-icon-114x114.png" />
    <link rel="apple-touch-icon" sizes="120x120" href="/images/apple-touch-icon-120x120.png" />
    <link rel="apple-touch-icon" sizes="144x144" href="/images/apple-touch-icon-144x144.png" />
    <link rel="apple-touch-icon" sizes="152x152" href="/images/apple-touch-icon-152x152.png" />
    <link rel="apple-touch-icon" sizes="180x180" href="/images/apple-touch-icon-180x180.png" />
    <title>@yield('title') - Sure Bills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="/landing/dist/css/main.css">
    @yield('css_styles')
</head>
<body>
    <header>
      <div class="container insideDiv">
        <div class="logo">
          <a href="index.html" title="شور بيلز">
            <img src="/landing/dist/images/logo.svg" alt="شور بيلز">
          </a>
        </div><!-- logo -->
        <div class="main_menu">
          <a href="index.html" title="شور بيلز">شور بيلز</a>
          <a href="#start_work" title="المميزات">المميزات</a>
          <a href="#prices" title="الأسعار">الأسعار</a>
          <a href="contact.html" title="اتصل بنا">اتصل بنا</a>
        </div><!-- main_menu -->
      </div><!-- container -->
    </header><!-- header -->

    @yield('content')

    <footer>
      <div class="container">
        <div class="row align-items-center">
          <div class="col-12 col-sm-6 col-md-7 col-lg-8 col-xl-8">
            <div class="footer_menu">
              <span><a href="index.html" title="شور بيلز">شور بيلز</a></span>
              <span><a href="#start_work" title="انضم لتجارنا">انضم لتجارنا</a></span>
              <span><a href="privacy.html" title="الخصوصية">الخصوصية</a></span>
              <span><a href="#how_work" title="كيف نعمل">كيف نعمل</a></span>
              <span><a href="contact.html" title="اتصل بنا">اتصل بنا</a></span>
              <span><a href="terms.html" title="الشروط والاحكام">الشروط والاحكام</a></span>
              <span><a href="#faq" title="الاسئلة الشائعة">الاسئلة الشائعة</a></span>
            </div><!-- footer_menu -->
          </div><!-- col-12 -->
        </div><!-- row -->
        <div class="footer_bottom">
          <div class="copyrights">
            جميع الحقوق محفوظة © 2019
          </div><!-- copyrights -->
          <div class="socialmedia">
            <a href="#" title="twitter" target="_blank"><i class="fab fa-twitter"></i></a>
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
    <script type="text/javascript" src="/landing/dist/js/main.js"></script>

    @stack('footer-scripts')
</body>

</html>
