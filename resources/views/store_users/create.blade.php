<button type="button" class="addUserBtn d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-pill" data-bs-toggle="modal" data-bs-target="#add_customer_Modal">{{ __('Add User') }} </button>

<div class="modal fade addCustomerModal" id="add_customer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <form method="POST" action="{{ route('users.store') }}" id="user_form" class="modal-dialog modal-dialog-scrollable">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add User') }}</h5>
        <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
      </div>
      <div class="modal-body">
        @csrf
        @method('post')
        <div class="form-group mb-3">
          <label for="Name" class="d-block mb-2">{{__('Name')}}<span class="requirement text-danger">*</span></label>
          <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}">
        </div>
        <div class="form-group mb-3">
          <label for="Mobile" class="d-block mb-2">{{__('Mobile')}}<span class="requirement text-danger">*</span></label>
          <div class="phoneInput overflow-hidden position-relative">
            <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
            <input name="mobile" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Mobile" placeholder="{{__('Mobile')}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric">
          </div><!-- phoneInput -->
        </div>
        <div class="form-group mb-3">
          <label for="Email" class="d-block mb-2">{{__('Email')}}<span class="requirement text-danger">*</span></label>
          <input name="email" type="email" inputmode="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Email" placeholder="{{__('Email')}}" autocomplete="off">
        </div>
        <div class="form-group mb-3">
          <label for="Password" class="d-block mb-2">{{__('Password')}}<span class="requirement text-danger">*</span></label>
          <input name="password" type="password" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Password" placeholder="{{__('Password')}}">
        </div>
        <div class="form-group mb-3">
          <label for="Confirm Password" class="d-block mb-2">{{__('Confirm Password')}}<span class="requirement text-danger">*</span></label>
          <input name="confirm_password" type="password" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Confirm_Password" placeholder="{{__('Confirm Password')}}">
        </div>
        <div class="form-group mb-3">
          <label class="d-block mb-2">{{__('Gander')}}<span class="requirement text-danger">*</span></label>
          <select name="gender" id="gender" class="form-control shadow-none bg-white border w-100 rounded-3 text-body">
            <option value="0">{{ __('Choose Gender')}}</option>
            <option value="1">{{ __('Male')}}</option>
            <option value="2">{{ __('female')}}</option>
          </select>
        </div>
        <div class="form-group mb-3">
          <label class="d-block mb-2">{{__('Role')}}<span class="requirement text-danger">*</span></label>
          <select name="role" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" aria-describedby="role-error" aria-invalid="false">
            <option value="">{{ __('Choose Role')}}</option>
            @foreach($roles as $role)
              <option value="{{$role->id}}">{{$role->name}}</option>
            @endforeach
          </select>
        </div>
      </div>
      <div class="modal-footer p-2">
        <button type="submit" class="border-0 shadow-none rounded-3 btn-primary">{{__('Add')}}</button>
        <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>
  </form>
</div>
