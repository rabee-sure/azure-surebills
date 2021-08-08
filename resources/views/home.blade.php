@extends('layouts.app')

@section('title', __('Home'))

@section('content')
    @php
        $settings =  Spatie\Valuestore\Valuestore::make(storage_path('app/settings.json'));
        $mobile_number = $settings->get('mobile_number');
    @endphp
      @if (!auth()->user()->verified)
        @if(auth()->user()->is_uploaded_documents)
          <div class="alert alert-warning account_not_verified mb-5" role="alert">
            {{ __('Your account is being verified so that you can withdraw the collected amounts. The documentation process may take up to two business days. In the event that the documentation is not completed before :date, please contact us on :mobile', ['mobile' => $mobile_number, 'date' => auth()->user()->two_business_days]) }}
          </div>
        @else
        <div class="alert alert-warning account_not_verified mb-5" role="alert">
          {{ __('Your account is not verified. Please upload the necessary documents to verify your account and avoid delays in transferring dues.') }}
          {{__('To upload files, please click on the')}}
          <a href="/account">{{ __('Account Settings') }}.</a>
          </div>
        @endif
      @endif
      @if (session('status'))
          <div class="alert alert-success" role="alert">
              {{ session('status') }}
          </div>
      @endif
      <div class="row">
        <div class="col-12">
          <div class="row icon-cards-row mx-n3">
            <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="{{ route('statement.index') }}" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon balance_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Balance') }}</p>
                  <p class="lead text-center">{{ round2(auth()->user()->balance) }}</p>
                </div>
              </a>
            </div>
            <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="{{ route('statement.index') }}" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon available_balance_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Total Paid') }}</p>
                  <p class="lead text-center">{{ $total_paid }}</p>
                </div>
              </a>
            </div>
            <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="/bills?dont_update_statuses=true" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon pending_balance_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Total Bills') }}</p>
                  <p class="lead text-center">{{ $total_bills }}</p>
                </div>
              </a>
            </div>
            <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
              <a href="/bills?statuses[]=paid&dont_update_statuses=true" class="card mb-4">
                <div class="card-body text-center">
                  <div class="statistic_icon settlements_icon"></div>
                  <p class="card-text font-weight-semibold mb-0">{{ __('Total Paid Bills') }}</p>
                  <p class="lead text-center">{{ $total_paid_bills }}</p>
                </div>
              </a>
            </div>
          </div>
        </div>

        <div class="col-12">
          <bills-paid-amount :user="{{auth()->user()}}"></bills-paid-amount>
          <bills-paid-count :user="{{auth()->user()}}"></bills-paid-count>
          <bills-count :user="{{auth()->user()}}"></bills-count>
        </div>
        
        <div class="col-xl-12 col-lg-12 mb-4">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title mb-3">{{__('Latest Bills') }}</h5>
              
              @if($latest->count() > 0)
                <div class="position-absolute card-top-buttons p-0">
                  <a href="/bills?dont_update_statuses=true" title="View all" class="btn btn-primary btn-xs"> {{__('View all') }}</a>
                </div>
              @endif
            @if($latest->count() > 0)
              <div class="table-responsive">
                <table class="table table-striped text-nowrap">
                  <thead>
                    <tr>
                      <th scope="col">{{__('Name') }}</th>
                      <th scope="col">{{__('Values') }}</th>
                      <th scope="col">{{__('Date created') }}</th>
                      <th scope="col" width="10%">{{__('Status') }}</th>
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
                <div class="no_bills_available">{{ __('No Bill Matched The Given Criteria.') }}</div>
              @endif
            </div>
          </div>
        </div>
      </div>
  <a href="{{ route('bills.create')}}" data-toggle="tooltip" data-placement="top" title="{{ __('Create a bill')}}" class="add_bill_button"></a>

@endsection


@push('footer-scripts')
<script src="{{ asset('js/Chart.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
<script type="text/javascript">
  Echo.channel('home')
    .listen('NewMessage', (e) => {
        console.log(e.message);
    });
</script>
@endpush
