@extends('layouts.app')

@section('title', __('My Account'))

@section('css_styles')
  <link rel="stylesheet" href="{{ asset('css/smart_wizard.min.css') }}">
@endsection


<link rel="stylesheet" href="css/vendor/" />

@section('content')
	<div class="row">

      @yield('steps')

	</div>
@endsection

  <script src="{{ asset('js/jquery.smartWizard.min.js') }}" defer></script>


@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\AccountInformationRequest', '#form') !!}
@endpush
