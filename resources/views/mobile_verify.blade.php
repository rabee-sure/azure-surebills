@extends('layouts.app')
@section('title', 'Page Title')
@section('content')
  <div class="row justify-content-center">
    <mobile-active :user="{{ json_encode($user) }}"> </mobile-active>
  </div><!-- row -->
@endsection