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

  @if(session()->has('success'))
    <div class="alert alert-success d-flex align-items-center mb-6" role="alert">
      <span class="alert-icon rounded">
        <i class="icon-base ti ti-check icon-md"></i>
      </span>
      {{ session()->get('success') }}
    </div>
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
                        <span data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
                          <button type="button" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#edit_user_Modal" data-user="{{ json_encode(['id' => $user->id, 'name' => $user->name, 'mobile' => $user->mobile, 'email' => $user->email, 'role_id' => optional($user->roles->first())->id, 'is_super_admin' => $user->getRoleNames()->first() == 'super admin']) }}">
                            <span class="icon-base ti ti-edit icon-18px"></span>
                          </button>
                        </span>
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
    @can('update user')
      @include('store_users.edit')
    @endcan
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
      $('#Role').select2({
        dropdownParent: $('#add_user_Modal')
      });
      $('#edit_Role').select2({
        dropdownParent: $('#edit_user_Modal')
      });
    });

    // Edit User Modal - populate form when opened
    document.addEventListener('DOMContentLoaded', function() {
      const editModal = document.getElementById('edit_user_Modal');
      if (editModal) {
        editModal.addEventListener('show.bs.modal', function(event) {
          const button = event.relatedTarget;
          if (button && button.dataset.user) {
            const user = JSON.parse(button.dataset.user);
            const form = document.getElementById('user_update_form');
            form.action = "{{ url('users') }}/" + user.id;

            document.getElementById('edit_Name').value = user.name || '';
            document.getElementById('edit_Mobile').value = user.mobile || '';
            document.getElementById('edit_Email').value = user.email || '';
            document.getElementById('edit_Password').value = '';
            document.getElementById('edit_Confirm_Password').value = '';

            const roleCol = document.getElementById('edit_role_col');
            const roleSelect = document.getElementById('edit_Role');
            if (user.is_super_admin) {
              roleCol.style.display = 'none';
              roleSelect.disabled = true;
              roleSelect.removeAttribute('name');
            } else {
              roleCol.style.display = '';
              roleSelect.disabled = false;
              roleSelect.setAttribute('name', 'role');
              $('#edit_Role').val(user.role_id || '').trigger('change');
            }
          }
        });
      }
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
