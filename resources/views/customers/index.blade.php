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

  @if(session()->has('success'))
    <div class="alert alert-success d-flex align-items-center mb-6" role="alert">
      <span class="alert-icon rounded">
        <i class="icon-base ti ti-check icon-md"></i>
      </span>
      {{ session()->get('success') }}
    </div>
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
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
                          <button type="button" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#edit_customer_Modal" data-customer="{{ json_encode($customer->only(['id', 'name', 'mobile', 'email', 'notes', 'bullding_no', 'street_name', 'district', 'city', 'postal_code', 'additional_no', 'other_buyer_id', 'vat_registration_number'])) }}">
                            <span class="icon-base ti ti-edit icon-18px"></span>
                          </button>
                        </span>
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

    @can('update customer')
      @include('customers.edit')
    @endcan

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\CustomerRequest', '#customers_store') !!}
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const editModal = document.getElementById('edit_customer_Modal');
      if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          if (button && button.dataset.customer) {
            const customer = JSON.parse(button.dataset.customer);
            const form = document.getElementById('customers_update');
            form.action = "{{ url('customers') }}/" + customer.id;
            document.getElementById('edit_Name').value = customer.name || '';
            document.getElementById('edit_Mobile').value = customer.mobile || '';
            document.getElementById('edit_Email').value = customer.email || '';
            document.getElementById('edit_Notes').value = customer.notes || '';
            @if($user->settings->add_tax_invoice ?? false)
              document.getElementById('edit_bullding_no').value = customer.bullding_no || '';
              document.getElementById('edit_street_name').value = customer.street_name || '';
              document.getElementById('edit_district').value = customer.district || '';
              document.getElementById('edit_city').value = customer.city || '';
              document.getElementById('edit_postal_code').value = customer.postal_code || '';
              document.getElementById('edit_additional_no').value = customer.additional_no || '';
              document.getElementById('edit_other_buyer_id').value = customer.other_buyer_id || '';
              document.getElementById('edit_vat_registration_number').value = customer.vat_registration_number || '';
            @endif
          }
        });
      }
    });
  </script>
@endpush
