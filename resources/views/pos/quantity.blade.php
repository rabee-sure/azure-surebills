@extends('layouts.app')
@section('title', __('POS Product'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-7 col-lg-9">
      <div class="posBackBtn d-flex align-items-center justify-content-start mb-4">
        <a class="d-flex align-items-center justify-content-center rounded-3 bg-white shadow-sm" href="{{ route('pos.products')}}"><i class="far fa-arrow-right"></i></a>
      </div><!-- posBackBtn -->
      <div class="posProductPage mb-4 d-flex align-items-center justify-content-center flex-column">
        <figure class="d-block mb-3 overflow-hidden mw-100 rounded-3">
          <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="" class="w-100 h-100">
        </figure>
        <figcaption class="mb-5">
          <span class="d-block font-weight-bold mb-3">Dynatron™ Boxed Tack Cloth, 812</span>
          <p class="d-flex align-items-end justify-content-center" dir="ltr">33.00 <small>SAR</small></p>
        </figcaption>
        <div class="qtyItem mt-1 d-flex align-items-center justify-content-end mb-3">
          <button class="p-0 border-0 shadow-none rounded-1 d-flex align-items-center justify-content-center text-white fal fa-minus" onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
          <input class="quantity bg-transparent p-0 border-0 text-center" min="1" name="quantity" value="1" type="number" readonly>
          <button class="p-0 border-0 shadow-none rounded-1 d-flex align-items-center justify-content-center text-white fal fa-plus" onclick="this.parentNode.querySelector('input[type=number]').stepUp()"></button>
        </div><!-- qtyItem -->
        <button type="submit" class="addItem mw-100 btn-primary d-flex align-items-center justify-content-center rounded-3 shadow-none p-0 border-0 fw-bold">إضافة</button>
      </div><!-- posProductPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection