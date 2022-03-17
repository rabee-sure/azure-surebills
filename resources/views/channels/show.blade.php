@extends('layouts.app')

@section('title', __('Channel') . ' ' . $channel->name)

@section('content')
  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Channel') }} {{ $channel->name }}</span>
  </div><!-- breadcrump -->
  
  <channel-applications :channel_id="{{$channel->id}}"> </channel-applications>
@endsection
