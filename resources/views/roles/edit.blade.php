@extends('layouts.app')

@section('title', __('Roles'))

@section('content')

  <h4 class="mb-1">{{ __('Edit')}} : {{$role->name}}</h4>

  <nav aria-label="breadcrumb">
    <ol class="breadcrumb breadcrumb-custom-icon mb-6">
      <li class="breadcrumb-item">
        <a href="{{ url('/roles') }}" title="{{ __('Roles') }}">{{ __('Roles')}}</a>
        <i class="breadcrumb-icon icon-base ti ti-chevron-right align-middle icon-xs"></i>
      </li>
      <li class="breadcrumb-item active">{{ __('Edit')}}</li>
    </ol>
  </nav>

  @if ($errors->any())
    <ul class="list-group mb-6">
      @foreach ($errors->all() as $error)
        <li class="list-group-item list-group-item-danger">{{ $error }}</li>
      @endforeach
    </ul>
  @endif

  <form method="post" action="{{ route('roles.update', $role->id) }}" id="roles_form" class="card mb-6">
    @method('PATCH')
    @csrf
    <div class="card-body">
      <div class="row row-cols-1 g-6">
        <div class="col">
          <label for="Name" class="form-label">{{__('Name')}} <span class="requirement text-danger">*</span></label>
          <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}" value="{{old('name') ?? $role->name}}">
        </div><!-- col -->
        <div class="col">
          <label for="Permissions" class="form-label">{{__('Permissions')}} <span class="requirement text-danger">*</span></label>
          <div class="border p-4 rounded-3">
            <div class="row row-cols-1 row-cols-md-4 g-3">
              @foreach(config('RolePermissionsMatrix') as $permission)
                <div class="col">
                  <div class="form-check">
                    <input class="form-check-input" id="{{$permission}}" value="{{$permission}}" name="permissions[]" type="checkbox" @if(in_array($permission, $role->getPermissionNames()->toArray()) || (old('permissions') && in_array($permission, old('permissions')))) {{"checked"}} @endif>
                    <label class="form-check-label" for="{{$permission}}">{{__($permission)}}</label>
                  </div>
                </div><!-- col -->
              @endforeach
            </div><!-- row -->
          </div>
        </div>
      </div><!-- row -->
    </div><!-- card-body -->
    <div class="card-footer d-flex align-items-center justify-content-end gap-3">
      <a href="{{ url('roles') }}" title="{{__('Back')}}" class="btn btn-light">{{__('Back')}}</a>
      <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
    </div><!-- card-footer -->
  </form>

@endsection

@push('footer-scripts')
  <!-- Laravel Javascript Validation -->
  <script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.min.js')}}?v={{ config('app.asset_version') }}"></script>
  {!! JsValidator::formRequest('App\Http\Requests\RoleRequest', '#roles_form') !!}
@endpush
