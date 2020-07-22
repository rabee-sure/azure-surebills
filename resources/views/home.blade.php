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
          <h1>{{ __('Home') }}</h1>
          <div class="separator mb-5"></div>
        </div>

        <div class="col-12">
          <div class="row icon-cards-row mx-n3">
            <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="#" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon balance_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Balance') }}</p>
                  <p class="lead text-center">{{ $balance}}</p>
                </div>
              </a>
            </div>
            <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="#" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon available_balance_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Total Paid') }}</p>
                  <p class="lead text-center">{{ $total_paid }}</p>
                </div>
              </a>
            </div>
            <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="#" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon pending_balance_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Total Bills') }}</p>
                  <p class="lead text-center">{{ $total_bills }}</p>
                </div>
              </a>
            </div>
            <div class="col-12 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="#" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon settlements_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Total Paid Bills') }}</p>
                  <p class="lead text-center">{{ $total_paid_bills }}</p>
                </div>
              </a>
            </div>
          </div>
        </div>

        <div class="col-lg-12 col-xl-6 d-none">
          <div class="card mb-4">
            <div class="card-body">
              <h5 class="card-title">{{ __('Sales') }}</h5>
              <div class="dashboard-line-chart chart">
                <canvas id="salesChart"></canvas>
              </div>
            </div>
          </div>
        </div>

        <div class="col-xl-12 col-lg-12 mb-4">
          <div class="card">
            @if($latest->count() > 0)
              <div class="position-absolute card-top-buttons">
                <a href="{{ route('bills.index')}}" title="View all" class="btn btn-primary btn-xs"> {{__('View all') }}</a>
              </div>
            @endif
            <div class="card-body">
              <h5 class="card-title mb-3">{{__('Latest Bills') }}</h5>
                @if($latest->count() > 0)
                    <table class="data-table data-table-scrollable responsive nowrap" data-order="[[ 0, &quot;desc&quot; ]]">
                        <thead>
                          <tr>
                            <th class="py-2 w-85">{{__('Name') }}</th>
                            <th class="py-2 w-15">{{__('Status') }}</th>
                          </tr>
                        </thead>
                        <tbody>
                            @foreach($latest as $bill)
                                <tr>
                                    <td class="py-2">
                                      <a href="{{ route('bills.show', $bill) }}">
                                          <p class="font-weight-bold">{{ __('Bill')}} {{ $bill->number }} - {{ $bill->customer_name }}</p>
                                          <p class="font-weight-normal">{{ $bill->total }} {{ __('SAR')}}</p>
                                          <time class="text-muted text-small mb-0 font-weight-light">{{ $bill->created_at }}</time>
                                      </a>
                                    </td>
                                    <td class="py-2">
                                      @if($bill->status == 'pending')
                                      <span class="badge badge-pill badge-info d-inline-block">{{ __('Pending')}}</span>
                                      @endif
                                      @if($bill->status == 'paid')
                                      <span class="badge badge-pill badge-success d-inline-block">{{ __('Paid')}}</span>
                                      @endif             
                                      @if($bill->status == 'canceled')
                                      <span class="badge badge-pill badge-light d-inline-block">{{ __('Canceled')}}</span>
                                      @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                  <div class="no_bills_available">{{ __('No data available in table') }}</div>
                @endif
            </div>
          </div>
        </div>
      </div>
@endsection


@section('footer-scripts')

@endsection
