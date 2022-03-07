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

  <div class="reportsIndexPage">
    <div class="row">
      <div class="col-12 col-lg-2">
        <div class="blocks icon-cards-row">
          <div class="item">
            <a href="{{ route('reports.merchants-outstanding') }}" class="card mb-3">
              <div class="card-body text-center p-2 position-relative">
                <div class="d-flex align-items-center justify-content-end mb-2">
                  <span>{{ __('Request Report') }}</span>
                </div>
                <div class="icon mb-1">
                  <svg viewBox="0 0 256 256" xmlns="http://www.w3.org/2000/svg"><path fill="none" d="M0 0h256v256H0z"/><circle cx="128" cy="140" fill="none" r="40" stroke="#00d595" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" class="stroke-000000"/><path d="M196 116a59.8 59.8 0 0 1 48 24m-232 0a59.8 59.8 0 0 1 48-24m10.4 100a64.1 64.1 0 0 1 115.2 0" fill="none" stroke="#00d595" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" class="stroke-000000"/><path d="M60 116a32 32 0 1 1 31.4-38m73.2 0a32 32 0 1 1 31.4 38" fill="none" stroke="#00d595" stroke-linecap="round" stroke-linejoin="round" stroke-width="12" class="stroke-000000"/></svg>
                </div>
                <p class="card-text font-weight-semibold m-0">{{ __('Merchants Outstanding') }}</p>
              </div>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div><!-- reportsIndexPage -->

@endsection