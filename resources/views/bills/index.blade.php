 @extends('layouts.app')

@section('title', 'Page Title')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="mb-2">
        <h1>Bills</h1>
        <div class="top-right-button-container">
          <a href="{{ route('bills.create')}}" class="btn btn-primary btn-md top-right-button mr-1">
            {{ __('Create a bill')}}
          </a>
        </div>
        <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
          <ol class="breadcrumb pt-0">
            <li class="breadcrumb-item">
              <a href="{{ url('/')}}">Home</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">{{ __('Bills')}}</li>
          </ol>
        </nav>
      </div>
       
      <div class="separator mb-5"></div>
    </div>
  </div>
  @if($bills->count())
    <div class="row">
      <div class="col-12 list" data-check-all="checkAll">
        @foreach($bills as $bill)
          @include('bills.item')
        @endforeach
        {{ $bills->links() }}
      </div>
    </div>
  @else
      <div>No Bill matched the given criteria.</div>
  @endif
@endsection