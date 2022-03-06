@extends('layouts.app')
@section('title', __('POS'))
@section('content')
  <div class="row align-self-stretch align-items-start">
    @include('pos.partials.cart')
    <div class="col-12 col-md-7 col-lg-9">
      <div class="posCategoriesSearch mb-3">
        <form action="#">
          <input class="form-control" type="text" placeholder="ابحث عن منتج">
        </form>
      </div><!-- posCategoriesSearch -->
      @include('pos.partials.linkBar')
      <div class="posCategoriesPage mb-4">
        <a href="#" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">الكل</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column overflow-hidden">
          <figure class="m-0 overflow-hidden w-100">
            <img src="https://dore-jquery.coloredstrategies.com/img/products/fruitcake-thumb.jpg" alt="" class="w-100 h-100">
          </figure>
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Advanced Materials</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Abrasives</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Adhesives, Sealants & Fillers</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Building Materials</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Cleaning Supplies</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Abrasives</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Adhesives, Sealants & Fillers</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Building Materials</span>
        </a>
        <a href="{{ route('pos.products')}}" title="#" class="d-flex align-items-center justify-content-center flex-column">
          <span class="d-flex align-items-center justify-content-center flex-grow-1 text-center">Cleaning Supplies</span>
        </a>
      </div><!-- posCategoriesPage -->
    </div><!-- col-12 -->
  </div><!-- row -->
@endsection