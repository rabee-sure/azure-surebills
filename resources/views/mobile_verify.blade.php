@extends('layouts.auth')

@section('title', __('mobile verify') )

@section('content')


<div id="app-container">
  <div id="app" class="row justify-content-center">
      <mobile-active :user="{{ json_encode($user) }}"> </mobile-active>
    </div><!-- row -->
  </div><!-- date_date_set -->
@endsection

@push('footer-scripts')
@if(in_array(request()->route()->getName(), ['channels.show', 'integration','mobile_verify', 'home' ]))
      <script src="{{ asset('assets/v2/vendor/js/app.js') }}?v={{ config('app.asset_version') }}"></script>
    @endif
@endpush
