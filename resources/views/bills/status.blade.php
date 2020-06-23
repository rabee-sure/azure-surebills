@extends('layouts.bill')

@section('title', 'Page Title')

@section('content')
  <div class="single_bill_page">
    <div class="container">
      <div class="row  justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-6">
          <div class="logo">
            <img src="https://www.sure.com.sa/wp-content/uploads/2019/10/21.png" alt="logo">
          </div><!-- logo -->
          <div class="title">
            <span>{{ $bill->business_name}}</span>
            <div>
              <p>Riyadh, Saudi Arabia</p>
              <b>0551234567</b>
            </div>
          </div><!-- title -->

            @if($bill->status == 'expired')
              <div class="alert alert-secondary" role="alert">
                this bill #{{ $bill->bay_id}} has been expired
              </div>
            @endif
            @if($bill->status == 'paid')
              <div class="alert alert-success" role="alert">
                this bill #{{ $bill->pay_id}} paid successfully
              </div>
            @endif
            @if($bill->status == 'canceled')
              <div class="alert alert-danger" role="alert">
                his bill #{{ $bill->bay_id}} has been canceled
              </div>
            @endif

        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- single_bill_page -->
@endsection


@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\PayBillRequest', '#bill_bay') !!}
@endsection
