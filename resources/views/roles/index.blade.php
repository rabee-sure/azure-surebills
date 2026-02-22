@extends('layouts.app')

@section('title', __('Roles'))

@section('content')

  <div class="d-flex align-items-center justify-content-between gap-2 mb-6">
    <h4 class="m-0 flex-grow-1">{{__('Roles')}}</h4>
    @can('create user')
      @include('roles.create')
    @endcan
  </div><!-- d-flex -->

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  @if($roles->count())
    <div class="card">
      <div class="table-responsive text-nowrap">
        <table class="table table-striped table-hover">
          <thead>
            <tr>
              <th scope="col" class="fw-bold">#</th>
              <th scope="col" class="fw-bold">{{__('Name')}}</th>
              <th scope="col" class="fw-bold">{{__('Permissions')}}</th>
              @canany(['update user', 'delete user'])
                <th scope="col" class="fw-bold" width="10%">{{__('Actions')}}</th>
              @endcanany
            </tr>
          </thead>
          <tbody>
            @foreach($roles as $role)
              <tr>
                <td>{{$loop->index+1}}</td>
                <td>{{$role->name}}</td>
                <td>
                  @php
                    $permissions = $role->getPermissionNames();
                    $visibleCount = 7;
                    $visiblePermissions = $permissions->take($visibleCount);
                    $remainingPermissions = $permissions->skip($visibleCount);
                  @endphp
                  @foreach($visiblePermissions as $permission)
                    <span class="badge bg-label-dark">{{__($permission)}}</span>
                  @endforeach
                  @if($remainingPermissions->isNotEmpty())
                    <span
                      class="badge bg-label-dark cursor-pointer"
                      data-bs-toggle="tooltip"
                      data-bs-html="true"
                      data-bs-placement="top"
                      title="{!! $remainingPermissions->map(fn($p) => e(__($p)))->implode('<br>') !!}"
                    >
                      +{{ $remainingPermissions->count() }}
                    </span>
                  @endif
                </td>
                @canany(['update user', 'delete user'])
                  <td>
                    <div class="d-flex align-items-center justify-content-start gap-2">
                      @can('update user')
                        <a href="{{ route('roles.edit', $role->id)}}" class="btn btn-icon text-white btn-sm btn-info waves-effect waves-light" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}">
                          <span class="icon-base ti ti-edit icon-18px"></span>
                        </a>
                      @endcan
                      @can('delete user')
                        @include('roles.delete', ['role' => $role])
                      @endcan
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
      {{ $roles->links() }}
    </div><!-- d-flex -->
  @else
    <div class="card">
      <div class="card-body">
        <div class="d-flex align-items-center justify-content-center flex-column py-5">
          <i class="ti ti-list-check ti-xl"></i>
          <span class="d-block text-center mt-3 text-capitalize">{{ __('No roles') }}</span>
        </div><!-- d-flex -->
      </div><!-- card-body -->
    </div><!-- card -->
  @endif

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\RoleRequest', '#roles_form') !!}
@endpush
