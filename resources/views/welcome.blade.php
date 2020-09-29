<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Go Pay</title>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="stylesheet" href="/css/all.css" />

  </head>
  <body id="app-container" class="menu-default show-spinner">
    <nav class="navbar fixed-top">
      <div class="d-flex align-items-center navbar-left">
        <a href="#" class="menu-button d-none d-md-block">
          <svg class="main" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 9 17">
            <rect x="0.48" y="0.5" width="7" height="1" />
            <rect x="0.48" y="7.5" width="7" height="1" />
            <rect x="0.48" y="15.5" width="7" height="1" />
          </svg>
          <svg class="sub" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 17">
            <rect x="1.56" y="0.5" width="16" height="1" />
            <rect x="1.56" y="7.5" width="16" height="1" />
            <rect x="1.56" y="15.5" width="16" height="1" />
          </svg>
        </a>

        <a href="#" class="menu-button-mobile d-xs-block d-sm-block d-md-none">
          <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 26 17">
            <rect x="0.5" y="0.5" width="25" height="1" />
            <rect x="0.5" y="7.5" width="25" height="1" />
            <rect x="0.5" y="15.5" width="25" height="1" />
          </svg>
        </a>

        <div class="search" data-search-path="Pages.Search.html?q=">
          <input placeholder="Search...">
          <span class="search-icon"><i class="simple-icon-magnifier"></i></span>
        </div>
        
      </div>

      <a class="navbar-logo" href="Dashboard.Default.html">
        <span class="logo d-none d-xs-block"></span>
        <span class="logo-mobile d-block d-xs-none"></span>
      </a>

      <div class="navbar-right">
        <div class="header-icons d-inline-block align-middle">
          <div class="d-none d-md-inline-block align-text-bottom mr-3">
            <div class="custom-switch custom-switch-primary-inverse custom-switch-small pl-1" data-toggle="tooltip" data-placement="left" title="{{ __('Dark Mode')}}">
              <input class="custom-switch-input" id="switchDark" type="checkbox" checked>
              <label class="custom-switch-btn" for="switchDark"></label>
            </div>
          </div>

          <div class="position-relative d-none d-sm-inline-block">
            <a href="store-client.html" class="header-icon btn btn-empty" data-toggle="tooltip"
            data-placement="top" title="Store">
              <i class="iconsminds-clothing-store"></i>
            </a>
          </div>

          <div class="position-relative d-inline-block">
            <button class="header-icon btn btn-empty" type="button" id="notificationButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              <i class="simple-icon-bell"></i>
              <span class="count">3</span>
            </button>
            <div class="dropdown-menu dropdown-menu-right mt-3 position-absolute" id="notificationDropdown">
              <div class="scroll">
                <div class="d-flex flex-row mb-3 pb-3 border-bottom">
                  <a href="#">
                    <p class="font-weight-medium mb-1">You've got a payment! 319.00 SAR from Ali Adel Ahmed </p>
                    <p class="text-muted mb-0 text-small">2020/02/09 08:31 AM</p>
                  </a>
                </div>
                <div class="d-flex flex-row mb-3 pb-3 border-bottom">
                  <a href="#">
                    <p class="font-weight-medium mb-1">You’ve got a new bill of SR30.00</p>
                    <p class="text-muted mb-0 text-small">2020/02/08 04:17 PM</p>
                  </a>
                </div>
                <div class="d-flex flex-row mb-3">
                  <a href="#">
                    <p class="font-weight-medium mb-1">You’ve got a new bill of SR130.00</p>
                    <p class="text-muted mb-0 text-small">2020/02/05 05:23 PM</p>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <button class="header-icon btn btn-empty d-none d-sm-inline-block" type="button" id="fullScreenButton">
            <i class="simple-icon-size-fullscreen"></i>
            <i class="simple-icon-size-actual"></i>
          </button>

        </div>

        <div class="user d-inline-block">
          <button class="btn btn-empty p-0" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <span class="name">Rehab AlTawari</span>
            <span><img alt="Profile Picture" src="img/profile-pic-l.jpg" /></span>
          </button>
          <div class="dropdown-menu dropdown-menu-right mt-3">
            <a class="dropdown-item" href="#">Account</a>
            <a class="dropdown-item" href="#">Support</a>
            <a class="dropdown-item" href="#">Sign out</a>
          </div>
        </div>
      </div>
    </nav>

    <div class="menu">
      <div class="main-menu">
        <div class="scroll">
          <ul class="list-unstyled">
            <li class="active">
              <a href="{{ url('/')}}" title="Dashboard">
                <i class="iconsminds-dashboard"></i>
                <span>Dashboard</span>
              </a>
            </li>
            <li>
              <a href="bills.html" title="Bills">
                <i class="iconsminds-testimonal"></i>
                Bills
              </a>
            </li>
            <li>
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
            </li>
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
                <i class="iconsminds-management"></i> <span class="d-inline-block">{{ __('Business Information') }}</span>
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

    <main>
      <div class="container-fluid">
        <div class="row">

          <div class="col-12">
            <h1>Home</h1>
            <div class="separator mb-5"></div>
          </div>

          <div class="col-12">
            <div class="row icon-cards-row mx-n3">
              <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                <a href="#" class="card mb-4">
                  <div class="card-body text-center">
                    <i class="iconsminds-coins"></i>
                    <p class="card-text font-weight-semibold mb-0">Balance</p>
                    <p class="lead text-center">832.00</p>
                  </div>
                </a>
              </div>
              <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                <a href="#" class="card mb-4">
                  <div class="card-body text-center">
                    <i class="iconsminds-coins"></i>
                    <p class="card-text font-weight-semibold mb-0">Available Balance</p>
                    <p class="lead text-center">720.00</p>
                  </div>
                </a>
              </div>
              <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                <a href="#" class="card mb-4">
                  <div class="card-body text-center">
                    <i class="iconsminds-coins"></i>
                    <p class="card-text font-weight-semibold mb-0">Pending Balance</p>
                    <p class="lead text-center">112.00</p>
                  </div>
                </a>
              </div>
              <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
                <a href="#" class="card mb-4">
                  <div class="card-body text-center">
                    <i class="iconsminds-coins"></i>
                    <p class="card-text font-weight-semibold mb-0">Settlements</p>
                    <p class="lead text-center">0.00</p>
                  </div>
                </a>
              </div>
            </div>
          </div>

          <div class="col-lg-12 col-xl-6">
            <div class="card mb-4">
              <div class="position-absolute card-top-buttons">
                <button class="btn btn-header-light icon-button" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="simple-icon-refresh"></i></button>
                <div class="dropdown-menu dropdown-menu-right mt-3">
                  <a class="dropdown-item" href="#">Sales</a>
                  <a class="dropdown-item" href="#">Orders</a>
                  <a class="dropdown-item" href="#">Refunds</a>
                </div>
              </div>
              <div class="card-body">
                <h5 class="card-title">Sales</h5>
                <div class="dashboard-line-chart chart">
                  <canvas id="salesChart"></canvas>
                </div>
              </div>
            </div>
          </div>

          <div class="col-xl-6 col-lg-12 mb-4">
            <div class="card">
              <div class="position-absolute card-top-buttons">
                <a href="#" title="View all" class="btn btn-primary btn-xs">View all</a>
              </div>
              <div class="card-body">
                <h5 class="card-title mb-3">Latest Bills</h5>
                <table class="data-table data-table-scrollable responsive nowrap" data-order="[[ 1, &quot;desc&quot; ]]">
                  <thead>
                    <tr>
                      <th class="py-2 w-85">Name</th>
                      <th class="py-2 w-15">status</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="py-2">
                        <p class="font-weight-bold">Sale - Saad Ahmed</p>
                        <p class="font-weight-normal">210.00 SAR</p>
                        <time class="text-muted text-small mb-0 font-weight-light">2020/02/12 12:33 PM</time>
                      </td>
                      <td class="py-2">
                        <span class="badge badge-danger d-block">Pending</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="py-2">
                        <p class="font-weight-bold">Sale - Ali Adel Ahmed</p>
                        <p class="font-weight-normal">319.00 SAR</p>
                        <time class="text-muted text-small mb-0 font-weight-light">2020/02/09 08:31 AM</time>
                      </td>
                      <td class="py-2">
                        <span class="badge badge-success d-block">Paid</span>
                      </td>
                    </tr>
                    <tr>
                      <td class="py-2">
                        <p class="font-weight-bold">Sale - Mazen Khaled</p>
                        <p class="font-weight-normal">90.50 SAR</p>
                        <time class="text-muted text-small mb-0 font-weight-light">2020/02/09 01:20 PM</time>
                      </td>
                      <td class="py-2">
                        <span class="badge badge-danger d-block">Pending</span>
                      </td>
                    </tr>
                    
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      
      </div>
    </main>

    <footer class="page-footer">
      <div class="footer-content">
        <div class="container-fluid">
          <div class="row">
            <div class="col-12 col-sm-6">
              <p class="mb-0 text-muted">Go Pay © All rights reserved </p>
            </div>
          </div>
        </div>
      </div>
    </footer>
    <script src="{{ mix('/js/all.js') }}"></script>

</body>

</html>