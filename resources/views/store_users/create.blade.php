<button type="button" class="btn btn-primary btn-md top-right-button mr-1" data-toggle="modal" data-target="#add_customer_Modal">{{ __('Add User') }} </button>
<!-- Modal -->
<div class="modal fade" id="add_customer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add User') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form method="POST" action="{{ route('users.store') }}" id="user_form">
                <div class="modal-body">
                        @csrf
                        @method('post')
                        <div class="form-group">
                            <label for="Name">{{__('Name')}} <span class="requirement">*</span></label>
                            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}">
                        </div>
                        <div class="form-group">
                            <label for="Mobile">{{ __('Mobile') }} <span class="requirement">*</span></label>
                            <div class="input-group phone_inputs">
                              <div class="input-group-prepend">
                                <span class="input-group-text" id="basic-addon3">+966</span>
                              </div>
                              <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="Email">{{__('Email')}}</label>
                            <input  name="email" type="email" class="form-control" id="Email" placeholder="{{__('Email')}}">
                        </div>
                        <div class="form-group">
                            <label for="Password">{{__('Password')}}</label>
                            <input name="password" type="password" class="form-control" id="Password" placeholder="{{__('Password')}}">
                        </div>
                        <div class="form-group">
                            <label for="Confirm Password">{{__('Confirm Password')}}</label>
                            <input name="confirm_password" type="password" class="form-control" id="Confirm_Password" placeholder="{{__('Confirm Password')}}">
                        </div>
                        <div class="form-group">
                            <label>{{__('Role')}}</label>
                            <select value="" name="role" class="form-control is-valid" aria-describedby="role-error" aria-invalid="false">
                                @foreach($roles as $role)
                                    <option value="{{$role->name}}">{{$role->name}}</option>
                                @endforeach
                            </select>
                        </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary login_button mr-3">{{__('Add')}}</button>
                    <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">{{__('Close')}}</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->

