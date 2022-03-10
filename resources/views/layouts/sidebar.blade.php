@auth

@php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
@endphp

<div class="menu">
  <div class="main-menu">
    <div class="scroll">
      <ul class="list-unstyled">
        <li class="{{ Request::is('/') ? 'active' : '' }}">
          <a href="/" title="{{ __('Dashboard') }}">
            <i class="iconsminds-dashboard"></i>
            <span>{{ __('Dashboard') }}</span>
          </a>
        </li>

        @can('show bills')
        <li class="{{ Request::is('bills*') ? 'active' : '' }}">
          <a href="/bills?{{$separated}}" title="{{ __('Bills') }}">
            <i class="iconsminds-testimonal"></i>
           {{ __('Bills') }}
          </a>
        </li>
        @endcan

        @can('show pos')
        <li class="{{ Request::is('pos*') ? 'active' : '' }}">
          <a href="/pos/categories" title="{{ __('POS') }}">
            <i class="iconsminds-testimonal"></i>
           {{ __('POS') }}
          </a>
        </li>
        @endcan

        @can('show customers')
        <li class="{{ Request::is('customers*') ? 'active' : '' }}">
          <a href="{{ route('customers.index') }}" title="{{ __('Customers') }}">
            <i class="iconsminds-mens"></i>
            {{ __('Customers') }}
          </a>
        </li>
        @endcan

        @can('show statement')
        <li class="{{ Request::is('statement*') ? 'active' : '' }}">
          <a href="{{ route('statement.index') }}" title="{{ __('Statement') }}">
            <i class="iconsminds-statistic"></i>
            {{ __('Statement') }}
          </a>
        </li>
        @endcan

        {{-- <li>
          <a href="#store" title="Store">
            <i class="iconsminds-shop-2"></i>
            {{ __('Store') }}
          </a>
        </li> --}}

        @can('show transfers')
        <li class="{{ Request::is('transfers*') ? 'active' : '' }}">
          <a href="{{ route('transfers.index') }}" title="{{ __('Transfers') }}">
            <i class="iconsminds-money-bag"></i>
            {{ __('Transfers') }}
          </a>
        </li>
        @endcan

        <li class="{{ Request::is('account*') ? 'active' : '' }}">
          <a href="{{ route('account') }}" title="{{ __('My Account') }}">
            <i class="iconsminds-male-2"></i>
            {{ __('My Account') }}
          </a>
        </li>
        {{-- <li class="{{ Request::is('pricing*') ? 'active' : '' }}">
          <a href="{{ route('pricing') }}" title="{{ __('Pricing') }}">
            <i class="iconsminds-tag-3"></i>
            {{ __('Pricing') }}
          </a>
        </li>  --}}

        @can('show channels')
        @if(count(auth()->user()->channels))
          <li class="{{ Request::is('channels*') ? 'active' : '' }}">
            <a href="{{ route('channels.index') }}" title="{{ __('Channels') }}">
              <i class="iconsminds-gears"></i>
              {{ __('Channels') }}
            </a>
          </li>
        @endif
        @endcan

        @can('show applications')
        <li class="{{ Request::is('integration*') ? 'active' : '' }}">
          <a href="{{ route('integration') }}" title="{{ __('Integration') }}">
            <i class="iconsminds-gears"></i>
            {{ __('Integration') }}
          </a>
        </li>
        @endcan

        @can('show products')
        <li>
          <a href="{{ route('products.all') }}" title="{{ __('Products') }}">
            <i class="iconsminds-project"></i>
            <span class="d-inline-block">{{ __('Products') }}</span>
          </a>
        </li>
        @endcan

        @can('show product categories')
        <li>
          <a href="{{ route('categories.all') }}" title="{{ __('Product Sections') }}">
            <i class="iconsminds-clothing-store"></i>
            <span class="d-inline-block">{{ __('Product Sections') }}</span>
          </a>
        </li>
        @endcan
      </ul>
    </div>
  </div>

  <div class="sub-menu">
    <div class="scroll">
      <ul class="list-unstyled" data-link="store">
        <li>
          <a href="{{ route('orders.all') }}" title="{{ __('Orders') }}">
            <i class="iconsminds-shopping-bag"></i> <span class="d-inline-block">{{ __('Orders') }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('products.settings') }}" title="{{ __('Store Settings') }}">
            <i class="iconsminds-clothing-store"></i> <span class="d-inline-block">{{ __('Store Settings') }}</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

</div>
@endauth
