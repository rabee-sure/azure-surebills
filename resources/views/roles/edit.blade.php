<div class="modal fade" id="edit_role_Modal" tabindex="-1" aria-hidden="true">
  <form method="POST" action="" id="roles_update_form" class="modal-dialog modal-lg" role="document">
    @method('PATCH')
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Edit') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="row g-6">
          <div class="col-12">
            <label for="edit_Name" class="form-label">{{__('Name')}} <span class="requirement text-danger">*</span></label>
            <input name="name" type="text" class="form-control" id="edit_Name" placeholder="{{__('Name')}}" required>
          </div><!-- col -->
          <div class="col-12">
            <label for="edit_Permissions" class="form-label">{{__('Permissions')}} <span class="requirement text-danger">*</span></label>
            <div class="border p-3 rounded-3">
              <div class="row row-cols-1 row-cols-md-2 g-3">
                @foreach(config('RolePermissionsMatrix') as $permission)
                  <div class="col">
                    <div class="form-check">
                      <input class="form-check-input edit-permission-checkbox" id="edit_{{ Str::slug($permission) }}" value="{{ $permission }}" name="permissions[]" type="checkbox" data-permission="{{ $permission }}" />
                      <label class="form-check-label" for="edit_{{ Str::slug($permission) }}">{{__($permission)}}</label>
                    </div>
                  </div><!-- col -->
                @endforeach
              </div><!-- row -->
            </div>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
      </div><!-- modal-footer -->
    </div><!-- modal-content -->
  </form>
</div><!-- modal -->
