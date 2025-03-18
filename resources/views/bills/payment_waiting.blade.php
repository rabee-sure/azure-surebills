@extends('layouts.bill')
@section('title', __('payment waiting'))
@section('content')

  <div class="loading"></div>

  <div class="singlebBillSimple_page d-flex align-items-center justify-content-center flex-column">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-12 col-md-5">
          <div class="all_bill_page">
            <div class="change_lang d-flex align-items-center justify-content-end w-100 mb-1">
            </div><!-- change_lang -->
            <div class="single_bill_content">
              <div class="countdown alert alert-warning d-flex align-items-center justify-content-center" id="new_countdown">
                <p class="mb-0">{{ __('the bill will expire in')}}</p>
                <span id="hm_timer"></span>
              </div><!-- countdown -->
            </div><!-- single_bill_content -->
          </div><!-- all_bill_page -->
        </div><!-- col-12 -->
      </div><!-- row -->
    </div><!-- container -->
  </div><!-- singlebBillSimple_page -->

  @include($iframe)

@endsection

@push('footer-scripts')



@endpush
