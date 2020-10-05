@extends('layouts.app')

@section('title', __('Change Password'))

@section('content')
<div class="row" id="myId">
	<form action="/file-upload" class="dropzone">
	  <div class="fallback">
	    <input name="file" type="file" multiple />
	  </div>
	</form>
</div>
@endsection

@push('header-css')

<link rel="stylesheet" href="/dropzone.min.css" />
@endpush

@push('footer-scripts')
      <script src="{{ asset('js/dropzone.min.js') }}"></script>
      <script type="text/javascript">
      	$("div#myId").dropzone({ url: "/file/post" });
      </script>
@endpush