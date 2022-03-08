@extends('layouts.app')

@section('title', __('Home'))

@section('content')
  <section id="homepage">

    @php
      $settings =  Spatie\Valuestore\Valuestore::make(storage_path('app/settings.json'));
      $mobile_number = $settings->get('mobile_number');
    @endphp

    @if (!$user->verified)
      @if($user->is_uploaded_documents)
        <div class="alert alert-warning account_not_verified mb-3" role="alert">
          {{ __('Your account is being verified so that you can withdraw the collected amounts. The documentation process may take up to two business days. In the event that the documentation is not completed before :date, please contact us on :mobile', ['mobile' => $mobile_number, 'date' => $user->two_business_days]) }}
        </div>
      @else
        <div class="alert alert-warning account_not_verified mb-3" role="alert">
          {{ __('Your account is not verified. Please upload the necessary documents to verify your account and avoid delays in transferring dues.') }} {{__('To upload files, please click on the')}} <a href="/account" title="{{ __('Account Settings') }}" class="alert-link">{{ __('Account Settings') }}.</a>
        </div>
      @endif
    @endif
  
    @if (session('status'))
      <div class="alert alert-success" role="alert">{{ session('status') }}</div>
    @endif

    <div class="statisticArea">
      <div class="row row-cols-2 row-cols-sm-2 row-cols-md-3 row-cols-lg-4">
        <div class="col">
          <a href="{{ route('statement.index') }}" title="{{ __('Electronic payment Gateway Balance') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon onlinePayment_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Electronic payment Gateway Balance') }}</p>
            <span class="d-block text-center fw-bold">{{ round2($balance) }}</span>
          </a>
        </div><!-- col -->
        <div class="col">
          <a href="{{ route('statement.index') }}" title="{{ __('Pending Balance') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon balance_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Pending Balance') }}</p>
            <span class="d-block text-center fw-bold">{{ round2($user->pending_balance) }}</span>
          </a>
        </div><!-- col -->
        <div class="col">
          <a href="{{ route('statement.index') }}" title="{{ __('Paid Cash Balance') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon balance_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Paid Cash Balance') }}</p>
            <span class="d-block text-center fw-bold">{{ round2($user->paid_cash_balance) }}</span>
          </a>
        </div><!-- col -->
        <div class="col">
          <a href="{{ route('statement.index') }}" title="{{ __('Paid Bank Transfer Balance') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon balance_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Paid Bank Transfer Balance') }}</p>
            <span class="d-block text-center fw-bold">{{ round2($user->paid_bank_transfer_balance) }}</span>
          </a>
        </div><!-- col -->
        <div class="col">
          <a href="{{ route('statement.index') }}" title="{{ __('Total Paid') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon available_balance_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Total Paid') }}</p>
            <span class="d-block text-center fw-bold">{{ $total_paid }}</span>
          </a>
        </div><!-- col -->
        <div class="col">
          <a href="/bills?dont_update_statuses=true" title="{{ __('Total Bills') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon pending_balance_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Total Bills') }}</p>
            <span class="d-block text-center fw-bold">{{ $total_bills }}</span>
          </a>
        </div><!-- col -->
        <div class="col">
          <a href="/bills?statuses[]=paid&dont_update_statuses=true" title="{{ __('Total Paid Bills') }}" class="d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <div class="icon total_bills_icon"></div>
            <p class="d-block mt-3 mb-2 text-center text-capitalize">{{ __('Total Paid Bills') }}</p>
            <span class="d-block text-center fw-bold">{{ $total_paid_bills }}</span>
          </a>
        </div><!-- col -->
      </div><!-- row -->
    </div><!-- statisticArea -->

    <bills-paid-amount :user="{{$user}}"></bills-paid-amount>
    <bills-paid-count :user="{{$user}}"></bills-paid-count>
    <bills-count :user="{{$user}}"></bills-count>

    <div class="latestBills rounded-3 bg-white shadow-sm mb-3">
      <div class="title d-flex align-items-center justify-content-between mb-3">
        <span class="d-block fw-bold text-capitalize">{{__('Latest Bills') }}</span>
        @if($latest->count() > 0)
          <a href="/bills?dont_update_statuses=true" title="{{__('View all') }}" class="d-flex align-items-center justify-content-center border rounded-pill">{{__('View all') }}</a>
        @endif
      </div><!-- title -->
      @if($latest->count() > 0)
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-hover text-nowrap">
            <thead>
              <tr>
                <th scope="col" class="text-capitalize">{{__('Name') }}</th>
                <th scope="col" class="text-center text-capitalize">{{__('Values') }}</th>
                <th scope="col" class="text-center text-capitalize">{{__('Date created') }}</th>
                <th scope="col" class="text-center text-capitalize" width="10%">{{__('Status') }}</th>
              </tr>
            </thead>
            <tbody>
              @foreach($latest as $bill)
                @include('latest_bill_item')
              @endforeach
            </tbody>
          </table>
        </div>
      @else
        <div class="no_bills_available text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</div>
      @endif
    </div><!-- latestBills -->
  
    <a href="{{ route('bills.create')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create a bill')}}" class="addNewBillBtn position-fixed rounded-circle d-block shadow"></a>

  </section><!-- homepage -->

@endsection


@push('footer-scripts')
  <script src="{{ asset('new/js/chartjs/Chart.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
  <script type="text/javascript">
    Echo.channel('home')
      .listen('NewMessage', (e) => {
          console.log(e.message);
      });
  </script>
@endpush