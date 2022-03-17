@extends('layouts.app')

@section('title', __('Reports'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Reports') }}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section id="reportsIndexPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Reports') }}</h1>
    </div><!-- title -->
    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4">
      <div class="col">
        <a href="{{ route('reports.merchants-outstanding') }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm p-3 position-relative">
          <span class="d-flex align-items-center justify-content-center position-absolute badge bg-secondary">{{ __('Request Report') }}</span>
          <i class="fal fa-file-chart-line"></i>
          <p class="d-block mt-3 text-center mb-0">{{ __('Merchants Outstanding') }}</p>
        </a>
      </div><!-- col -->
    </div><!-- row -->
  </section><!-- reportsIndexPage -->

@endsection