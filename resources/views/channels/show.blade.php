@extends('layouts.app')

@section('title', __('Channel') . ' ' . $channel->name)

@section('content')

  <channel-applications :channel_id="{{$channel->id}}"> </channel-applications>
@endsection
