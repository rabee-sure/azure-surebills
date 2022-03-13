@auth

  @php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  @endphp

  <aside class="bg-white position-fixed end-0 d-print-none">
    <a href="/" title="{{ __('Dashboard') }}"  class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('home') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-tachometer-alt-fast"></i>
      <span class="text-center">{{ __('Dashboard') }}</span>
    </a>
    <a href="/bills?{{$separated}}" title="{{ __('Bills') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('bills*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-file-invoice"></i>
      {{ __('Bills') }}
    </a>
    <a href="/pos/categories" title="{{ __('POS') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('pos*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-cash-register"></i>
      {{ __('POS') }}
    </a>
    <a href="{{ route('customers.index') }}" title="{{ __('Customers') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('customers*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-users"></i>
      {{ __('Customers') }}
    </a>
    <a href="{{ route('statement.index') }}" title="{{ __('Statement') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('statement*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-analytics"></i>
      {{ __('Statement') }}
    </a>
    <a href="{{ route('transfers.index') }}" title="{{ __('Transfers') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('transfers*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-sack-dollar"></i>
      {{ __('Transfers') }}
    </a>
    <a href="{{ route('account') }}" title="{{__('Settings')}}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('account*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-user-cog"></i>
      {{__('Settings')}}
    </a>
    @if(in_array(Auth::user()->email, explode(',', env('NOVA_ALLOWED_ADMINS'))))
      <a href="{{ route('reports.index') }}" title="{{ __('Reports') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('reports*') ? 'active' : '' }}">
        <i class="d-flex align-items-center justify-content-center fal fa-file-chart-line"></i>
        {{ __('Reports') }}
      </a>
    @endif
    <a href="{{ route('orders.all') }}" title="{{ __('Orders') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('orders*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-file-chart-line"></i>
      {{ __('Orders') }}
    </a>
    <a href="{{ route('products.settings') }}" title="{{ __('Store Settings') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('products*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-file-chart-line"></i>
      {{ __('Store Settings') }}
    </a>
  </aside><!-- aside -->
  
@endauth
