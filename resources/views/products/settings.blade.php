@extends('layouts.app')

@section('title', __('Products'))

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Store Settings') }}</h1>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/') }}">{{ __('Home')}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Store Settings') }}</li>
          </ol>
        </nav>
      </div>
    </div>
  </div>
    
@endsection
