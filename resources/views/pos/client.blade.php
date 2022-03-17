@extends('layouts.app')
@section('title', __('POS User'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-7 col-lg-9">
      <div class="posBackBtn d-flex align-items-center justify-content-start mb-4">
        <a class="d-flex align-items-center justify-content-center rounded-3 bg-white shadow-sm" href="{{ route('pos.categories')}}"><i class="far fa-arrow-right"></i></a>
      </div><!-- posBackBtn -->
      <div class="posUserDetails mb-4">
        <div class="row justify-content-center">
          <div class="col-12 col-md-12 col-lg-5">
            <div class="title text-center d-block mb-4 fs-4">معلومات العميل</div>
            <div class="inputClient border rounded-3 overflow-hidden bg-white mb-3 d-flex align-items-center justify-content-start">
              <input type="text" class="border-0 shadow-none flex-grow-1 h-100 text-body" placeholder="اسم العميل">
              <button type="button" class="p-0 h-100 border-0 shadow-none bg-light text-body border-start flex-grow-0 d-flex align-items-center justify-content-center h-100 fal fa-user-plus"></button>
            </div><!-- inputTel -->
            <div class="phoneInput overflow-hidden position-relative mb-4">
              <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
              <input name="customer_mobile" type="tel" class="h-100 shadow-none bg-white border w-100 rounded-3 text-body @error('customer_mobile') is-invalid @enderror" id="customer_mobile" placeholder="رقم الجوال"  pattern="[0-9]*" maxlength="9" inputmod="numaric">
            </div><!-- phoneInput -->
            <div class="form-group mb-3">
              <input type="email" inputmode="email" dir="ltr" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" placeholder="البريد الإلكتروني">
            </div><!-- form-group -->
            <div class="moreInfoArea mb-3">
              <div class="posMoreInfo overflow-hidden">
                <div class="form-group mb-3">
                  <input name="notes" type="text" id="Notes" placeholder="ملاحظات" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="bullding_no" type="text" id="bullding_no" placeholder="رقم المبني" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="street_name" type="text" id="street_name" placeholder="اسم الشارع" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="district" type="text" id="district" placeholder="الحي" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="city" type="text" id="city" placeholder="المدينة" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="postal_code" type="text" id="postal_code" placeholder="الرمز البريدي" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="additional_no" type="text" id="additional_no" placeholder="الرقم الاضافي للعنوان" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="other_buyer_id" type="text" id="other_buyer_id" placeholder="معرف آخر" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
                <div class="form-group mb-3">
                  <input name="vat_registration_number" type="text" id="vat_registration_number" placeholder="رقم التسجيل الضريبي" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
                </div>
              </div><!-- posMoreInfo -->
              <button type="button" class="showPosInfoBtn border-0 d-flex align-items-center justify-content-start bg-transparent p-0">معلومات إضافية</button>
            </div><!-- moreInfoArea -->
            <button type="submit" class="saveBtn btn-primary w-100 border-0 p-0 shadow-none rounded-3 fw-bold d-flex align-items-center justify-content-center">حفظ</button>
          </div><!-- col-12 -->
        </div><!-- row -->
      </div><!-- posUserDetails -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection

@push('footer-scripts')
  <script>
    // Additional Information
    $(".posMoreInfo").hide();
    $("button.showPosInfoBtn").click(function(){
      $(this).toggleClass("show");
      $(".posMoreInfo").slideToggle();
    });
  </script>
@endpush