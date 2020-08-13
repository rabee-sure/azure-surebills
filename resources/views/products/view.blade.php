@extends('layouts.app')

@section('title', __('Product Details'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/baguetteBox.min.css') }}" />
@endsection

@section('content')

 
        <div class="row">
          <div class="col-12 col-md-12 col-xl-8 col-left">
            <div class="card mb-4">
              <div class="card-body">
                <div class="glide details">
                  <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides gallery">
                      <li class="glide__slide">
                        <a href="../../img/parkin.jpg"><img alt="detail" src="../../img/parkin.jpg" class="responsive border-0 border-radius img-fluid mb-3" /></a>
                      </li>
                      <li class="glide__slide">
                        <a href="../../img/napoleonshat.jpg"><img alt="detail" src="../../img/napoleonshat.jpg" class="responsive border-0 border-radius img-fluid mb-3" /></a>
                      </li>
                      <li class="glide__slide">
                        <a href="../../img/marble-cake.jpg"><img alt="detail" src="../../img/marble-cake.jpg" class="responsive border-0 border-radius img-fluid mb-3" /></a>
                      </li>
                      <li class="glide__slide">
                        <a href="../../img/fruitcake.jpg"><img alt="detail" src="../../img/fruitcake.jpg" class="responsive border-0 border-radius img-fluid mb-3" /></a>
                      </li>
                      <li class="glide__slide">
                        <a href="../../img/magdalena.jpg"><img alt="detail" src="../../img/magdalena.jpg" class="responsive border-0 border-radius img-fluid mb-3" /></a>
                      </li>
                      <li class="glide__slide">
                        <a href="../../img/tea-loaf.jpg"><img alt="detail" src="../../img/tea-loaf.jpg" class="responsive border-0 border-radius img-fluid mb-3" /></a>
                      </li>
                    </ul>
                  </div>
                </div>
                <div class="glide thumbs">
                  <div class="glide__track" data-glide-el="track">
                    <ul class="glide__slides">
                      <li class="glide__slide">
                        <img alt="thumb" src="../../img/parkin-thumb.jpg" class="responsive border-0 border-radius img-fluid" />
                      </li>
                      <li class="glide__slide">
                        <img alt="thumb" src="../../img/napoleonshat-thumb.jpg" class="responsive border-0 border-radius img-fluid" />
                      </li>
                      <li class="glide__slide">
                        <img alt="thumb" src="../../img/marble-cake-thumb.jpg" class="responsive border-0 border-radius img-fluid" />
                      </li>
                      <li class="glide__slide">
                        <img alt="thumb" src="../../img/fruitcake-thumb.jpg" class="responsive border-0 border-radius img-fluid" />
                      </li>
                      <li class="glide__slide">
                        <img alt="thumb" src="../../img/magdalena-thumb.jpg" class="responsive border-0 border-radius img-fluid" />
                      </li>
                      <li class="glide__slide">
                        <img alt="thumb" src="../../img/tea-loaf-thumb.jpg" class="responsive border-0 border-radius img-fluid" />
                      </li>
                    </ul>
                  </div>
                  <div class="glide__arrows" data-glide-el="controls">
                    <button class="glide__arrow glide__arrow--left" data-glide-dir="<"><i class="simple-icon-arrow-left"></i></button>
                    <button class="glide__arrow glide__arrow--right" data-glide-dir=">"><i class="simple-icon-arrow-right"></i></button>
                  </div>
                </div>
              </div>
            </div>

            <div class="card mb-4">
              <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs " role="tablist">
                  <li class="nav-item">
                    <a class="nav-link active" id="first-tab" data-toggle="tab" href="#first" role="tab" aria-controls="first" aria-selected="true">Details</a>
                  </li>
                  <li class="nav-item">
                    <a class="nav-link" id="second-tab" data-toggle="tab" href="#second" role="tab" aria-controls="second" aria-selected="false">Comments(19)</a>
                  </li>
                </ul>
              </div>
              <div class="card-body">
                <div class="tab-content">
                  <div class="tab-pane fade show active" id="first" role="tabpanel" aria-labelledby="first-tab">
                    <p>
                      Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.
                    </p>
                  </div>
                  <div class="tab-pane fade" id="second" role="tabpanel" aria-labelledby="second-tab">
                    <div class="d-flex flex-row mb-3 border-bottom justify-content-between">
                      <a href="#">
                        <img src="../../img/profile-pic-l-7.jpg" alt="Mimi Carreira" class="img-thumbnail border-0 rounded-circle list-thumbnail align-self-center xsmall" />
                      </a>
                      <div class="pl-3 flex-grow-1">
                        <a href="#">
                          <p class="font-weight-medium mb-0">Reem Ahmed</p>
                          <p class="text-muted mb-0 text-small">Two Days Ago</p>
                        </a>
                        <p class="mt-3">Pellentesque quis cursus mauris.</p>
                      </div>
                      <div class="comment-likes">
                        <span class="post-icon"><a href="#"><span>12 Likes</span> <i class="simple-icon-heart ml-2"></i></a></span>
                      </div>
                    </div>
                    <div class="d-flex flex-row mb-3 border-bottom justify-content-between">
                      <a href="#">
                        <img src="../../img/profile-pic-l-3.jpg" alt="Kathryn Mengel" class="img-thumbnail border-0 rounded-circle list-thumbnail align-self-center xsmall" />
                      </a>
                      <div class="pl-3 flex-grow-1">
                        <a href="#">
                          <p class="font-weight-medium mb-0">Abdulrhman Majed</p>
                          <p class="text-muted mb-0 text-small">Two Days Ago</p>
                        </a>
                        <p class="mt-3">
                          Vestibulum ante ipsum primis in faucibus orci luctus et ultrices posuere
                          cubilia Curae; Pellentesque quis cursus mauris. Nam in ornare erat.
                          Vestibulum convallis enim ac massa dapibus consectetur. Maecenas
                          facilisis eros ac felis mattis, eget auctor sapien varius.
                        </p>
                      </div>
                      <div class="comment-likes">
                        <span class="post-icon"><a href="#"><span>2 Likes</span> <i class="simple-icon-heart ml-2"></i></a></span>
                      </div>
                    </div>
                    <div class="comment-contaiener">
                      <div class="input-group">
                        <input type="text" class="form-control" placeholder="Add a comment">
                        <div class="input-group-append">
                          <button class="btn btn-secondary" type="button">
                            <span class="d-inline-block">Send</span> <i class="simple-icon-arrow-right ml-2"></i>
                          </button>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-12 col-xl-4 col-right">
            <div class="card mb-4">
              <div class="position-absolute card-top-buttons">
                <button class="btn btn-header-light icon-button"><i class="simple-icon-note"></i></button>
              </div>
              <div class="card-body">
                <div class="mb-3">
                  <div class="post-icon mr-3 d-inline-block">
                    <a href="#"><i class="simple-icon-heart mr-1"></i></a>
                    <span>4 Likes</span>
                  </div>
                  <div class="post-icon d-inline-block">
                    <i class="simple-icon-bubble mr-1"></i>
                    <span>1 Comment</span>
                  </div>
                </div>
                <h2 class="mb-3 font-weight-bold">
                  name of product here to show only
                </h2>
                <h4>454 SAR</h4>
                <br>
                <div class="add_cart_area">
                  <div class="product_count">
                    <button onclick="this.parentNode.querySelector('input[type=number]').stepDown()" ></button>
                    <input class="quantity" min="0" name="quantity" value="1" type="number">
                    <button onclick="this.parentNode.querySelector('input[type=number]').stepUp()" class="plus"></button>
                  </div><!-- product_count -->
                  <button type="button" class="btn btn-primary default">PAY</button>
                </div><!-- add_cart_area -->
                <br>
                <p class="text-muted text-small mb-2">Tags</p>
                <p class="mb-3">
                  <a href="#" class="m-1 d-inline-block">
                    <span class="badge badge-pill badge-outline-theme-2 mb-1">Cake</span>
                  </a>
                  <a href="#" class="m-1 d-inline-block">
                    <span class="badge badge-pill badge-outline-theme-2 mb-1">Sweets</span>
                  </a>
                  <a href="#" class="m-1 d-inline-block">
                     <span class="badge badge-pill badge-outline-theme-2 mb-1">Chocolate</span>
                  </a>
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
      
@endsection

@push('footer-scripts')
  <script src="{{ asset('js/baguetteBox.min.js') }}"></script>
@endpush
