@extends('layouts.app')

@section('title', __('Roles'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <span>{{ __('Roles')}}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div><!-- alert -->
  @endif

  <section id="usersIndexPage">
    <div class="title d-flex align-items-center justify-content-between mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Roles')}}</h1>
      @can('create user')
        @include('roles.create')
      @endcan
    </div><!-- title -->
    <div class="tabsArea d-flex align-items-center justify-content-start flex-wrap mb-3">
      <a href="{{route('users.index')}}" title="{{__('Users')}}" class="d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-3">{{__('Users')}}</a>
    </div><!-- tabsArea -->
    @if(count($roles) == 0)
      <div class="no_customers_yet d-flex align-items-center justify-content-center flex-column bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <i class="fal fa-users"></i>
        <span class="d-block text-center mt-3 text-capitalize">{{ __('No roles') }}</span>
      </div><!-- no_customers_yet -->
    @else
      <div class="blockArea bg-white shadow-sm rounded-3 overflow-hidden mb-3">
        <div class="table-responsive">
          <table class="table table-striped table-hover text-nowrap">
            <thead>
              <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col" class="text-center">{{__('Name')}}</th>
                <th scope="col" class="text-center">{{__('Permissions')}}</th>
                @canany(['update user', 'delete user'])
                  <th scope="col" class="text-center">{{__('Actions')}}</th>
                @endcanany
              </tr>
            </thead>
            <tbody>
              @foreach($roles as $role)
                <tr>
                  <td class="text-center">{{$loop->index+1}}</td>
                  <td class="text-center">{{$role->name}}</td>
                  <td class="text-center">
                    @foreach($role->getPermissionNames() as $permission)
                      <span class="badge badge-info">{{__($permission)}}</span>
                    @endforeach
                  </td>
                  @canany(['update user', 'delete user'])
                    <td class="text-center">
                      <div class="d-flex align-items-center justify-content-center">
                        @can('update user')
                          <a href="{{ route('roles.edit', $role->id)}}" class="rounded-3 border-0 shadow-none p-0 btn-primary d-flex align-items-center justify-content-center mx-1" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Edit') }}"><i class="fal fa-edit"></i></a>
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
        {{ $roles->links() }}
      </div><!-- blockArea -->
    @endif
  </section><!-- rolesIndexPage -->
  
@endsection

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\RoleRequest', '#roles_form') !!}
@endpush