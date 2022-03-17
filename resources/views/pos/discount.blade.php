@extends('layouts.app')
@section('title', __('POS Product'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-7 col-lg-9">
      <div class="posBackBtn d-flex align-items-center justify-content-start mb-4">
        <a class="d-flex align-items-center justify-content-center rounded-3 bg-white shadow-sm" href="{{ route('pos.categories')}}"><i class="far fa-arrow-right"></i></a>
      </div><!-- posBackBtn -->
      <div class="posDiscountPage mb-4 d-flex align-items-center justify-content-center flex-column">
        <div class="title text-center d-block mb-4 fs-4">اضف خصم</div>
        <div class="row justify-content-center w-100">
          <div class="col-12 col-md-12 col-lg-5">
            <div class="selectArea mb-4 d-flex align-items-center justify-content-between">
              <label for="percentage" class="d-block mb-0 position-relative">
                <input type="radio" id="percentage" value="percentage" name="discountItem" class="w-100 h-100 position-absolute opacity-0">
                <span class="d-flex align-items-center justify-content-center rounded-3 border bg-white fw-bold">خصم نسبة</span>
              </label>
              <label for="fixed" class="d-block mb-0 position-relative">
                <input type="radio" id="fixed" value="fixed" name="discountItem" class="w-100 h-100 position-absolute opacity-0">
                <span class="d-flex align-items-center justify-content-center rounded-3 border bg-white fw-bold">خصم مبلغ</span>
              </label>
            </div><!-- selectArea -->
            <div class="inputGroup position-relative d-flex align-items-center justify-content-start flex-wrap mb-4">
              <div class="txt align-items-center justify-content-center position-absolute rounded-3" id="discountFixed" style="display: none;">ريال</div>
              <div class="txt align-items-center justify-content-center position-absolute rounded-3" id="discountPercentage"><i class="far fa-percentage"></i></div>
              <input type="tel" name="discount_value" class="form-control shadow-none bg-white border w-100 rounded-3 h-100" value="" id="Discount_Value" aria-describedby="basic-addon2">
            </div><!-- inputGroup -->
            <button type="submit" class="saveBtn btn-primary w-100 border-0 p-0 shadow-none rounded-3 fw-bold d-flex align-items-center justify-content-center">تطبيق</button>
          </div><!-- col-12 -->
        </div><!-- row -->
      </div><!-- posDiscountPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection

@push('footer-scripts')
  <script>
    $(function(){
      $("input#percentage").prop('checked',true);
    });
    $("div#discountFixed").hide();
    $('input[name="discountItem"]:radio').change(function () {
      $('#discountPercentage').toggle(this.id == 'percentage');
      $('#discountFixed').toggle(this.id == 'fixed');
    });
  </script>
@endpush

