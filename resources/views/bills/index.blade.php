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
      <div class="mb-2">
        <a class="btn pt-0 pl-0 d-inline-block d-md-none" data-toggle="collapse" href="#displayOptions" role="button" aria-expanded="true" aria-controls="displayOptions">
          Display Options
          <i class="simple-icon-arrow-down align-middle"></i>
        </a>
{{--         <div class="collapse dont-collapse-sm" id="displayOptions">
          <div class="d-block d-md-inline-block">
            <div class="btn-group float-md-left mr-1 mb-1">
              <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Order By</button>
              <div class="dropdown-menu">
                <a class="dropdown-item" href="#">All</a>
                <a class="dropdown-item" href="#">Pending</a>
                <a class="dropdown-item" href="#">Paid</a>
                <a class="dropdown-item" href="#">Canceled</a>
              </div>
            </div>
            <div class="search-sm calendar-sm d-inline-block float-md-left mr-1 mb-1 align-top">
              <input class="form-control datepicker" placeholder="Search by day">
            </div>
          </div>
          <div class="float-md-right">
            <span class="text-muted text-small mr-1">Displaying 1-10 of 210 items </span>
            <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{20}}</button>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="?per_page=10">10</a>
              <a class="dropdown-item active" href="?per_page=20">20</a>
              <a class="dropdown-item" href="?per_page=30">30</a>
              <a class="dropdown-item" href="?per_page=50">50</a>
              <a class="dropdown-item" href="?per_page=100">100</a>
            </div>
          </div>
        </div> --}}
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