@extends('layouts.app')

@section('title', __('Products'))

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Products')}}</h1>
        <div class="text-zero top-right-button-container">
          <a href="{{ route('products.create')}}" class="btn btn-primary btn-md top-right-button">{{ __('Add Product')}}</a>
        </div><!-- text-zero -->
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/') }}">{{ __('Home')}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Products')}}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="col-12 mb-4">
      <div class="d-block d-md-inline-block">
        <div class="btn-group float-md-left mr-1 mb-1">
          <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            {{ __('Order By')}}
          </button>
          <div class="dropdown-menu">
            <a class="dropdown-item" href="#">{{ __('Most Recent')}}</a>
            <a class="dropdown-item" href="#">{{ __('Oldest')}}</a>
          </div>
        </div>
        <div class="search-sm d-inline-block float-md-left mr-1 mb-1 align-top">
          <input placeholder="{{ __('Search')}} ...">
        </div>
      </div>
      <div class="float-md-right">
        <span class="text-muted text-small mr-1">{{ __('Displaying')}} 1-10 {{ __('of')}} 210 {{ __('product')}}</span>
        <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
          20
        </button>
        <div class="dropdown-menu dropdown-menu-right">
          <a class="dropdown-item" href="#">10</a>
          <a class="dropdown-item active" href="#">20</a>
          <a class="dropdown-item" href="#">30</a>
          <a class="dropdown-item" href="#">50</a>
          <a class="dropdown-item" href="#">100</a>
        </div>
      </div>
    </div>
    <div class="separator mb-5"></div> 
  </div>
  <div class="row list disable-text-selection">
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-1.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-theme-1 position-absolute badge-top-left">{{ __('Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-3.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-danger position-absolute badge-top-left">{{ __('Not Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-4.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-theme-1 position-absolute badge-top-left">{{ __('Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-2.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-danger position-absolute badge-top-left">{{ __('Not Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-1.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-theme-1 position-absolute badge-top-left">{{ __('Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-3.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-danger position-absolute badge-top-left">{{ __('Not Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-4.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-theme-1 position-absolute badge-top-left">{{ __('Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-2.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-danger position-absolute badge-top-left">{{ __('Not Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-1.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-theme-1 position-absolute badge-top-left">{{ __('Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-3.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-danger position-absolute badge-top-left">{{ __('Not Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-4.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-theme-1 position-absolute badge-top-left">{{ __('Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-xl-3 col-lg-4 col-12 col-sm-6 mb-4">
          <div class="card">
            <div class="position-relative">
              <a href="products/1/view">
                <img class="card-img-top" src="img/card-thumb-2.jpg" alt="Card image cap">
              </a>
              <div class="edit_product">
                <button type="button"></button>
              </div><!-- edit_product -->
              <span class="badge badge-pill badge-danger position-absolute badge-top-left">{{ __('Not Shown')}}</span>
            </div>
            <div class="card-body">
              <a href="products/1/view">
                <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                <p class="text-muted text-md mb-2 font-weight-normal">545 {{ __('SAR')}}</p>
                <p class="text-muted text-small mb-0 font-weight-normal dir-ltr">2020/02/23 12:26 PM</p>
              </a>
            </div>
          </div>
        </div>
        <div class="col-12">
          <nav class="mt-4 mb-3">
            <ul class="pagination justify-content-center mb-0">
              <li class="page-item ">
                <a class="page-link first" href="#"><i class="simple-icon-control-start"></i></a>
              </li>
              <li class="page-item ">
                <a class="page-link prev" href="#"><i class="simple-icon-arrow-left"></i></a>
              </li>
              <li class="page-item active"><a class="page-link" href="#">1</a></li>
              <li class="page-item "><a class="page-link" href="#">2</a></li>
              <li class="page-item"><a class="page-link" href="#">3</a></li>
              <li class="page-item ">
                <a class="page-link next" href="#" aria-label="Next"><i class="simple-icon-arrow-right"></i></a>
              </li>
              <li class="page-item ">
                <a class="page-link last" href="#"><i class="simple-icon-control-end"></i></a>
              </li>
            </ul>
          </nav>
        </div>
      </div>
@endsection
