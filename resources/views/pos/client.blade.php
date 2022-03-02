@extends('layouts.app')
@section('title', __('POS User'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-9">
      <div class="posBackBtn d-flex align-items-center justify-content-start mb-4">
        <a class="d-flex align-items-center justify-content-center icon-arrow_forward" href="{{ route('pos.categories')}}"></a>
      </div><!-- posBackBtn -->
      <div class="posUserDetails mb-4">
        <div class="row justify-content-center">
          <div class="col-12 col-md-5">
            <div class="title text-center font-weight-bold d-block mb-4">معلومات العميل</div>
            <div class="inputClient mb-3 d-flex align-items-center justify-content-start">
              <input type="text" class="border-0 flex-grow-1 h-100" placeholder="اسم العميل">
              <button type="button" class="p-0 d-flex align-items-center justify-content-center h-100 icon-user-plus"></button>
            </div><!-- inputTel -->
            <div class="inputTel mb-3 d-flex align-items-center justify-content-start">
              <input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="9" dir="ltr" class="border-0 flex-grow-1 h-100" placeholder="رقم الجوال">
              <span class="h-100 font-weight-bold d-flex align-items-center justify-content-center" dir="ltr">+966</span>
            </div><!-- inputTel -->
            <input type="email" inputmode="email" dir="ltr" class="inputMail w-100 mb-3" placeholder="البريد الإلكتروني">
            <div class="moreInfoArea mb-3">
              <div class="posMoreInfo overflow-hidden">
                <div class="form-group">
                  <input name="notes" type="text" id="Notes" placeholder="ملاحظات" class="form-control">
                </div>
                <div class="form-group">
                  <input name="bullding_no" type="text" id="bullding_no" placeholder="رقم المبني" class="form-control">
                </div>
                <div class="form-group">
                  <input name="street_name" type="text" id="street_name" placeholder="اسم الشارع" class="form-control">
                </div>
                <div class="form-group">
                  <input name="district" type="text" id="district" placeholder="الحي" class="form-control">
                </div>
                <div class="form-group">
                  <input name="city" type="text" id="city" placeholder="المدينة" class="form-control">
                </div>
                <div class="form-group">
                  <input name="postal_code" type="text" id="postal_code" placeholder="الرمز البريدي" class="form-control">
                </div>
                <div class="form-group">
                  <input name="additional_no" type="text" id="additional_no" placeholder="الرقم الاضافي للعنوان" class="form-control">
                </div>
                <div class="form-group">
                  <input name="other_buyer_id" type="text" id="other_buyer_id" placeholder="معرف آخر" class="form-control">
                </div>
                <div class="form-group">
                  <input name="vat_registration_number" type="text" id="vat_registration_number" placeholder="رقم التسجيل الضريبي" class="form-control">
                </div>
              </div><!-- posMoreInfo -->
              <button type="button" class="showPosInfo bg-transparent p-0 border-0 d-flex align-items-center justify-content-start">معلومات إضافية</button>
            </div><!-- moreInfoArea -->
            <button type="submit" class="saveBtn btn-primary w-100 border-0 p-0 font-weight-bold d-flex align-items-center justify-content-center">حفظ</button>
          </div><!-- col-12 -->
        </div><!-- row -->
      </div><!-- posUserDetails -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection