<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>{{ config('app.name', 'Sure Bills') }} - @yield('title')</title>

    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <!-- Scripts -->
    <script src="{{ asset('js/jbootstrap.js') }}"></script>
    <script src="{{ asset('js/auth.js') }}" defer></script>

    <!-- Styles -->
    <link href="{{ asset('css/auth.css') }}" rel="stylesheet">
  </head>
  <body class="background show-spinner no-footer">
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

      
  </body>
</html>
