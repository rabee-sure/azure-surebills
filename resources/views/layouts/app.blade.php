<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Go Pay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/css/all.css" />

  </head>
  <body id="app-container" class="menu-default show-spinner">
    @include('layouts.navbar')
    @include('layouts.sidebar')

    <main>
      <div class="container-fluid">
        @yield('content')
      </div>
    </main>

    @include('layouts.footer')

</body>

</html>