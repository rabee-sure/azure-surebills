<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}" @if(app()->getLocale() == 'ar')dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title') - Sure Bills</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="/css/bill_details.css" />
    @stack('styles')
  </head>
  <body>

    @yield('content')
    <script src="{{ asset('js/jbootstrap.js') }}"></script>
    <script src="{{ asset('js/app.js') }}"></script>
    @stack('footer-scripts')
</body>
</html>
