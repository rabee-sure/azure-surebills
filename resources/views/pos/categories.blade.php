@extends('layouts.app')
@section('title', __('POS'))
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
      <div class="posCategoriesPage mb-4">
        <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4">
          <div class="col align-self-stretch mb-3">
            <a href="#" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">الكل</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100 flex-grow-0">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="" class="w-100 h-100">
              </figure>
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Advanced Materials</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Abrasives</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Adhesives, Sealants & Fillers</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100 flex-grow-0">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="" class="w-100 h-100">
              </figure>
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Building Materials</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Cleaning Supplies</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Abrasives</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <figure class="m-0 overflow-hidden w-100 flex-grow-0">
                <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="" class="w-100 h-100">
              </figure>
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Adhesives, Sealants & Fillers</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Building Materials</span>
            </a>
          </div><!-- col -->
          <div class="col align-self-stretch mb-3">
            <a href="{{ route('pos.products')}}" title="#" class="h-100 overflow-hidden d-flex align-items-center justify-content-center flex-column bg-white rounded-3 shadow-sm">
              <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center p-2">Cleaning Supplies</span>
            </a>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- posCategoriesPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection