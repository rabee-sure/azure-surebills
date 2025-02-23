<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', $lang) }}"  @if($lang == 'ar') dir="rtl" @else dir="ltr" @endif>
  <head>
    <meta charset="utf-8">
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Almarai:wght@300;400;700&family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/new/css/print.css?v={{ config('app.asset_version') }}" />
  </head>
  <style>
    @font-face {
      font-family: "A Jannat LT";
      src: url("{{asset('fonts/AJannatLT-Bold/AJannatLT-Bold_1.ttf')}}") format("truetype");
      font-weight: normal;
      font-style: normal;
    }
    .rtl {
      direction: rtl !important;
    }

    .riyal-symbol-font {
      font-family: "A Jannat LT", sans-serif !important;
    }
  </style>
  <body>
    @yield('content')
  </body>
</html>