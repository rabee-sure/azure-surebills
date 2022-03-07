@extends('layouts.app')

@section('title', __('Reports'))

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Reports')}}</h1>
        <div class="top-right-button-container">
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Reports')}}</li>
        </ol>
      </nav>
      <div class="separator mt-3 mb-5"></div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="homepage_blocks icon-cards-row">
      <div class="item">
        <a href="{{ route('reports.merchants-outstanding') }}" class="card mb-3">
          <div class="card-body text-center">
            <div class="statistic_icon balance_icon"></div>
            <p class="card-text font-weight-semibold mb-2">{{ __('Merchants Outstanding') }}</p>
            <p class="lead text-center">{{ __('Request Report') }}</p>
          </div>
        </a>
      </div>
    </div>
  </div>
  </div>
@endsection