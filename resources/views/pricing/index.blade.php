@extends('layouts.app')

@section('title', 'Page Title')

@section('content')

<div class="row">
  <div class="col-12">
    <h1>{{ __('Pricing') }}</h1>
    <div class="separator mb-5"></div>
  </div>
  <div class="col-12">
    <pricing></pricing>
  </div>
</div>

@endsection