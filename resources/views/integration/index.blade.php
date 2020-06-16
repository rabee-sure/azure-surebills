 @extends('layouts.app')

@section('title', 'Page Title')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="mb-2">
        <h1>{{__('Integration')}}</h1>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ route('home') }}">{{__('Home')}}</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{__('Integration')}}</li>
          </ol>
        </nav>
      </div>

      <div class="separator mb-5"></div>
    </div>
  </div>

  <div class="row">
    <passport-clients></passport-clients>
    <passport-authorized-clients></passport-authorized-clients>
    <passport-personal-access-tokens></passport-personal-access-tokens>
  </div>
@endsection