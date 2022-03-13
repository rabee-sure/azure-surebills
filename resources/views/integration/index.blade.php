@extends('layouts.app')

@section('title', __('Integration'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{__('Integration')}}</span>
  </div><!-- breadcrump -->

  <applications></applications>
  <!-- <passport-authorized-clients></passport-authorized-clients> -->
  <!-- <passport-personal-access-tokens></passport-personal-access-tokens>` -->
  
@endsection
