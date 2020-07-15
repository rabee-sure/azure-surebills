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
        <li class="{{ Request::is('settlements*') ? 'active' : '' }}">
          <a href="{{ route('settlements.index') }}" title="Statement">
            <i class="iconsminds-statistic"></i>
            {{ __('Settlements') }}
          </a>
        </li>
        <li class="{{ Request::is('account*') ? 'active' : '' }}">
          <a href="#account" title="Account">
            <i class="iconsminds-male-2"></i>
            {{ __('Account') }}
          </a>
        </li>
        <li class="{{ Request::is('pricing*') ? 'active' : '' }}">
          <a href="{{ route('pricing') }}" title="Pricing">
            <i class="iconsminds-tag-3"></i>
            {{ __('Pricing') }}
          </a>
        </li> 
        <li>
          <a href="#integration" title="Integration">
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
          <a href="store.html">
            <i class="iconsminds-clothing-store"></i> <span class="d-inline-block">{{ __('Store page') }}</span>
          </a>
        </li>
        <li>
          <a href="products.html">
            <i class="iconsminds-project"></i> <span class="d-inline-block">{{ __('Products') }}</span>
           </a>
        </li>
      </ul>

      <ul class="list-unstyled" data-link="account">
        <li>
          <a href="{{ route('account_information') }}">
            <i class="iconsminds-id-card"></i> <span class="d-inline-block">{{ __('Account Information') }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('bank_information') }}">
            <i class="iconsminds-bank"></i> <span class="d-inline-block">{{ __('Bank Information') }}</span>
           </a>
        </li>
        <li>
          <a href="{{ route('business_information') }}">
            <i class="iconsminds-management"></i> <span class="d-inline-block">{{ __('Business Information') }}</span>
          </a>
        </li>
        <li>
          <a href="{{ route('change_password') }}">
            <i class="iconsminds-type-pass"></i> <span class="d-inline-block">{{ __('Change Password') }}</span>
          </a>
        </li>
        <!-- <li>
          <a href="notifications.html">
            <i class="iconsminds-bell"></i> <span class="d-inline-block">{{ __('Notifications') }}</span>
          </a>
        </li> -->
      </ul>      
      <ul class="list-unstyled" data-link="integration">
        <li>
          <a href="{{ route('integration')}}">
            <i class="iconsminds-type-pass"></i> <span class="d-inline-block">{{ __('Integration') }}</span>
          </a>
        </li>
        <!-- <li>
          <a href="notifications.html">
            <i class="iconsminds-bell"></i> <span class="d-inline-block">{{ __('Documentation') }}</span>
          </a>
        </li> -->
      </ul>

    </div>
  </div>
</div>
@endauth
