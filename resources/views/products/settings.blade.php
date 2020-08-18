@extends('layouts.app')

@section('title', __('Products'))

@section('content') 

        <div class="row">
          <div class="col-12 mb-5 relative">
            <img class="social-header card-img" src="../../img/social-header.jpg" />
            <div class="edit_cover">
              <button type="button" data-toggle="modal" data-target="#edit_cover_Modal"></button>
            </div><!-- edit_cover -->
            <!-- Modal -->
            <div class="modal fade" id="edit_cover_Modal" tabindex="-1" role="dialog" aria-hidden="true">
              <div class="modal-dialog" role="document">
                <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title" id="edit_cover_ModalLabel">Edit Cover</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true">&times;</span>
                    </button>
                  </div>
                  <div class="modal-body">
                    <div class="form-group">
                      <label for="inputEmail8">Cover Image</label>
                      <div class="custom-file">
                        <input type="file" class="custom-file-input" id="inputEmail8">
                        <label class="custom-file-label" for="inputEmail8">Choose file</label>
                      </div>
                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
                  </div>
                </div>
              </div>
            </div>
            <!-- Modal -->
          </div>
          <div class="col-12 col-lg-5 col-xl-4 col-left">
            <img alt="Profile" src="../../img/logoCN.png" class="img-thumbnail card-img social-profile-img">
            <div class="card mb-4">
              <div class="card-body">
                <div class="edit_store">
                  <button type="button" data-toggle="modal" data-target="#edit_store_Modal"></button>
                </div><!-- edit_store -->
                <!-- Modal -->
                <div class="modal fade" id="edit_store_Modal" tabindex="-1" role="dialog" aria-hidden="true">
                  <div class="modal-dialog" role="document">
                    <div class="modal-content">
                      <div class="modal-header">
                        <h5 class="modal-title" id="edit_store_ModalLabel">Edit Store</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                          <span aria-hidden="true">&times;</span>
                        </button>
                      </div>
                      <div class="modal-body">
                        <div class="form-group">
                          <label for="inputEmail8">logo</label>
                          <div class="custom-file">
                            <input type="file" class="custom-file-input" id="inputEmail8">
                            <label class="custom-file-label" for="inputEmail8">Choose file</label>
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="inputEmail7">Name Store</label>
                          <div class="custom-file">
                            <input type="text" class="form-control" id="inputEmail7" placeholder="Name Store">
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="inputEmail7">BIO</label>
                          <div class="custom-file">
                            <input type="text" class="form-control" id="inputEmail7" placeholder="BIO">
                          </div>
                        </div>
                        <div class="form-group">
                          <label for="inputEmail7">Social Media</label>
                          <div class="input-group mb-2">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="facebook"><i class="simple-icon-social-facebook"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Facebook Link" aria-label="facebook" aria-describedby="facebook">
                          </div>
                          <div class="input-group mb-2">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="twitter"><i class="simple-icon-social-twitter"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Twitter Link" aria-label="twitter" aria-describedby="twitter">
                          </div>
                          <div class="input-group mb-2">
                            <div class="input-group-prepend">
                              <span class="input-group-text" id="instagram"><i class="simple-icon-social-instagram"></i></span>
                            </div>
                            <input type="text" class="form-control" placeholder="Instagram Link" aria-label="instagram" aria-describedby="instagram">
                          </div>
                        </div>
                      </div>
                      <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save</button>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- Modal -->
                <div class="text-center pt-4">
                  <p class="list-item-heading pt-2">Noura Cake</p>
                </div>
                <p class="mb-3">
                  Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.
                </p>
                <p class="text-muted text-small mb-2">Contact</p>
                <div class="social-icons">
                  <ul class="list-unstyled list-inline">
                    <li class="list-inline-item">
                      <a href="#"><i class="simple-icon-social-facebook"></i></a>
                    </li>
                    <li class="list-inline-item">
                      <a href="#"><i class="simple-icon-social-twitter"></i></a>
                    </li>
                    <li class="list-inline-item">
                      <a href="#"><i class="simple-icon-social-instagram"></i></a>
                    </li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
          <div class="col-12 col-lg-7 col-xl-8 col-right">
            <div class="row listing-card-container">
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/marble-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/goose-breast-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/chocolate-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/souffle-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/marble-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/goose-breast-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/chocolate-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/souffle-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/marble-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/goose-breast-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/chocolate-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/souffle-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/marble-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/goose-breast-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
              <div class="col-sm-6 col-lg-6 col-xl-4 col-12 mb-4">
                <div class="card">
                  <div class="position-relative">
                    <a href="product.html">
                      <img class="card-img-top" src="../../img/chocolate-cake-thumb.jpg" alt="Card image cap">
                    </a>
                  </div>
                  <div class="card-body">
                    <a href="product.html">
                      <p class="list-item-heading mb-2 pt-1 font-weight-bold">Cheesecake</p>
                      <p class="text-muted text-md mb-2 font-weight-normal">545 SAR</p>
                    </a>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
    
@endsection
