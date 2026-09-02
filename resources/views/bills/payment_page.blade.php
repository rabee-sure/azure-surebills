@extends('layouts.bill')
@section('title', __('Bill No.') . ' ' . $bill->number)

@section('content')

  {{-- Vue mounts to #app; keep it off the hosted-field form or session iframes get wiped. --}}
  <div id="app" hidden></div>
  <div class="simple_bill_page py-4 min-vh-100">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-lg-5">

        <div class="card">
          <div class="card-body p-3 position-relative">

            <div class="load_form active">
              <div class="spinner-border text-muted"></div>
            </div>

            <div class="d-flex align-items-center justify-content-between">
              @if($bill->user->logo)
                <div class="d-flex align-items-center justify-content-start">
                  <img src="{{ $bill->user->logo_url }}" alt="{{ $bill->user->business_name }}" class="rounded-circle" width="30px" height="30px">
                  <span class="mr-2 d-block">{{ $bill->user->business_name }}</span>
                </div><!-- d-flex -->
              @endif
              @if($bill->application && $bill->is_redirect)
                <a href="{{ $bill->back_url}}" title="{{ __('Back') }}" class="btn btn-sm btn-label-secondary waves-effect">{{ __('Back') }}</a>
              @endif
            </div><!-- d-flex -->

            <h4 class="d-flex align-items-center my-5 {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-center' : 'justify-content-center'}} gap-1 m-0">
              {{ $bill->total }} <i class="sar-icon"></i>
            </h4>

            @if(!$bill->is_expired && $bill->remaining_time_hours['hours'] == '00' && $bill->remaining_time_hours['days'] == 0)
              <div class="countdown alert alert-warning d-flex align-items-center justify-content-center gap-1 mb-3" id="new_countdown">
                <p class="mb-0">{{ __('the bill will expire in')}}</p>
                <span id="hm_timer"></span>
              </div><!-- countdown -->
            @endif

            <div id="status"></div>

            @include('bills.partials.payment_form')


          </div><!-- card-body -->
        </div><!-- card -->


        </div><!-- col -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- simple_bill_page -->

@endsection

@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/js/app.js') }}"></script>
  <script>
    @if($bill->user->settings->api_bill_style && $bill->application_id)
    if (typeof window.Echo !== 'undefined') {
      Echo.channel('bill.{{$bill->id}}').listen('BillStatusUpdated', (e) => {
        switch(e.bill.status) {
          case "paid":
            $("#payment_area").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-success" role="alert">{{ __("this bill is paid successfully") }}</div>');
            break;
          case "canceled":
            $("#payment_area").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __("this bill is canceled") }}</div>');
            break;
          case "expired":
            $("#payment_area").remove();
            $("#status").empty();
            $("#status").append('<div class="alert alert-danger" role="alert">{{ __('this bill has been expired', ['number' => $bill->number ]) }}</div>');
            break;
          default:
            $("#payment_area").remove();
            $("#status").empty();
        }
      });
    }
    @endif
  </script>
@endpush
