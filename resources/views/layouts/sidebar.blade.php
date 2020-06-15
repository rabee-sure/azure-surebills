<div class="menu">
  <div class="main-menu">
    <div class="scroll">
      <ul class="list-unstyled">
        <li class="{{ Request::is('/') ? 'active' : '' }}">
          <a href="/" title="Dashboard">
            <i class="iconsminds-dashboard"></i>
            <span>Dashboard</span>
          </a>
        </li>
        <li class="{{ Request::is('bills*') ? 'active' : '' }}">
          <a href="{{ route('bills.index') }}" title="Bills">
            <i class="iconsminds-testimonal"></i>
            Bills
          </a>
        </li>
{{--         <li>
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
        </li>
        <li>
          <a href="customers.html" title="Customers">
            <i class="iconsminds-mens"></i>
            Customers
          </a>
        </li>
        <li>
          <a href="statement.html" title="Statement">
            <i class="iconsminds-statistic"></i>
            Statement
          </a>
        </li> --}}
        <li>
          <a href="#account" title="Account">
            <i class="iconsminds-male-2"></i>
            Account
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
            <i class="iconsminds-clothing-store"></i> <span class="d-inline-block">Store page</span>
          </a>
        </li>
        <li>
          <a href="products.html">
            <i class="iconsminds-project"></i> <span class="d-inline-block">Products</span>
           </a>
        </li>
      </ul>

      <ul class="list-unstyled" data-link="account">
        <li>
          <a href="account-information.html">
            <i class="iconsminds-id-card"></i> <span class="d-inline-block">Account Information</span>
          </a>
        </li>
        <li>
          <a href="bank-information.html">
            <i class="iconsminds-bank"></i> <span class="d-inline-block">Bank Information</span>
           </a>
        </li>
        <li>
          <a href="business-information.html">
            <i class="iconsminds-management"></i> <span class="d-inline-block">Business Information</span>
          </a>
        </li>
        <li>
          <a href="change-password.html">
            <i class="iconsminds-type-pass"></i> <span class="d-inline-block">Change Password</span>
          </a>
        </li>
        <li>
          <a href="notifications.html">
            <i class="iconsminds-bell"></i> <span class="d-inline-block">Notifications</span>
          </a>
        </li>
      </ul>

    </div>
  </div>
</div>