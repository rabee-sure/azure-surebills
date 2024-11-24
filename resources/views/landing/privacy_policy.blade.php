 @extends('layouts.landing')

@section('title', __('privacy_policy'))

@section('content')
    <div class="page_name">
      <div class="container">
        <div class="breadcrumbs">
          <ul>
            <li><a href="{{ url('/') }}" title="الرئيسية">الرئيسية</a></li>
            <li>|</li>
            <li>سياسة الخصوصية</li>
          </ul>
        </div><!-- breadcrumbs -->
        <div class="title">سياسة الخصوصية</div>
      </div><!-- container -->
    </div><!-- page_name -->

    <div class="container">
      <div id="simple_page">
        {!! nl2br(app()->getLocale() == 'ar' ? config('privacy_policy.contentAr') : config('privacy_policy.contentEn')) !!}
      </div><!-- simple_page -->
    </div><!-- container -->
@endsection


@push('footer-scripts')
@endpush
