
<nav class="layout-navbar navbar navbar-expand-xl align-items-center bg-navbar-theme" id="layout-navbar">
  <div class="container-fluid">
    <div class="navbar-brand app-brand demo d-none d-xl-flex py-0 m-0">
      <a href="{{ url('/') }}" class="app-brand-link">
      <span class="app-brand-logo demo app-brand-logo-theme">
        <img
          src="{{ asset('assets/v2/img/logo_blue_light.png') }}"
          alt="Sure Bills"
          class="mw-100 app-brand-logo-light"
          loading="lazy"
          height="24px"
        >
        <img
          src="{{ asset('assets/v2/img/logo_blue_dark.png') }}"
          alt="Sure Bills"
          class="mw-100 app-brand-logo-dark"
          loading="lazy"
          height="24px"
        >
        </span>
      </a>
      <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-xl-none">
        <i class="ti ti-x ti-sm align-middle"></i>
      </a>
    </div>

    <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
      <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
        <i class="ti ti-menu-2 ti-sm"></i>
      </a>
    </div>

    <div class="navbar-nav-right d-flex align-items-center justify-content-end" id="navbar-collapse">
      <ul class="navbar-nav flex-row align-items-center ms-md-auto">

        <!-- Theme Toggle Button (Light/Dark) -->
        <li class="nav-item">
          <a class="nav-link btn btn-icon btn-text-secondary rounded-pill waves-effect theme-toggle-btn hide-arrow" href="javascript:void(0);" title="{{ __('Toggle theme') }}" role="button">
            <i class="ti ti-sun icon-base  icon-22px text-heading theme-toggle-icon-light"></i>
            <i class="ti ti-moon icon-base icon-22px text-heading theme-toggle-icon-dark d-none"></i>
          </a>
        </li>
        <!-- Style Switcher Dropdown -->
        <!-- <li class="nav-item dropdown-style-switcher dropdown me-2 me-xl-0">
          <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" title="{{ __('Theme') }}">
            <i class="ti ti-device-desktop ti-md"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end dropdown-styles">
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-bs-theme-value="light">
                <span class="align-middle"><i class="ti ti-sun me-2"></i>Light</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-bs-theme-value="dark">
                <span class="align-middle"><i class="ti ti-moon me-2"></i>Dark</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="javascript:void(0);" data-bs-theme-value="system">
                <span class="align-middle"><i class="ti ti-device-desktop me-2"></i>System</span>
              </a>
            </li>
          </ul>
        </li> -->
        <!-- / Style Switcher-->

        <!--/ Language -->
        <li class="nav-item dropdown-language dropdown me-3 me-xl-2">
          <a
            class="nav-link dropdown-toggle hide-arrow btn btn-icon btn-text-secondary rounded-pill"
            href="javascript:void(0);"
            data-bs-toggle="dropdown">
            <i class="icon-base ti ti-language icon-22px text-heading"></i>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <a class="dropdown-item" href="{{ route('changeLang', ['lang' => 'en']) }}" title="English">
                <span>English</span>
              </a>
            </li>
            <li>
              <a class="dropdown-item" href="{{ route('changeLang', ['lang' => 'ar']) }}" title="عربي">
                <span>عربي</span>
              </a>
            </li>
          </ul>
        </li>
        <!--/ Language -->

        <!-- User -->
        <li class="nav-item navbar-dropdown dropdown-user dropdown">
          <a class="nav-link dropdown-toggle hide-arrow p-0" href="javascript:void(0);" data-bs-toggle="dropdown">
            <div class="avatar">
              <span class="avatar-initial rounded-circle bg-label-primary">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
            </div>
          </a>
          <ul class="dropdown-menu dropdown-menu-end">
            <li>
              <div class="dropdown-item mt-0">
                <div class="d-flex align-items-center">
                  <div class="flex-shrink-0 me-2">
                    <div class="avatar">
                      <span class="avatar-initial rounded-circle bg-label-primary">{{ mb_strtoupper(mb_substr(Auth::user()->name, 0, 1)) }}</span>
                    </div>
                  </div>
                  <div class="flex-grow-1">
                    <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                    <small class="text-body-secondary">{{ Auth::user()->email }}</small>
                  </div>
                </div>
              </div>
            </li>
            <li>
              <div class="dropdown-divider my-1 mx-n2"></div>
            </li>
            @if(auth()->user()->is_complete_profile)
              <li>
                <a class="dropdown-item" href="{{ route('account') }}" title="{{ __('Settings') }}">
                  <i class="icon-base ti ti-settings me-2 icon-md"></i
                  ><span class="align-middle">{{ __('Settings') }}</span>
                </a>
              </li>
            @endif
            <li>
              <a class="dropdown-item" href="{{ route('account_information') }}" title="{{ __('My Information') }}">
                <i class="icon-base ti ti-user-cog me-2 icon-md"></i
                ><span class="align-middle">{{ __('My Information') }}</span>
              </a>
            </li>
            <li>
              <div class="dropdown-divider my-1 mx-n2"></div>
            </li>
            <li>
              <div class="d-grid px-2 pt-2 pb-1">
                <a class="btn btn-sm btn-danger d-flex" href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" title="{{ __('Logout') }}">
                  <small class="align-middle">{{ __('Logout') }}</small>
                  <i class="icon-base ti tabler-logout ms-2 icon-14px"></i>
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                  @csrf
                </form>
              </div>
            </li>
          </ul>
        </li>
        <!--/ User -->

      </ul>
    </div>
  </div>
</nav>
