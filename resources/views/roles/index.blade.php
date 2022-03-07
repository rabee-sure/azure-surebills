@extends('layouts.app')

@section('title', __('Roles'))

@section('content')
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Roles')}}</h1>
        <div class="top-right-button-container">
        @include('roles.create')
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Roles')}}</li>
        </ol>
      </nav>
      <div class="separator mt-3 mb-5"></div>
      </div>
    </div>
  </div>
  <div class="row">
  <div class="col-12">
    <div class="card">
      <div class="card-body">
        <div class="table-responsive">
          <!-- condition of product count -->

            @if(count($roles) == 0)
                <div class="no_customers_yet">
                    <svg xmlns="http://www.w3.org/2000/svg" height="512" viewBox="0 0 24 24" width="512" fill="#999" xmlns:v="https://vecta.io/nano"><path d="M20 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-3a.94.94 0 0 0-1 1 .94.94 0 0 0 1 1 .94.94 0 0 0 1-1 .94.94 0 0 0-1-1zm3.5 8a.47.47 0 0 1-.5-.5v-1a1.54 1.54 0 0 0-1.5-1.5h-3a.47.47 0 0 1-.5-.5.47.47 0 0 1 .5-.5h3c1.4 0 2.5 1.1 2.5 2.5v1a.47.47 0 0 1-.5.5zM4 12c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm0-3a.94.94 0 0 0-1 1 .94.94 0 0 0 1 1 .94.94 0 0 0 1-1 .94.94 0 0 0-1-1zM.5 17a.47.47 0 0 1-.5-.5v-1C0 14.1 1.1 13 2.5 13h3a.47.47 0 0 1 .5.5.47.47 0 0 1-.5.5h-3A1.54 1.54 0 0 0 1 15.5v1a.47.47 0 0 1-.5.5zM12 12.5c-1.7 0-3-1.3-3-3s1.3-3 3-3 3 1.3 3 3-1.3 3-3 3zm0-5c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zM17 19a.47.47 0 0 1-.5-.5v-2A1.54 1.54 0 0 0 15 15H9a1.54 1.54 0 0 0-1.5 1.5v2a.47.47 0 0 1-.5.5.47.47 0 0 1-.5-.5v-2C6.5 15.1 7.6 14 9 14h6c1.4 0 2.5 1.1 2.5 2.5v2a.47.47 0 0 1-.5.5z"/></svg>
                    <span>{{ __('No roles') }}</span>
                </div><!-- no_customers_yet -->
            @else
                <table class="table table-striped">
                    <thead>
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">{{__('Name')}}</th>
                        <th scope="col">{{__('Permissions')}}</th>
                        <th scope="col">{{__('Actions')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    <!-- foreach of roles -->
                        @foreach($roles as $role)
                            <tr>
                                <th scope="row">{{$loop->index+1}}</th>
                                <td>{{$role->name}}</td>
                                <td>
                                    @foreach($role->getPermissionNames() as $permission)
                                        <span class="badge badge-info">{{$permission}}</span>
                                    @endforeach
                                </td>
                                <td>
                                <a href="{{ route('roles.edit', $role->id)}}" class="btn btn-primary" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Edit') }}">{{ __('Edit') }}</a>
                                @include('roles.delete', ['role' => $role])
                                {{-- <a href="#" class="btn btn-danger" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Delete') }}">{{ __('Delete') }}</a> --}}
                                </td>
                            </tr>
                        @endforeach
                    <!-- endforeach -->
                    </tbody>
                </table>
                {{ $roles->links() }}
            @endif
          <!-- else -->
        </div>
      </div>
    </div>
  </div>
  </div>
@endsection

@push('footer-scripts')
    {!! JsValidator::formRequest('App\Http\Requests\RoleRequest', '#roles_form') !!}
@endpush
