<button type="button" class="addUserBtn d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-pill" data-bs-toggle="modal" data-bs-target="#add_role_Modal">{{ __('Add Role') }} </button>

<!-- Add Role Modal -->
<div class="modal fade addRoleModal" id="add_role_Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <form method="POST" action="{{ route('roles.store') }}" id="roles_form" class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add Role') }}</h5>
        <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
      </div>
      <div class="modal-body">
        @csrf
        <div class="form-group mb-3">
          <label for="Name" class="d-block mb-2">{{__('Name')}} <span class="requirement text-danger">*</span></label>
          <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}">
        </div>
        <div class="form-group mb-3">
          <label for="Permissions" class="d-block mb-2">{{__('Permissions')}} <span class="requirement text-danger">*</span></label>
          <div class="border p-3 rounded-3">
            <div class="row">
              @foreach(config('RolePermissionsMatrix') as $permission)
                <div class="col-12 col-md-6">
                  <label for="{{$permission}}" class="checkboxItem d-block mb-3 position-relative">
                    <input type="checkbox" id="{{$permission}}" value="{{$permission}}" class="w-100 h-100 position-absolute" name="permissions[]">
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
      </div>
      <div class="modal-footer p-2">
        <button type="submit" class="border-0 shadow-none rounded-3 btn-primary">{{__('Add')}}</button>
        <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>
  </form>
</div>
<!-- Add Role Modal -->