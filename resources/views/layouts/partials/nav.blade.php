@auth

  @php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  @endphp

  <aside id="layout-menu" class="layout-menu-horizontal menu-horizontal menu flex-grow-0">
              <div class="container-fluid d-flex h-100">
                <ul class="menu-inner">
        <!-- Dashboards -->
        @if(auth()->user()->source != 'pos')
          <li class="menu-item {{ Request::is('home') ? 'active' : '' }}">
            <a href="{{ url('/') }}" title="{{ __('Dashboard') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-home"></i>
              <div>{{ __('Dashboard') }}</div>
            </a>
          </li>
        @endif
        <!-- Bills -->
        @can('show bills')
          <li class="menu-item {{ Request::is('bills*') ? 'active' : '' }}">
            <a href="/bills?{{$separated}}" title="{{ __('Bills') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-receipt-2"></i>
              <div>{{ __('Bills') }}</div>
            </a>
          </li>
        @endcan
        <!-- Electronic payment -->
        @can('show statement')
          <li class="menu-item {{ Request::is('statement*') ? 'active' : '' }}">
            <a href="{{ route('statement.index') }}" title="{{ __('Electronic payment') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-credit-card-pay"></i>
              <div>{{ __('Electronic payment') }}</div>
            </a>
          </li>
        @endcan
        <!-- Payment Record -->
        @can('show payment record')
          <li class="menu-item {{ Request::is('payment_record*') ? 'active' : '' }}">
            <a href="{{ route('reports.paymentRecord') }}" title="{{ __('Payment Record') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-logs"></i>
              <div>{{ __('Payment Record') }}</div>
            </a>
          </li>
        @endcan
        <!-- customers -->
        @can('show customers')
          <li class="menu-item {{ Request::is('customers*') ? 'active' : '' }}">
            <a href="{{ route('customers.index') }}" title="{{ __('Customers') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-users"></i>
              <div>{{ __('Customers') }}</div>
            </a>
          </li>
        @endcan
        <!-- transfers -->
        @can('show transfers')
          <li class="menu-item {{ Request::is('transfers*') ? 'active' : '' }}">
            <a href="{{ route('transfers.index') }}" title="{{ __('Transfers') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-transfer"></i>
              <div>{{ __('Transfers') }}</div>
            </a>
          </li>
        @endcan
        <!-- users -->
        @can('show users')
          <li class="menu-item {{ Request::is('users*') ? 'active' : '' }}">
            <a href="{{ route('users.index') }}" title="{{ __('Users') }}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-users-group"></i>
              <div>{{ __('Users') }}</div>
            </a>
          </li>
          <!-- roles -->
          <li class="menu-item {{ Request::is('roles*') ? 'active' : '' }}">
            <a href="{{route('roles.index')}}" title="{{__('Roles')}}" class="menu-link">
              <i class="menu-icon tf-icons ti ti-list-check"></i>
              <div>{{ __('Roles') }}</div>
            </a>
          </li>
        @endcan
        <!-- Settings -->
        <li class="menu-item {{ Request::is('account*') ? 'active' : '' }}">
          <a href="{{ route('account') }}" title="{{ __('Settings') }}" class="menu-link">
            <i class="menu-icon tf-icons ti ti-settings"></i>
            <div>{{ __('Settings') }}</div>
          </a>
        </li>
        {{-- <a href="{{ route('pricing') }}" title="{{__('Pricing')}}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('pricing*') ? 'active' : '' }}">
          <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-user-cog"></i>
          {{__('Pricing')}}
        </a> --}}
        {{-- @if(in_array(Auth::user()->email, explode(',', env('NOVA_ALLOWED_ADMINS'))))
          <a href="{{ route('reports.index') }}" title="{{ __('Reports') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('reports*') ? 'active' : '' }}">
            <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-chart-line"></i>
            {{ __('Reports') }}
          </a>
        @endif --}}
        {{-- <a href="{{ route('orders.all') }}" title="{{ __('Orders') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('orders*') ? 'active' : '' }}">
          <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-chart-line"></i>
          {{ __('Orders') }}
        </a>
        <a href="{{ route('products.settings') }}" title="{{ __('Store Settings') }}" class="d-flex text-center align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('products*') ? 'active' : '' }}">
          <i class="d-flex align-items-center justify-content-center flex-shrink-0 fal fa-file-chart-line"></i>
          {{ __('Store Settings') }}
        </a> --}}
      </ul>

    </div>
  </aside><!-- aside -->

@endauth
