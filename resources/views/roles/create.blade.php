<button type="button" class="btn btn-primary btn-md top-right-button mr-1" data-toggle="modal" data-target="#add_customer_Modal">{{ __('Add Role') }} </button>
<!-- Modal -->
<div class="modal fade" id="add_customer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add Role') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form method="POST" action="{{ route('roles.store') }}" id="roles_form">
                <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <label for="Name">{{__('Name')}} <span class="requirement">*</span></label>
                            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}">
                        </div>
                        <div class="form-group">
                            <label for="Permissions">{{__('Permissions')}} <span class="requirement">*</span></label>
                            @foreach(config('RolePermissionsMatrix') as $permission)
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" id="{{$permission}}" value="{{$permission}}" class="custom-control-input status_checkbox" name="permissions[]">
                                <label for="{{$permission}}" class="custom-control-label">{{__($permission)}}</label>
                            </div>
                            @endforeach
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

