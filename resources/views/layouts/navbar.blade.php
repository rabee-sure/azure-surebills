<nav class="navbar fixed-top">
  <div class="d-flex align-items-center navbar-left">
    @auth
    <a href="#" class="menu-button d-none d-md-block">
      <svg class="main" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 17">
        <rect x="0.48" y="0.5" width="7" height="1" />
        <rect x="0.48" y="7.5" width="7" height="1" />
        <rect x="0.48" y="15.5" width="7" height="1" />
      </svg>
      <svg class="sub" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 17">
        <rect x="1.56" y="0.5" width="16" height="1" />
        <rect x="1.56" y="7.5" width="16" height="1" />
        <rect x="1.56" y="15.5" width="16" height="1" />
      </svg>
    </a>

    <a href="#" class="menu-button-mobile d-xs-block d-sm-block d-md-none">
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 17">
        <rect x="0.5" y="0.5" width="25" height="1" />
        <rect x="0.5" y="7.5" width="25" height="1" />
        <rect x="0.5" y="15.5" width="25" height="1" />
      </svg>
    </a>
    @endauth
    <div class="position-relative d-none d-sm-inline-block">
      @if(App::isLocale('en'))
        <a href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">عربي</a>
      @else
        <a href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
      @endif
    </div>

{{--     <div class="search" data-search-path="Pages.Search.html?q=">
      <input placeholder="Search...">
      <span class="search-icon"><i class="simple-icon-magnifier"></i></span>
    </div> --}}
    
  </div>

  <a class="navbar-logo" href="{{ url('/')}}">
    <span class="logo d-none d-xs-block"></span>
    <span class="logo-mobile d-block d-xs-none"></span>
  </a>

  <div class="navbar-right">
    <div class="header-icons d-inline-block align-middle">
      <div class="d-none d-md-inline-block align-text-bottom mr-3">
        <div class="custom-switch custom-switch-primary-inverse custom-switch-small pl-1" data-toggle="tooltip" data-placement="left" title="Dark Mode">
          <input class="custom-switch-input" id="switchDark" type="checkbox" checked>
          <label class="custom-switch-btn" for="switchDark"></label>
        </div>
      </div>
@auth
{{--       <div class="position-relative d-none d-sm-inline-block">
        <a href="store-client.html" class="header-icon btn btn-empty" data-toggle="tooltip"
        data-placement="top" title="Store">
          <i class="iconsminds-clothing-store"></i>
        </a>
      </div>

      <div class="position-relative d-inline-block">
        <button class="header-icon btn btn-empty" type="button" id="notificationButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <i class="simple-icon-bell"></i>
          <span class="count">3</span>
        </button>
        <div class="dropdown-menu dropdown-menu-right mt-3 position-absolute" id="notificationDropdown">
          <div class="scroll">
            <div class="d-flex flex-row mb-3 pb-3 border-bottom">
              <a href="#">
                <p class="font-weight-medium mb-1">You've got a payment! 319.00 SAR from Ali Adel Ahmed </p>
                <p class="text-muted mb-0 text-small">2020/02/09 08:31 AM</p>
              </a>
            </div>
            <div class="d-flex flex-row mb-3 pb-3 border-bottom">
              <a href="#">
                <p class="font-weight-medium mb-1">You’ve got a new bill of SR30.00</p>
                <p class="text-muted mb-0 text-small">2020/02/08 04:17 PM</p>
              </a>
            </div>
            <div class="d-flex flex-row mb-3">
              <a href="#">
                <p class="font-weight-medium mb-1">You’ve got a new bill of SR130.00</p>
                <p class="text-muted mb-0 text-small">2020/02/05 05:23 PM</p>
              </a>
            </div>
          </div>
        </div>
      </div> --}}
@endauth

      <button class="header-icon btn btn-empty d-none d-sm-inline-block" type="button" id="fullScreenButton">
        <i class="simple-icon-size-fullscreen"></i>
        <i class="simple-icon-size-actual"></i>
      </button>

    </div>

    <div class="user d-inline-block">
      <!-- Authentication Links -->
      @guest

          <div class="position-relative d-none d-sm-inline-block">
            <a href="{{ route('login') }}" class="header-icon btn btn-empty" >
            {{ __('Login') }}
            </a>
          </div>

          @if (Route::has('register'))
            <div class="position-relative d-none d-sm-inline-block">
              <a href="{{ route('register') }}" class="header-icon btn btn-empty" >
              {{ __('Register') }}
              </a>
            </div>
          @endif
      @else
        <button class="btn btn-empty p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          <span class="name">{{ Auth::user()->name }}</span>
          <span><img alt="Profile Picture" src="{{ auth()->user()->gravatar}}" /></span>
        </button>
        <div class="dropdown-menu dropdown-menu-right mt-3">
          <a class="dropdown-item" href="{{ url('account/account')}}">{{ __('Account') }}</a>
          <a class="dropdown-item" href="{{ route('logout') }}"
             onclick="event.preventDefault();
                           document.getElementById('logout-form').submit();">
              {{ __('Logout') }}
          </a>

          <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
              @csrf
          </form>
        </div>
      @endguest

    </div>
  </div>
</nav>
