<header class="bg-white position-sticky top-0">
  <div class="container-fluid h-100 d-flex align-items-center justify-content-between">
    <div class="headerRight d-flex align-items-center justify-content-start">
      <div class="sidebarButton d-flex align-items-center justify-content-center flex-column">
        <span></span>
        <span></span>
        <span></span>
        <!-- <i class="fal fa-stream"></i> -->
      </div>
    </div><!-- headerRight -->
    <div class="logo d-flex align-items-center justify-content-center flex-grow-1">
      <a href="{{ url('/') }}" title="SureBills">
        <img src="{{ asset('new/images/logo.webp') }}" alt="SureBills" loading="lazy" width="586px" height="187px" class="mw-100 w-auto h-auto">
      </a>
    </div><!-- logo -->
    <div class="userList position-relative d-flex justify-content-end">
      <button class="d-flex align-items-center justify-content-end border-0 bg-transparent p-0" type="button" id="UserListItem" data-bs-toggle="dropdown" aria-expanded="false">
        <span class="name d-none d-md-block">{{ Auth::user()->name }}</span>
        <img alt="{{ Auth::user()->name }}" src="{{ auth()->user()->gravatar}}" class="d-block rounded-circle" />
      </button>
      <div class="dropdown-menu p-0" aria-labelledby="UserListItem">
        <a class="d-flex align-items-center justify-content-start" href="{{ url('account')}}">
          <i class="fal fa-user d-flex align-items-center justify-content-center"></i>
          <span>{{ __('My Account') }}</span>
        </a>
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
        <a class="d-flex align-items-center justify-content-start" href="{{ route('settings') }}" title="{{__('Settings')}}">
          <i class="fal fa-cog d-flex align-items-center justify-content-center"></i>
          <span>{{__('Settings')}}</span>
        </a>
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