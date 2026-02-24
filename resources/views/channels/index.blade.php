@extends('layouts.app')

@section('title', __('Channels'))

@section('content')

  <h4 class="mb-1">{{ __('Channels')}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('account') }}" title="{{ __('Settings') }}">{{ __('Settings')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Channels') }}</li>
    </ol>
  </nav>

  <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4 g-6">
    @foreach($channels as $channel)
      <div class="col">
        <a href="{{ route('channels.show', $channel->id) }}" title="{{ $channel->name }}" class="card h-100">
          <div class="card-body d-flex align-items-center justify-content-start">
            <div class="badge rounded p-2 bg-label-primary me-3">
              <i class="icon-base ti ti-device-imac icon-lg"></i>
            </div>
            <h6 class="card-title mb-0">{{ $channel->name }}</h6>
          </div>
        </a>
      </div><!-- col -->
    @endforeach
  </div><!-- row -->

@endsection
