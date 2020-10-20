@extends('layouts.app')
@section('title', __('order'))
@section('content')

<div class="row">
  <div class="col-12">
    <div class="mb-2">
      <h1>{{ __('Order')}} #310</h1>
      <div class="top-right-button-container">
        <a href="{{ url('/bills') }}" class="btn btn-dark btn-md top-right-button bill_statue_order"><p>{{ __('Bill') }} #193 - </p><span class="badge badge-pill badge-success">{{ __('Paid') }}</span></a>
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}" title="{{ __('Home')}}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/orders') }}" title="{{ __('Orders') }}">{{ __('Orders') }}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Order') }} #310</li>
        </ol>
      </nav>
    </div>
    <div class="separator mb-5"></div>
  </div>
  </div>
  <div class="row">
    <div class="col-12 col-ms-12 col-md-6 col-lg-8 col-xl-8 list mb-4" data-check-all="checkAll">

      <div class="card d-flex flex-row mb-3">
          <a class="d-flex" href=".html">
              <img src="../../img/fat-rascal-thumb.jpg" alt="Fat Rascal"
                  class="list-thumbnail responsive border-0 card-img-left" />
          </a>
          <div class="pl-2 d-flex flex-grow-1 min-width-zero">
              <div
                  class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                  <a href=".html" class="w-40 w-sm-100">
                      <p class="list-item-heading mb-0 truncate">Fat Rascal</p>
                  </a>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">215 SAR</p>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">5 X</p>
              </div>
              
          </div>
      </div>


      <div class="card d-flex flex-row mb-3">
          <a class="d-flex" href=".html">
              <img src="../../img/goose-breast-thumb.jpg" alt="Goose Breast"
                  class="list-thumbnail responsive border-0 card-img-left" />
          </a>
          <div class="pl-2 d-flex flex-grow-1 min-width-zero">
              <div
                  class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                  <a href=".html" class="w-40 w-sm-100">
                      <p class="list-item-heading mb-0 truncate">Goose Breast</p>
                  </a>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">358 SAR</p>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">8 X</p>
              </div>
              
          </div>
      </div>

      <div class="card d-flex flex-row mb-3">
          <a class="d-flex" href=".html">
              <img src="../../img/petit-gateau-thumb.jpg" alt="Petit Gateau"
                  class="list-thumbnail responsive border-0 card-img-left" />
          </a>
          <div class="pl-2 d-flex flex-grow-1 min-width-zero">
              <div
                  class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                  <a href=".html" class="w-40 w-sm-100">
                      <p class="list-item-heading mb-0 truncate">Petit Gâteau</p>
                  </a>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">985 SAR</p>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">2 X</p>
              </div>
              
          </div>
      </div>

      <div class="card d-flex flex-row mb-3">
          <a class="d-flex" href=".html">
              <img src="../../img/salzburger-nockerl-thumb.jpg" alt="Salzburger Nockerl"
                  class="list-thumbnail responsive border-0 card-img-left" />
          </a>
          <div class="pl-2 d-flex flex-grow-1 min-width-zero">
              <div
                  class="card-body align-self-center d-flex flex-column flex-lg-row justify-content-between min-width-zero align-items-lg-center">
                  <a href=".html" class="w-40 w-sm-100">
                      <p class="list-item-heading mb-0 truncate">Salzburger Nockerl</p>
                  </a>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">852 SAR</p>
                  <p class="mb-0 text-muted text-small w-15 w-sm-100">14 X</p>
              </div>
              
          </div>
        </div>

  </div>

    <div class="col-12 col-sm-12 col-md-6 col-lg-4 col-xl-4 col-right">
      <div class="card d-flex flex-row mb-3">
        <div class="card-body">
          <div class="text-center">
            <span class="badge badge-warning d-inline-block badge_order_status">فى الإنتظار <button type="button"><i class="simple-icon-note"></i></button></span>
          </div>
          <div class="separator mb-4"></div>
          <div class="sds">
            <h3 class="list-item-heading font-weight-bold">Client :</h3>
            <p class="text-muted text-md mb-2 font-weight-normal">Saad Ahmed</p>
            <p class="text-muted text-md mb-2 font-weight-normal">+966554102993</p>
            <p class="text-muted text-md mb-2 font-weight-normal">saadh@gmail.com</p>
            <h3 class="mt-4 list-item-heading font-weight-bold">Address :</h3>
            <p class="text-muted text-md mb-2 font-weight-normal">Saudi</p>
            <p class="text-muted text-md mb-2 font-weight-normal">Riyadh</p>
            <p class="text-muted text-md mb-2 font-weight-normal">Al-Rawda, house No. 5 Street 192</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

@endsection
