@auth

  @php
    $statues = session('status_filters', ['pending', 'paid'])?? [];
    $separated = (count($statues)) ? 'statuses[]='.implode("&statuses[]=", $statues):'';
  @endphp

  <aside class="bg-white position-fixed end-0 min-vh-100">
      <ul class="list-unstyled">
        <li class="{{ Request::is('/') ? 'active' : '' }}">
          <a href="/" title="{{ __('Dashboard') }}">
            <i class="iconsminds-dashboard"></i>
            <span>{{ __('Dashboard') }}</span>
          </a>
        </li>
        <li class="{{ Request::is('bills*') ? 'active' : '' }}">
          <a href="/bills?{{$separated}}" title="{{ __('Bills') }}">
            <i class="iconsminds-testimonal"></i>
           {{ __('Bills') }}
          </a>
        </li>
        <li class="{{ Request::is('customers*') ? 'active' : '' }}">
          <a href="{{ route('customers.index') }}" title="{{ __('Customers') }}">
            <i class="iconsminds-mens"></i>
            {{ __('Customers') }}
          </a>
        </li> 
        <li class="{{ Request::is('statement*') ? 'active' : '' }}">
          <a href="{{ route('statement.index') }}" title="{{ __('Statement') }}">
            <i class="iconsminds-statistic"></i>
            {{ __('Statement') }}
          </a>
        </li>
        {{-- <li>
          <a href="#store" title="Store">
            <i class="iconsminds-shop-2"></i>
            {{ __('Store') }}
          </a>
        </li> --}}
        <li class="{{ Request::is('transfers*') ? 'active' : '' }}">
          <a href="{{ route('transfers.index') }}" title="{{ __('Transfers') }}">
            <i class="iconsminds-money-bag"></i>
            {{ __('Transfers') }}
          </a>
        </li>
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
        @if(count(auth()->user()->channels))
          <li class="{{ Request::is('channels*') ? 'active' : '' }}">
            <a href="{{ route('channels.index') }}" title="{{ __('Channels') }}">
              <i class="iconsminds-gears"></i>
              {{ __('Channels') }}
            </a>
          </li>
        @endif
        <li class="{{ Request::is('integration*') ? 'active' : '' }}">
          <a href="{{ route('integration') }}" title="{{ __('Integration') }}">
            <i class="iconsminds-gears"></i>
            {{ __('Integration') }}
          </a>
        </li>
      </ul>
  </aside>

@endauth