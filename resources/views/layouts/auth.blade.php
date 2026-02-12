<!DOCTYPE html>
<html
  lang="{{ str_replace('_', '-', app()->getLocale()) }}"
  class="light-style layout-menu-fixed layout-compact"
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
    <link rel="stylesheet" href="{{ asset('assets/v2/css/custom.css') }}" />
    <!-- Vendors CSS -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/perfect-scrollbar/perfect-scrollbar.css') }}" />
    <!-- endbuild -->
    <!-- Page CSS -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/@form-validation/form-validation.css') }}" />
    <!-- Page -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/css/pages/page-auth.css') }}" />

    <!-- Helpers -->
    <script src="{{ asset('assets/v2/vendor/js/helpers.js') }}"></script>
    <!--! Template customizer & Theme config files MUST be included after core stylesheets and helpers.js in the <head> section -->

    <!--? Config:  Mandatory theme config file contain global vars & default theme options, Set your preferred theme option in this file.  -->
    <script src="{{ asset('assets/v2/js/config.js') }}"></script>

    <!-- Apply stored theme immediately to prevent flash and persist across page loads -->
    <script>
      (function() {
        var templateName = document.documentElement.getAttribute('data-template') || 'horizontal-menu-template';
        var storedTheme = localStorage.getItem('templateCustomizer-' + templateName + '--Theme');
        if (storedTheme) {
          var themeToApply = storedTheme === 'system'
            ? (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light')
            : storedTheme;
          document.documentElement.setAttribute('data-bs-theme', themeToApply);
        }
      })();
    </script>

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
    <!-- Content -->

    <div class="container-xxl">
      <div class="authentication-wrapper authentication-basic container-p-y">
        <div class="authentication-inner py-6">

          <div class="d-flex align-items-center justify-content-end position-relative z-10 mb-3">
            @if(App::isLocale('en'))
              <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي" class="btn btn-sm btn-label-primary waves-effect">عربي</a>
            @else
              <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English" class="btn btn-sm btn-label-primary waves-effect">English</a>
            @endif
          </div>

          <!-- Login -->
          <div class="card">
            <div class="card-body">
              <!-- Logo -->
              <div class="app-brand app-brand-logo-theme d-flex align-items-center justify-content-center mb-8">
                <img
                  src="{{ asset('assets/v2/img/logo_blue_light.png') }}"
                  alt="Sure Bills"
                  class="mw-100 app-brand-logo-light"
                  loading="lazy"
                  height="42px"
                >
                <img
                  src="{{ asset('assets/v2/img/logo_blue_dark.png') }}"
                  alt="Sure Bills"
                  class="mw-100 app-brand-logo-dark"
                  loading="lazy"
                  height="42px"
                >
              </div>
              <!-- /Logo -->

              @yield('content')

            </div>
          </div>
          <!-- /Login -->
        </div>
      </div>
    </div>
    <!-- / Content -->




  <!-- Conditions Modal -->
  <div class="modal fade" id="conditionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel1">{{ __('Terms & Conditions') }}</h5>
          <button
            type="button"
            class="btn-close"
            data-bs-dismiss="modal"
            aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-body d-block m-0">
            سوف يتم تحويل المبالغ لحساب بنكي باسم منشأتك فقط في حال كنت تستخدم سجل تجاري ، ولحسابك الشخصي المسجل في وثيقة العمل الحر في حال كنت تستخدم وثيقة عمل حر .
            <br>
            تأكيد من تحميلك للسجل التجاري او وثيقة العمل الحر لتوثيق حسابك والبدء بإستقبال المدفوعات.
            <br>
            يرجى الافصاح إذا كان نشاطك التجاري يتطلب ترخيص من جهة غير وزارة التجارة.
          </p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal"> {{ __('Close') }}</button>
        </div>
      </div>
    </div>
  </div>
  <!-- Conditions Modal -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/theme.js -->
    <script src="{{ asset('assets/v2/vendor/libs/jquery/jquery.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/popper/popper.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/js/bootstrap.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/node-waves/node-waves.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/@algolia/autocomplete-js.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/pickr/pickr.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/perfect-scrollbar/perfect-scrollbar.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/hammer/hammer.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/libs/i18n/i18n.js') }}"></script>
    <script src="{{ asset('assets/v2/vendor/js/menu.js') }}"></script>
    <!-- endbuild -->
    <!-- Vendors JS -->
    <!-- Main JS -->
    <script src="{{ asset('assets/v2/js/main.js') }}"></script>
    <!-- Page JS -->
    <script src="{{ asset('assets/v2/js/pages-auth.js') }}"></script>
    @stack('footer-scripts')
    <!-- Laravel Javascript Validation -->
    <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}"></script>
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
