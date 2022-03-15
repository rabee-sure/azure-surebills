<nav class="navbar fixed-top">
  <div class="d-flex align-items-center navbar-left">
    @auth
      @if(auth()->user()->is_complete_profile)
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
        @endif
    @endauth

  </div>

  <a class="navbar-logo" href="{{ url('/')}}">
    <span class="logo d-none d-xs-block"></span>
    <span class="logo-mobile d-block d-xs-none"></span>
  </a>

  <div class="navbar-right">
    <div class="header-icons d-inline-block align-middle">
      <div class="d-none d-md-inline-block align-text-bottom mr-3">
        <div class="custom-switch custom-switch-primary-inverse custom-switch-small pl-1" data-toggle="tooltip" data-placement="left" title="{{ __('Dark Mode')}}">
          <input class="custom-switch-input" id="switchDark" type="checkbox" checked>
          <label class="custom-switch-btn" for="switchDark"></label>
        </div>
      </div>



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
          <a class="dropdown-item" href="{{ url('account')}}">{{ __('My Account') }}</a>
          @if(App::isLocale('en'))
            <a class="dropdown-item" href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">عربي</a>
          @else
            <a class="dropdown-item" href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">English</a>
          @endif

          @can('update settings')
          <a class="dropdown-item" href="{{ route('settings') }}" title="English">{{__('Settings')}}</a>
        @endcan
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
