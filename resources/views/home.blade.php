 @extends('layouts.app')

@section('title', 'Page Title')

@section('content')
      @if (session('status'))
          <div class="alert alert-success" role="alert">
              {{ session('status') }}
          </div>
      @endif
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
@endsection