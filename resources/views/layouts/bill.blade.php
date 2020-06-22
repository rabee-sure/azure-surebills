<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sure Bills') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="/css/bill_details.css" />

  </head>
  <body>

    @yield('content')
    @yield('footer-scripts')
    <script src="{{ asset('js/jbootstrap.js') }}"></script>
    <script src="{{ asset('js/jquery.card.js') }}" defer></script>
    <script src="{{ asset('js/bill_details.js') }}" defer></script>
</body>
</html>