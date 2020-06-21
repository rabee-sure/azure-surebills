@extends('layouts.app')

@section('title', __('Customers'))

@section('content')
        <div class="row">
          <div class="col-12">
            <div class="mb-3">
              <h1>customers</h1>
              <div class="top-right-button-container">
                @include('customers.create')
                <a href="#" class="btn btn-primary btn-md top-right-button mr-1">Download (CSV)</a>
              </div>
              <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
                <ol class="breadcrumb pt-0">
                  <li class="breadcrumb-item">
                    <a href="index.html">Home</a>
                  </li>
                  <li class="breadcrumb-item active" aria-current="page">customers</li>
                </ol>
              </nav>
            </div>
            <div class="mb-2">
              <div class="d-block d-md-inline-block">
                <div class="search-sm d-inline-block float-md-left mr-1 mb-1 align-top">
                    <input placeholder="Search...">
                </div>
              </div>
              <div class="float-md-right">
                <span class="text-muted text-small">Displaying 1-10 of 210 items </span>
                <button class="btn btn-outline-dark btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  20
                </button>
                <div class="dropdown-menu dropdown-menu-right">
                  <a class="dropdown-item" href="#">10</a>
                  <a class="dropdown-item active" href="#">20</a>
                  <a class="dropdown-item" href="#">30</a>
                  <a class="dropdown-item" href="#">50</a>
                  <a class="dropdown-item" href="#">100</a>
                </div>
              </div>
            </div>
          </div>
          <div class="separator mb-5"></div>
        </div>
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="table-responsive">
                <table class="table table-striped">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">{{__('Name')}}</th>
                      <th scope="col">{{__('Mobile')}}</th>
                      <th scope="col">{{__('Email')}}</th>
                      <th scope="col">{{__('Bills')}}</th>
                      <th scope="col">{{__('Date created')}}</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach ($customers as $customer)
                      <tr>
                        <th scope="row">1</th>
                        <td>{{ $customer->name }}</td>
                        <td>{{ $customer->mobile }}</td>
                        <td>{{ $customer->email }}</td>
                        <td>{{ $customer->bills->count() }}</td>
                        <td>{{ $customer->created_at }}</td>
                      </tr>
                    @endforeach

                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
@endsection

@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\CustomerRequest', '#customers_store') !!}
@endsection