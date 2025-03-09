<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full font-sans antialiased">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ \Laravel\Nova\Nova::name() }}</title>

    <!-- Fonts -->
    <!-- <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,800,800i,900,900i" rel="stylesheet"> -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Sans+Arabic:wght@300;400;500;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ mix('app.css', 'vendor/nova') }}">

    <style>
      body {
        font-family: "Roboto", "IBM Plex Sans Arabic";
        font-weight: 400;
        font-size: 15px;
      }

      [lang="ar"] body {
        font-family: "IBM Plex Sans Arabic", "Roboto";
        direction: rtl;
      }
      .nova-login-area h2 {
        font-family: "Roboto", "IBM Plex Sans Arabic";
        font-weight: 500;
        font-size: 25px;
      }
      [lang="ar"] .nova-login-area h2 {
        font-family: "IBM Plex Sans Arabic", "Roboto";
      }
      .nova-login-area .form-group label {
        font-weight: 400 !important;
        font-size: 16px;
        color: #000000;
        text-align: start;
      }
      .nova-login-area .form-group input {
        min-height: 45px;
        padding: 0 .5rem;
        color: #000000;
        border: 1px solid #dddddd;
        border-radius: .25rem;
        outline: none;
        box-shadow: none;
        font-weight: 400;
        font-size: 16px;
      }
      .bottom-area label {
        gap: .5rem;
      }
      button[type="submit"] {
        min-height: 45px;
        border-radius: .25rem;
        padding: 0;
        font-size: 16px;
        background-color: #00d595;
        text-shadow: none;
        font-weight: 500;
        transition: .3s;
        width: 100%;
        color: #ffffff;
        outline: none;
      }
      button[type="submit"]:hover {
        background-color: #00c686;
      }
    </style>

    <!-- Custom Meta Data -->
    @include('nova::partials.meta')

    <!-- Theme Styles -->
    @foreach(\Laravel\Nova\Nova::themeStyles() as $publicPath)
        <link rel="stylesheet" href="{{ $publicPath }}">
    @endforeach
</head>
<body class="bg-40 text-black h-full">
    <div class="h-full">
        <div class="px-view py-view mx-auto">
            @yield('content')
        </div>
    </div>
</body>
</html>