@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
  <div class="row">
    <div class="col-12">
      <h1>{{ __('OTP') }}</h1>
      <div class="separator mb-5"></div>
    </div>
    <div class="col-12">
      <div class="create_bill_page card mb-4">
        <mobile-active :user="{{ json_encode($user) }}"> </mobile-active>
      </div>
    </div>
  </div>
@endsection