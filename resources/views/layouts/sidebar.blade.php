@auth
<div class="menu">
  <div class="main-menu">
    <div class="scroll">
      <ul class="list-unstyled">
        <li class="{{ Request::is('/') ? 'active' : '' }}">
          <a href="/" title="Dashboard">
            <i class="iconsminds-dashboard"></i>
            <span>{{ __('Dashboard') }}</span>
          </a>
        </li>
        <li class="{{ Request::is('bills*') ? 'active' : '' }}">
          <a href="{{ route('bills.index') }}" title="Bills">
            <i class="iconsminds-testimonal"></i>
           {{ __('Bills') }}
          </a>
        </li>
    <!-- <li>
          <a href="orders.html" title="Orders">
            <i class="iconsminds-shopping-bag"></i>
            Orders
          </a>
        </li>
        <li>
          <a href="#store" title="Store">
            <i class="iconsminds-shop"></i>
            Store
          </a>
        </li> -->
        <li class="{{ Request::is('customers*') ? 'active' : '' }}">
          <a href="{{ route('customers.index') }}" title="Customers">
            <i class="iconsminds-mens"></i>
            {{ __('Customers') }}
          </a>
        </li> 
        <li class="{{ Request::is('statement*') ? 'active' : '' }}">
          <a href="{{ route('statement.index') }}" title="Statement">
            <i class="iconsminds-statistic"></i>
            {{ __('Statement') }}
          </a>
        </li>
        <li>
          <a href="#store" title="Store">
            <i class="iconsminds-shop-2"></i>
            {{ __('Store') }}
          </a>
        </li>
        <!-- <li class="{{ Request::is('settlement*') ? 'active' : '' }}">
          <a href="{{ route('settlement.index') }}" title="Statement">
            <i class="iconsminds-statistic"></i>
            {{ __('Settlements') }}
          </a>
        </li> -->
        <li class="{{ Request::is('account*') ? 'active' : '' }}">
          <a href="{{ route('account') }}" title="My Account">
            <i class="iconsminds-male-2"></i>
            {{ __('My Account') }}
          </a>
        </li>
        <li class="{{ Request::is('pricing*') ? 'active' : '' }}">
          <a href="{{ route('pricing') }}" title="Pricing">
            <i class="iconsminds-tag-3"></i>
            {{ __('Pricing') }}
          </a>
        </li> 
        <li class="{{ Request::is('integration*') ? 'active' : '' }}">
          <a href="{{ route('integration') }}" title="Integration">
            <i class="iconsminds-gears"></i>
            {{ __('Integration') }}
          </a>
        </li>
      </ul>
    </div>
  </div>

  <div class="sub-menu">
    <div class="scroll">
      <ul class="list-unstyled" data-link="store">
        <li>
          <a href="{{ route('products.settings') }}" title="{{ __('Store Settings') }}">
            <i class="iconsminds-clothing-store"></i> <span class="d-inline-block">{{ __('Store Settings') }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('products.all') }}" title="{{ __('Products') }}">
            <i class="iconsminds-project"></i> <span class="d-inline-block">{{ __('Products') }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('products.categories') }}" title="{{ __('Product Sections') }}">
            <i class="iconsminds-clothing-store"></i> <span class="d-inline-block">{{ __('Product Sections') }}</span>
          </a>
        </li>
      </ul>
    </div>
  </div>

</div>
@endauth
