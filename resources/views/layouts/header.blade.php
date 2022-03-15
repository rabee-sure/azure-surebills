<header class="bg-white position-sticky top-0 d-print-none">
  <div class="container-fluid h-100 d-flex align-items-center justify-content-between">
    <div class="headerRight d-flex align-items-center justify-content-start">
      @if(auth()->user()->is_complete_profile)
        <div class="sidebarButton d-none d-md-flex align-items-center justify-content-center flex-column">
          <span></span><span></span><span></span>
        </div><!-- sidebarButton -->
        <div class="sidebarBtnMobile d-flex d-md-none align-items-center justify-content-center flex-column">
          <span></span><span></span><span></span>
        </div><!-- sidebarBtnMobile -->
      @else
        <div class="sidebarButton d-none"></div>
        <div class="sidebarBtnMobile d-none"></div>
      @endif
    </div><!-- headerRight -->
    <div class="userList position-relative d-flex justify-content-end">
      <button class="d-flex align-items-center justify-content-end border-0 bg-transparent p-0" type="button" id="UserListItem" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="name d-none d-sm-block">{{ Auth::user()->name }}</span>
        <img alt="{{ Auth::user()->name }}" src="{{ auth()->user()->gravatar}}" class="d-block rounded-circle" />
      </button>
      <div class="dropdown-menu p-0" aria-labelledby="UserListItem">
        @if(auth()->user()->is_complete_profile)
          <a class="d-flex align-items-center justify-content-start" href="{{ url('account')}}" title="{{ __('Settings') }}">
            <i class="fal fa-cog d-flex align-items-center justify-content-center"></i>
            <span>{{ __('Settings') }}</span>
          </a>
        @endif
        @if(App::isLocale('en'))
          <a class="d-flex align-items-center justify-content-start" href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">
            <i class="fal fa-globe-europe d-flex align-items-center justify-content-center"></i>
            <span>عربي</span>
          </a>
        @else
          <a class="d-flex align-items-center justify-content-start" href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">
            <i class="fal fa-globe-europe d-flex align-items-center justify-content-center"></i>
            <span>English</span>
          </a>
        @endif
        <a class="d-flex align-items-center justify-content-starts" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="{{ __('Logout') }}">
          <i class="fal fa-sign-out d-flex align-items-center justify-content-center"></i>
          <span>{{ __('Logout') }}</span>
        </a>
        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
          @csrf
        </form>
      </div><!-- dropdown-menu -->
    </div><!-- userList -->
  </div><!-- container -->
</header><!-- header -->