@extends('layouts.app')

@section('title', __('Users'))

@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/select2/select2.css') }}">
@endpush

@section('content')

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <h4 class="m-0 flex-grow-1">{{__('Users')}}</h4>
    @can('create user')
      @include('store_users.create')
    @endcan
  </div><!-- d-flex -->

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if($users->count())
    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col" class="fw-bold" width="5%">#</th>
              <th scope="col" class="fw-bold">{{__('Name')}}</th>
              <th scope="col" class="fw-bold">{{__('Mobile')}}</th>
              <th scope="col" class="fw-bold">{{__('Email')}}</th>
              {{-- <th scope="col" class="fw-bold">{{__('Gender')}}</th> --}}
              <th scope="col" class="fw-bold">{{__('Role')}}</th>
              @canany(['update user', 'delete user'])
                <th scope="col" class="fw-bold" width="10%">{{__('Actions')}}</th>
              @endcanany
            </tr>
          </thead>
          <tbody>
            @foreach($users as $user)
              <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$user->name}}</td>
                <td>{{$user->mobile}}</td>
                <td>{{$user->email}}</td>
                <td>{{$user->getRoleNames()->first()}}</td>
                @canany(['update user', 'delete user', 'restore user'])
                  <td>
                    <div class="d-flex align-items-center justify-content-start gap-2">
                      @can('updateMerchantUser', $user)
                        <a href="{{ route('users.edit', $user->id)}}" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
                          <span class="icon-base ti ti-edit icon-18px"></span>
                        </a>
                      @endcan
                      @if ($user->deleted_at == null)
                        @can('deleteMerchantUser', $user)
                          @include('store_users.delete', ['user' => $user])
                        @endcan
                      @else
                        @can('restoreMerchantUser', $user)
                          @include('store_users.restore', ['user' => $user])
                        @endcan
                      @endif
                    </div>
                  </td>
                @endcanany
              </tr>
            @endforeach
          </tbody>
        </table>
      </div><!-- table-responsive -->
    </div><!-- card -->
    <div class="d-flex align-items-center justify-content-center mt-3">
      {{ $users->links() }}
    </div><!-- d-flex -->
  @else
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center flex-column py-5">
          <i class="ti ti-users-group ti-xl"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No users') }}</span>
        </div><!-- d-flex -->
      </div><!-- card-body -->
    </div><!-- card -->
  @endif

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\StoreUserRequest', '#user_form') !!}
  <script src="{{ asset('assets/v2/vendor/libs/select2/select2.js') }}"></script>
  <script>
    // Select2
    $(document).ready(function() {
      $('.select2-single').select2({
        dropdownParent: $('#add_user_Modal')
      });
    });

    // Password Toggle
    document.addEventListener('DOMContentLoaded', function() {
      initPasswordToggle();
    });
    function initPasswordToggle() {
      const togglers = document.querySelectorAll('.custom-form-password-toggle i');
      togglers.forEach(icon => {
        icon.addEventListener('click', function(e) {
          e.preventDefault();

          const container = this.closest('.custom-form-password-toggle');
          const input = container.querySelector('input');
          const toggleIcon = container.querySelector('i');

          if (input.type === 'password') {
            input.type = 'text';
            toggleIcon.classList.replace('ti-eye-off', 'ti-eye');
          } else {
            input.type = 'password';
            toggleIcon.classList.replace('ti-eye', 'ti-eye-off');
          }
        });
      });
    }
  </script>
@endpush
