<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $lang) }}"  @if($lang == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@100;200;300;400;500;600;700&family=IBM+Plex+Sans:ital,wght@0,100..700;1,100..700&display=swap" rel="stylesheet">
    <!-- Core CSS -->
    <!-- build:css assets/vendor/css/theme.css  -->
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/node-waves/node-waves.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/pickr/pickr-themes.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/vendor/css/core.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/v2/css/print.css') }}" />
    @stack('css_styles')
  </head>
  <body>
    @yield('content')
  </body>
</html>
