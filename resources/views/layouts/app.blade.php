<!DOCTYPE html>
<html  lang="{{ str_replace('_', '-', app()->getLocale()) }}">
  <head>
    <meta charset="utf-8">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Sure Bills') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <link rel="stylesheet" href="/css/all.css" />

  </head>
  <body id="app-container" class="menu-default show-spinner">
    @include('layouts.navbar')
    @include('layouts.sidebar')

    <main>
      <div class="container-fluid" id="app">
        @yield('content')
      </div>
    </main>

    @include('layouts.footer')

</body>

</html>