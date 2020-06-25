 @extends('layouts.app')

@section('title', 'Page Title')

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="mb-2">
        <h1>Bills</h1>
        <div class="top-right-button-container">
          <a href="{{ route('bills.create')}}" class="btn btn-primary btn-lg top-right-button mr-1">
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
{{--         <nav class="mt-4 mb-3">
          <ul class="pagination justify-content-center mb-0">
            <li class="page-item ">
              <a class="page-link first" href="#">
                <i class="simple-icon-control-start"></i>
              </a>
            </li>
            <li class="page-item ">
              <a class="page-link prev" href="#">
                <i class="simple-icon-arrow-left"></i>
              </a>
            </li>
            <li class="page-item active">
              <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item ">
              <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
              <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item ">
              <a class="page-link next" href="#" aria-label="Next">
                <i class="simple-icon-arrow-right"></i>
              </a>
            </li>
            <li class="page-item ">
              <a class="page-link last" href="#">
                <i class="simple-icon-control-end"></i>
              </a>
            </li>
          </ul>
        </nav> --}}
      </div>
    </div>
  @else
      <div>No Bill matched the given criteria.</div>
  @endif
@endsection