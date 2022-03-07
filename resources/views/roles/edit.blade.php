@extends('layouts.app')

@section('title', __('Roles'))

@section('content')

  <div class="row">
    <div class="col-12">
      <div class="mb-3">
        <h1>{{ __('Roles')}}</h1>
        <div class="top-right-button-container">
      </div>
      <nav class="breadcrumb-container d-none d-sm-block d-lg-inline-block" aria-label="breadcrumb">
        <ol class="breadcrumb pt-0">
          <li class="breadcrumb-item">
            <a href="{{ url('/') }}">{{ __('Home')}}</a>
          </li>
          <li class="breadcrumb-item">
            <a href="{{ url('/roles') }}">{{ __('Roles')}}</a>
          </li>
          <li class="breadcrumb-item active" aria-current="page">{{ __('Edit')}}</li>
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
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{ route('roles.update', $role->id) }}" id="roles_form">
                <div class="modal-body">
                    @method('PATCH')
                    @csrf
                    <div class="form-group">
                        <label for="Name">{{__('Name')}} <span class="requirement">*</span></label>
                        <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}" value="{{$role->name}}">
                    </div>
                    <div class="form-group">
                        <label for="Permissions">{{__('Permissions')}} <span class="requirement">*</span></label>
                        @foreach(config('RolePermissionsMatrix') as $permission)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" id="{{$permission}}" value="{{$permission}}" class="custom-control-input status_checkbox" name="permissions[]" @if(in_array($permission, $role->getPermissionNames()->toArray())) {{"checked"}} @endif>
                            <label for="{{$permission}}" class="custom-control-label">{{__($permission)}}</label>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary login_button mr-3">{{__('Update')}}</button>
                    <a href="{{ url('roles') }}" class="btn btn-secondary m-0" data-dismiss="modal">{{__('Back')}}</a>
                </div>
            </form>
      </div>
    </div>
  </div>
  </div>
@endsection


@push('footer-scripts')

@endpush
