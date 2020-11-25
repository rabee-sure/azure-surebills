@extends('layouts.app')

@section('title', __('Channel') . ' ' . $channel->name)

@section('content')
<div class="row">
  <div class="col-12">
    <h1>{{ __('Channel') }}</h1>
    <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
      <ol class="breadcrumb pt-0">
        <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
        <li class="breadcrumb-item"><a href="/channels" title="{{__('Channels')}}">{{__('Channels')}}</a></li>
        <li class="breadcrumb-item active" aria-current="page">{{ $channel->name }}</li>
      </ol>
    </nav>
    <div class="separator mb-5"></div>
  </div>
</div>

  <channel-applications :channel_id="{{$channel->id}}"> </channel-applications>

@endsection
