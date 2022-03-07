@extends('layouts.app')
@section('title', __('POS Product'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-7 col-lg-9">
      <div class="posBackBtn d-flex align-items-center justify-content-start mb-4">
        <a class="d-flex align-items-center justify-content-center icon-arrow_forward" href="{{ route('pos.categories')}}"></a>
      </div><!-- posBackBtn -->
      <div class="posDiscountPage mb-4 d-flex align-items-center justify-content-center flex-column">
        <div class="title mb-4 font-weight-bold">اضف خصم</div>
        <div class="row justify-content-center w-100">
          <div class="col-12 col-md-12 col-lg-5">
            <div class="selectArea mb-4 d-flex align-items-center justify-content-between">
              <label for="percentage" class="d-block mb-0 position-relative">
                <input type="radio" id="percentage" value="percentage" name="discountItem" class="w-100 h-100 position-absolute">
                <span class="d-flex align-items-center justify-content-center font-weight-bold">خصم نسبة</span>
              </label>
              <label for="fixed" class="d-block mb-0 position-relative">
                <input type="radio" id="fixed" value="fixed" name="discountItem" class="w-100 h-100 position-absolute">
                <span class="d-flex align-items-center justify-content-center font-weight-bold">خصم مبلغ</span>
              </label>
            </div><!-- selectArea -->
            <div id="discountPercentage" class="discountField w-100 align-items-center justify-content-center overflow-hidden mb-4">
              <input type="tel" class="border-0 flex-grow-1 h-100 text-center">
              <span class="h-100 font-weight-bold d-flex align-items-center justify-content-center">%</span>
            </div><!-- discountPercentage -->
            <div id="discountFixed" class="discountField w-100 align-items-center justify-content-center overflow-hidden mb-4">
              <input type="tel" class="border-0 flex-grow-1 h-100 text-center">
              <span class="h-100 font-weight-bold d-flex align-items-center justify-content-center">ريال</span>
            </div><!-- discountFixed -->
            <button type="submit" class="addDiscount d-flex align-items-center justify-content-center btn-primary p-0 border-0 w-100 font-weight-bold">إضافة</button>
          </div><!-- col-12 -->
        </div><!-- row -->
      </div><!-- posDiscountPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection