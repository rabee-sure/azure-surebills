@extends('layouts.app')

@section('title', __('Customers'))

@section('content')
  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Customers')}}</h1>
        <div class="top-right-button-container">
        @include('customers.create')
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Customers')}}</li>
        </ol>
      </nav>
      </div>
    </div>
    <div class="separator mb-5"></div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          @if($customers->count())
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
                  <th scope="row">{{ $customer->id }}</th>
                  <td>{{ $customer->name }}</td>
                  <td>{{ $customer->mobile }}</td>
                  <td>{{ $customer->email }}</td>
                  <td>{{ $customer->bills->count() }}</td>
                  <td>{{ $customer->created_at }}</td>
                </tr>
              @endforeach

            </tbody>
          </table>
          @else
          <div>No Customer matched the given criteria.</div>
          @endif
                  {{ $customers->links() }}
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@section('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\CustomerRequest', '#customers_store') !!}
@endsection