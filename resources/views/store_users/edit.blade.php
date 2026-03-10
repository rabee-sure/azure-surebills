<div class="modal fade" id="edit_user_Modal" tabindex="-1" aria-hidden="true">
  <form method="POST" action="" id="user_update_form" class="modal-dialog modal-lg" role="document">
    @method('PATCH')
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">{{ __('Edit') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="row row-cols-1 row-cols-md-2 g-6">
          <div class="col">
            <label for="edit_Name" class="form-label">{{__('Name')}} <span class="requirement text-danger">*</span></label>
            <input name="name" type="text" class="form-control" id="edit_Name" placeholder="{{__('Name')}}" required autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="edit_Mobile" class="form-label">{{__('Mobile')}} <span class="requirement text-danger">*</span></label>
            <input name="mobile" type="tel" class="form-control" id="edit_Mobile" placeholder="{{__('Mobile')}}" pattern="[0-9]*" maxlength="9" inputmod="numaric" required autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="edit_Email" class="form-label">{{__('Email')}} <span class="requirement text-danger">*</span></label>
            <input name="email" type="email" inputmode="email" class="form-control" id="edit_Email" placeholder="{{__('Email')}}" required autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="edit_Password" class="form-label">{{__('Password')}}</label>
            <div class="input-group input-group-merge custom-form-password-toggle">
              <input
                id="edit_Password"
                name="password"
                autocomplete="off"
                type="password"
                class="form-control"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="edit_Password"
                />
              <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
            </div>
          </div><!-- col -->
          <div class="col">
            <label for="edit_Confirm_Password" class="form-label">{{__('Confirm Password')}}</label>
            <div class="input-group input-group-merge custom-form-password-toggle">
              <input
                id="edit_Confirm_Password"
                name="confirm_password"
                autocomplete="off"
                type="password"
                class="form-control"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="edit_Confirm_Password"
                />
              <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
            </div>
          </div><!-- col -->
          <div class="col" id="edit_role_col">
            <label for="edit_Role" class="form-label">{{__('Role')}} <span class="requirement text-danger">*</span></label>
            <select name="role" class="form-control select2-single" id="edit_Role" aria-describedby="role-error" aria-invalid="false">
              <option value="" disabled selected>{{ __('Choose Role')}}</option>
              @foreach($roles as $role)
                <option value="{{$role->id}}">{{$role->name}}</option>
              @endforeach
            </select>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- modal-body -->
      <div class="modal-footer">
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn btn-primary btn-submit-with-spinner" data-loading-text="{{ __('Saving...') }}">
          <span class="btn-spinner d-none me-2" role="status">
            <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
          </span>
          <span class="btn-text">{{__('Update')}}</span>
        </button>
      </div><!-- modal-footer -->
    </div><!-- modal-content -->
  </form>
</div><!-- modal -->
