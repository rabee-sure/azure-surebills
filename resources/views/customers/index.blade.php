@extends('layouts.app')

@section('title', __('Customers'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <span>{{ __('Customers')}}</span>
  </div><!-- breadcrump -->

  <section id="customersIndexPage">

    <div class="title mb-4 d-flex align-items-center justify-content-between flex-wrap">
      <h1 class="d-block fw-bold m-0 fs-5">{{ __('Customers')}}</h1>
      @can('create customer')
        @include('customers.create')
      @endcan
    </div><!-- title -->

    @if ($errors->any())
      <div class="alert alert-danger">
        <ul>
          @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <div class="customersArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
      @if($customers->count())
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">{{__('Name')}}</th>
                <th scope="col" class="text-center">{{__('Mobile')}}</th>
                <th scope="col" class="text-center">{{__('Email')}}</th>
                <th scope="col" class="text-center">{{__('Bills')}}</th>
                <th scope="col" class="text-center">{{__('Date created')}}</th>
                @canany(['update customer', 'delete customer'])
                <th scope="col" class="text-center" width="10%">{{__('Actions')}}</th>
                @endcanany
              </tr>
            </thead>
            <tbody>
              @foreach ($customers as $customer)
                <tr>
                  <td class="text-center">{{ $customer->id }}</td>
                  <td class="text-center">{{ $customer->name }}</td>
                  <td class="text-center">{{ $customer->mobile }}</td>
                  <td class="text-center">{{ $customer->email }}</td>
                  <td class="text-center">{{ $customer->bills->count() }}</td>
                  <td class="text-center">{{ $customer->created_at }}</td>
                  @canany(['update customer', 'delete customer'])
                  <td class="text-center">
                    <div class="d-flex align-items-center justify-content-center">
                        @can('update customer')
                        <a href="{{ route('customers.edit', $customer->id)}}" class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}"><i class="fal fa-edit"></i></a>
                        @endcan
                        @can('delete customer')
                        @include('customers.delete', ['customer' => $customer])
                        @endcan
                    </div>
                  </td>
                  @endcanany
                </tr>
              @endforeach

            </tbody>
          </table>
        </div><!-- table-responsive -->
        {{ $customers->links() }}
      @else
        <div class="no_customers_yet d-flex align-items-center justify-content-center flex-column">
          <i class="fal fa-users"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No Customer matched the given criteria.') }}</span>
        </div><!-- no_customers_yet -->
      @endif
    </div><!-- customersArea -->

  </section><!-- customersIndexPage -->

@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\CustomerRequest', '#customers_store') !!}
@endpush
