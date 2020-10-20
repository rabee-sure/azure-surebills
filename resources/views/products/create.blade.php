@extends('layouts.app')

@section('title', __('Add Product'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
  <link rel="stylesheet" href="{{ asset('css/quill.snow.css') }}">
  <link rel="stylesheet" href="{{ asset('css/quill.bubble.css') }}">
@endsection

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Add Product')}}</h1>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/') }}" title="{{ __('Home')}}">{{ __('Home')}}</a>
            </li>
            <li class="breadcrumb-item"><a href="{{ url('products') }}" title="{{ __('Products')}}">{{ __('Products')}}</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Add Product')}}</li>
          </ol>
        </nav>
      </div>
    </div>
    <div class="separator mb-5"></div>
  </div>
  <div class="row">
    <div class="col-12 col-sm-12">
      <div class="card mb-4">
        <div class="card-body">
          <form>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail1">{{ __('Product Name')}}</label>
                <input type="text" class="form-control" id="inputEmail1" placeholder="{{ __('Product Name')}}">
              </div>
              <div class="form-group col-md-6">
                <label for="inputEmail2">{{ __('Category')}}</label>
                <select id="inputEmail4" class="form-control">
                  <option selected>{{ __('Choose ...')}}</option>
                  <option value="1">Category 1</option>
                  <option value="2">Category 2</option>
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail3">{{ __('Price')}}</label>
                <input type="tel" class="form-control" id="inputEmail3" placeholder="{{ __('Price')}}">
              </div>
              <div class="form-group col-md-6">
                <label for="inputEmail3">{{ __('The maximum amount to request a product each time')}}</label>
                <input type="number" class="form-control" id="inputEmail3" placeholder="0">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-md-6">
                <label for="inputEmail3">{{ __('Quantity')}} <small>( {{ __('Optional')}} )</small></label>
                <div class="form-row">
                  <div class="form-group switch_quantity col-12 col-md-6 col-lg-4 col-xl-4">
                    <p>{{ __('on / off')}}</p>
                    <div class="custom-switch custom-switch-primary">
                      <input class="custom-switch-input" id="switchS2" type="checkbox" checked>
                      <label class="custom-switch-btn" for="switchS2"></label>
                    </div>
                  </div>
                  <div class="form-group col-12 col-md-6 col-lg-8 col-xl-8">
                    <input type="number" class="form-control" id="inputEmail3" placeholder="0">
                  </div>
                </div>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group col-12">
                <label for="inputEmail3">{{ __('Description')}}</label>
                <div class="html-editor" id="quillEditor"></div>
              </div>
              <div class="form-group col-12">
                <label for="inputEmail4">{{ __('Product Images')}}</label>
                <form action="/file-upload">
                  <div class="dropzone"> </div>
                </form>
              </div>
              <div class="form-group col-12">
                <label for="inputEmail4">{{ __('Product Status')}}</label>
                <div class="custom-switch custom-switch-primary mb-2 custom-switch-small">
                  <input class="custom-switch-input" id="switchS2" type="checkbox" checked>
                  <label class="custom-switch-btn" for="switchS2"></label>
                </div>
              </div>
            </div>
            <button type="submit" class="btn btn-primary d-inline-block mt-2">{{ __('Save')}}</button>
            <button type="submit" class="btn btn-danger d-inline-block mt-2">{{ __('Cancel')}}</button>
          </form>
        </div>
      </div>
    </div>
  </div>
@endsection

@push('footer-scripts')
  <script src="{{ asset('js/dropzone.min.js') }}"></script>
  <script src="{{ asset('js/quill.min.js') }}"></script>
@endpush
