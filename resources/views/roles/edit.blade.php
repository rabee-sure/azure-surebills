@extends('layouts.app')

@section('title', __('Roles'))

@section('content')

  <div class="breadcrump d-flex align-items-center justify-content-start flex-wrap mb-4 shadow-sm">
    <a href="{{ url('/')}}" title="{{ __('Home') }}">{{ __('Home') }}</a>
    <i>/</i>
    <a href="{{ url('account')}}" title="{{ __('Settings') }}">{{ __('Settings') }}</a>
    <i>/</i>
    <a href="{{ url('/roles')}}" title="{{ __('Roles') }}">{{ __('Roles') }}</a>
    <i>/</i>
    <span>{{ __('Edit')}}</span>
  </div><!-- breadcrump -->

  @if ($errors->any())
    <div class="alert alert-danger">
      <ul>
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <section id="usersEditPage">
    <div class="title mb-4">
      <h1 class="d-block fw-bold m-0 fs-5">{{__('Edit')}}</h1>
    </div><!-- title -->
    <div class="blockArea bg-white rounded-3 shadow-sm p-3">
      <form method="post" action="{{ route('roles.update', $role->id) }}" id="roles_form">
        @method('PATCH')
        @csrf
        <div class="form-group mb-3">
          <label for="Name" class="d-block mb-2">{{__('Name')}} <span class="requirement text-danger">*</span></label>
          <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}" value="{{old('name') ?? $role->name}}">
        </div>
        <div class="form-group mb-3">
          <label for="Permissions" class="d-block mb-2">{{__('Permissions')}} <span class="requirement text-danger">*</span></label>
          <div class="border p-3 rounded-3">
            <div class="row">
              @foreach(config('RolePermissionsMatrix') as $permission)
                <div class="col-12 col-md-6">
                  <label for="{{$permission}}" class="checkboxItem d-block mb-3 position-relative">
                    <input type="checkbox" id="{{$permission}}" value="{{$permission}}" class="w-100 h-100 position-absolute" name="permissions[]" @if(in_array($permission, $role->getPermissionNames()->toArray()) || (old('permissions') && in_array($permission, old('permissions')))) {{"checked"}} @endif>
                    <span class="d-flex align-items-center justify-content-start">
                      <i class="d-block rounded-pill position-relative"></i>
                      {{__($permission)}}
                    </span>
                  </label>
                </div><!-- col-12 -->
              @endforeach
            </div><!-- row -->
          </div>
        </div>
        <div class="buttonsArea mt-5 d-flex align-items-center justify-content-start">
          <button type="submit" class="rounded-3 border-0 shadow-none d-flex align-items-center justify-content-center btn-primary fw-bold formBtn">{{__('Update')}}</button>
          <a href="{{ url('roles') }}" class="rounded-3 border-0 shadow-none d-flex align-items-center fw-bold justify-content-center btn-light m-0" title="{{__('Back')}}">{{__('Back')}}</a>
        </div>
      </form>
    </div><!-- blockArea -->
  </section><!-- usersEditPage -->

@endsection
