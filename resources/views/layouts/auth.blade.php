<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"  @if(app()->getLocale() == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Sure Bills') }} - @yield('title')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Scripts -->
    <script src="{{ asset('js/jbootstrap.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}" defer></script>

    <!-- Styles -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet">
    <link href="{{ asset('css/slick.css') }}" rel="stylesheet">
    <link href="{{ asset('css/glide.core.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
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
      <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
      @yield('footer-scripts')
      <script src="{{ asset('js/slick.min.js') }}"></script>
      <script src="{{ asset('js/glide.min.js') }}"></script>

      
  </body>
</html>
