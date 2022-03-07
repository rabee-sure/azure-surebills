@auth

  @php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  @endphp

  <aside class="bg-white position-fixed end-0">
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
    <a href="{{ route('account') }}" title="{{ __('My Account') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('account*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-user-cog"></i>
      {{ __('My Account') }}
    </a>
    @if(count(auth()->user()->channels))
      <a href="{{ route('channels.index') }}" title="{{ __('Channels') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('channels*') ? 'active' : '' }}">
        <i class="d-flex align-items-center justify-content-center fal fa-cogs"></i>
        {{ __('Channels') }}
      </a>
    @endif
    <a href="{{ route('integration') }}" title="{{ __('Integration') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('integration*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-network-wired"></i>
      {{ __('Integration') }}
    </a>
    <a href="{{ route('products.all') }}" title="{{ __('Products') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('products*') ? 'active' : '' }}">
      <i class="fal fa-boxes"></i>
      {{ __('Products') }}
    </a>
    <a href="{{ route('categories.all') }}" title="{{ __('Product Sections') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('categories*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-store"></i>
      {{ __('Product Sections') }}
    </a>
    <a href="{{ route('reports.index') }}" title="{{ __('Reports') }}" class="d-flex align-items-center justify-content-center flex-column rounded w-100 {{ Request::is('reports*') ? 'active' : '' }}">
      <i class="d-flex align-items-center justify-content-center fal fa-file-chart-line"></i>
      {{ __('Reports') }}
    </a>
  </aside><!-- aside -->
  
@endauth