@extends('layouts.app')

@section('title', __('My Account'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/smart_wizard.min.css') }}">
@endsection

@section('content')

  <section id="accountStepsPage" class="py-5">
    <div class="row justify-content-center">
      <div class="col-12 col-md-8">
        @yield('steps')
      </div><!-- col-12 -->
    </div><!-- row -->
  </section><!-- accountStepsPage -->

@endsection

<script src="{{ asset('js/jquery.smartWizard.min.js') }}" defer></script>

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
