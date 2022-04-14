@extends('layouts.app')

@section('title', __('Channels'))

@section('content')
  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Channels') }}</span>
  </div><!-- breadcrump -->

  <section id="channelsIndexPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Channels')}}</h1>
    </div><!-- title -->
    <div class="row row-cols-2 row-cols-sm-2 row-cols-md-4">
      @foreach($channels as $channel)
        <div class="col">
          <a href="{{ route('channels.show', $channel->id) }}" title="{{ $channel->name }}" class="catItem d-flex align-items-center justify-content-center flex-column mb-3 rounded-3 bg-white shadow-sm">
            <i class="fal fa-desktop"></i>
            <span class="d-block text-capitalize mt-3 text-center">{{ $channel->name }}</span>
          </a>
        </div><!-- col -->
      @endforeach
    </div><!-- row -->
  </section><!-- channelsIndexPage -->
@endsection
