@extends('layouts.app')
@section('title', __('POS Product'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-7 col-lg-9">
      <div class="posCategoriesSearch mb-3">
        <form action="#">
          <input class="form-control shadow-none border rounded-3 text-body" type="text" placeholder="ابحث عن منتج">
        </form>
      </div><!-- posCategoriesSearch -->
      @include('pos.partials.linkBar')
      <div class="posProductsPage mb-4">
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4">
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.categories')}}" title="#" class="backCategory h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm"> <i class="far fa-arrow-right"></i></a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/marble-cake-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/chocolate-cake-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/marble-cake-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/chocolate-cake-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.quantity')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/fat-rascal-thumb.jpg" alt="#" class="w-100 h-100">
              </figure>
              <figcaption class="d-flex align-items-center justify-content-center flex-column flex-grow-1 p-2">
                <p class="d-block text-center mb-2">Advanced Materials</p>
                <span class="d-block text-center" dir="ltr">121.00</span>
              </figcaption>
            </a>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- posProductsPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection