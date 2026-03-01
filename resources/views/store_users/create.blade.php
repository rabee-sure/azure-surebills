<button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_user_Modal">
  <span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Add User') }}
</button>

<div class="modal fade" id="add_user_Modal" tabindex="-1" aria-hidden="true">
  <form method="POST" action="{{ route('users.store') }}" id="user_form" class="modal-dialog modal-lg" role="document">
    @csrf
    @method('post')
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Add User') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="row row-cols-1 row-cols-md-2 g-6">
          <div class="col">
            <label for="Name" class="form-label">{{__('Name')}} <span class="requirement text-danger">*</span></label>
            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="Mobile" class="form-label">{{__('Mobile')}} <span class="requirement text-danger">*</span></label>
            <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="Email" class="form-label">{{__('Email')}} <span class="requirement text-danger">*</span></label>
            <input name="email" type="email" inputmode="email" class="form-control" id="Email" placeholder="{{__('Email')}}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="Password" class="form-label">{{__('Password')}} <span class="requirement text-danger">*</span></label>
            <div class="input-group input-group-merge custom-form-password-toggle">
              <input
                id="password"
                name="password"
                autocomplete="off"
                type="password"
                class="form-control"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="password"
                />
              <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
            </div>
          </div><!-- col -->
          <div class="col">
            <label for="Confirm_Password" class="form-label">{{__('Confirm Password')}} <span class="requirement text-danger">*</span></label>
            <div class="input-group input-group-merge custom-form-password-toggle">
              <input
                id="Confirm_Password"
                name="confirm_password"
                autocomplete="off"
                type="password"
                class="form-control"
                placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                aria-describedby="Confirm_Password"
                />
              <span class="input-group-text cursor-pointer position-relative"><i class="icon-base ti ti-eye-off"></i></span>
            </div>
          </div><!-- col -->
          {{-- <div class="col">
            <label for="gender" class="form-label">{{__('Gander')}} <span class="requirement text-danger">*</span></label>
            <select name="gender" id="gender" class="form-control">
              <option value="0">{{ __('Choose Gender')}}</option>
              <option value="1">{{ __('Male')}}</option>
              <option value="2">{{ __('female')}}</option>
            </select>
          </div> --}}
          <div class="col">
            <label for="Role" class="form-label">{{__('Role')}} <span class="requirement text-danger">*</span></label>
            <select name="role" class="form-control select2-single" id="Role" aria-describedby="role-error" aria-invalid="false">
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
          <span class="btn-text">{{__('Save')}}</span>
        </button>
      </div><!-- modal-footer -->
    </div><!-- modal-content -->
  </form>
</div><!-- modal -->
