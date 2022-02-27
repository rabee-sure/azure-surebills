@extends('layouts.app')
@section('title', __('POS Product'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-9">
      <div class="posCategoriesSearch mb-3">
        <form action="#">
          <input class="form-control" type="text" placeholder="ابحث عن منتج">
        </form>
      </div><!-- posCategoriesSearch -->
      <div class="posLinksBar mb-3">
        <a href="#" title="تعليق العملية" class="btn-primary d-flex align-items-center justify-content-center flex-column">
          <div class="icon saveIcon"></div>
          <span class="d-block mt-2">تعليق العملية</span>
        </a>
        <a href="#" title="معلومات العميل" class="btn-primary d-flex align-items-center justify-content-center flex-column">
          <div class="icon userIcon"></div>
          <span class="d-block mt-2">معلومات العميل</span>
        </a>
        <a href="#" title="خصم" class="btn-primary d-flex align-items-center justify-content-center flex-column">
          <div class="icon discountIcon"></div>
          <span class="d-block mt-2">خصم</span>
        </a>
        <a href="#" title="طباعة" class="btn-primary d-flex align-items-center justify-content-center flex-column">
          <div class="icon printIcon"></div>
          <span class="d-block mt-2">طباعة</span>
        </a>
      </div><!-- posLinksBar -->
      <div class="posProductsPage">
        <a href="{{ route('pos.categories')}}" title="#" class="d-flex align-items-center justify-content-center flex-column backCategory icon-arrow_forward"></a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/marble-cake-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/chocolate-cake-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/marble-cake-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/chocolate-cake-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/fat-rascal-thumb.jpg" alt="#" class="w-100 h-100">
          </figure>
          <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1">
            <p class="d-block text-center mb-2">Advanced Materials</p>
            <span class="d-block text-center" dir="ltr">121.00</span>
          </figcaption>
        </a>
      </div><!-- posProductsPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection