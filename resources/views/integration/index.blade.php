@extends('layouts.app')

@section('title', __('Integration'))

@section('content')

  @canany(['show applications', 'create application', 'update application', 'delete application'])
  <applications></applications>
  @endcanany
  <!-- <passport-authorized-clients></passport-authorized-clients> -->
  <!-- <passport-personal-access-tokens></passport-personal-access-tokens>` -->

@endsection
