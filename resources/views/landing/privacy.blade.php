 @extends('layouts.landing')

@section('title', __('Contact'))

@section('content')
    <div class="page_name">
      <div class="container">
        <div class="breadcrumbs">
          <ul>
            <li><a href="{{ url('/') }}" title="الرئيسية">الرئيسية</a></li>
            <li>|</li>
            <li>الخصوصية</li>
          </ul>
        </div><!-- breadcrumbs -->
        <div class="title">الخصوصية</div>
      </div><!-- container -->
    </div><!-- page_name -->

    <div class="container">
      <div id="simple_page">
        
      </div><!-- simple_page -->
    </div><!-- container -->
@endsection


@push('footer-scripts')
@endpush
