@extends('layouts.app')

@section('title', __('Channels'))


@section('content')
	<div class="row">

		<div class="col-12">
			<h1>{{ __('Channels') }}</h1>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item"><a href="{{ url('/') }}" title="{{__('Home')}}">{{__('Home')}}</a></li>
          <li class="breadcrumb-item active" aria-current="page">{{__('Channels')}}</li>
        </ol>
      </nav>
			<div class="separator mb-5"></div>
		</div>

    <div class="col-12">
      <div class="row icon-cards-row mx-n3">
        @foreach($channels as $channel)
          <div class="col-6 col-sm-6 col-md-3 col-lg-3 col-xl-3">
            <a href="{{ route('channels.show', $channel->id) }}" title="{{ $channel->name }}" class="card mb-4">
              <div class="card-body text-center">
                <div class="statistic_icon iconsminds-id-card"></div>
                <p class="card-text font-weight-semibold mb-0">{{ $channel->name }}</p>
              </div>
            </a>
          </div>
        @endforeach

      </div>
    </div>

	</div>
@endsection

