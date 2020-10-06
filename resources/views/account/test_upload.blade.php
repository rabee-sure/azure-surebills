@extends('layouts.app')

@section('title', __('Change Password'))

@push('header-css')

<link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
@endpush

@section('content')
<div class="card mb-4">
                        <div class="card-body">
                            <h5 class="mb-4">Dropzone</h5>
                            <form action="/file-upload">
                                <div class="dropzone">
                                </div>
                            </form>
                        </div>
                    </div>
@endsection

@push('footer-scripts')
      <script src="{{ asset('js/dropzone.min.js') }}"></script>
 <!--      <script type="text/javascript">
      	$("div#myId").dropzone({ url: "/file/post" });
      </script> -->
@endpush