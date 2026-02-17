@extends('layouts.app')

@section('title', __('Customers'))

@section('content')

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <h4 class="m-0 flex-grow-1">{{ __('Customers')}}</h4>
    @can('create customer')
      @include('customers.create')
    @endcan
  </div><!-- d-flex -->

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  <div class="card">
    @if($customers->count())
        <div class="table-responsive text-nowrap">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col" class="fw-bold">#</th>
                <th scope="col" class="fw-bold">{{__('Name')}}</th>
                <th scope="col" class="fw-bold">{{__('Mobile')}}</th>
                <th scope="col" class="fw-bold">{{__('Email')}}</th>
                <th scope="col" class="fw-bold">{{__('Bills')}}</th>
                <th scope="col" class="fw-bold">{{__('Date created')}}</th>
                @canany(['update customer', 'delete customer'])
                <th scope="col" class="fw-bold" width="10%">{{__('Actions')}}</th>
                @endcanany
              </tr>
            </thead>
            <tbody>
              @foreach ($customers as $customer)
                <tr>
                  <td>{{ $customer->id }}</td>
                  <td>{{ $customer->name }}</td>
                  <td>{{ $customer->mobile }}</td>
                  <td>{{ $customer->email }}</td>
                  <td>{{ $customer->bills->count() }}</td>
                  <td>{{ $customer->created_at }}</td>
                  @canany(['update customer', 'delete customer'])
                  <td>
                    <div class="d-flex align-items-center justify-content-start gap-2">
                      @can('update customer')
                        <a href="{{ route('customers.edit', $customer->id)}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light">
                          <span class="icon-base ti ti-edit icon-18px"></span>
                        </a>
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
        <div class="d-flex align-items-center justify-content-center mt-4">
          {{ $customers->links() }}
        </div>
      @else
        <div class="no_bills_yet d-flex align-items-center justify-content-center flex-column py-5">
          <i class="ti ti-users ti-xl"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No Customer matched the given criteria.') }}</span>
        </div><!-- no_bills_yet -->
      @endif
    </div><!-- card -->

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\CustomerRequest', '#customers_store') !!}
@endpush
