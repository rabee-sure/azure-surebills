@extends('layouts.app')

@section('title', __('My Account'))

@push('css_styles')
<link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/bs-stepper/bs-stepper.css') }}?v={{ config('app.asset_version') }}" />
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/@form-validation/form-validation.css') }}?v={{ config('app.asset_version') }}" />
@endpush

@section('content')

  <section id="accountStepsPage" class="py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8">
        @yield('steps')
      </div><!-- col-12 -->
    </div><!-- row -->
  </section><!-- accountStepsPage -->

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
@endpush
