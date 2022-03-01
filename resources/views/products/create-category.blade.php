<button type="button" class="btn btn-primary btn-md top-right-button mr-1" data-toggle="modal" data-target="#add_customer_Modal">{{ __('Add Category') }} </button>
<!-- Modal -->
<div class="modal fade" id="add_customer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add Category') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form method="POST" action="#" id="category_store">
                <div class="modal-body">
                    @csrf
                    <div class="form-group">
                        <label for="Name">{{__('Name En')}} <span class="requirement">*</span></label>
                        <input name="name_en" type="text" class="form-control" id="Name_en" placeholder="{{__('Name En')}}">
                    </div>
                    <div class="form-group">
                        <label for="Name">{{__('Name Ar')}} <span class="requirement">*</span></label>
                        <input name="name_ar" type="text" class="form-control" id="Name_ar" placeholder="{{__('Name Ar')}}">
                    </div>

                    <div class="form-row">
                        <div class="form-group col-md-6">
                        <label for="inputEmail8">{{ __('Image') }}</label>
                        <div class="custom-file">
                            <input name="image" type="file" class="custom-file-input" accept="image/png, image/jpeg, image/jpg">
                            <input type="hidden" name="hidden_image" value="" />
                            <label class="custom-file-label" for="inputEmail8">{{ __('Choose file') }}</label>
                            @if($errors->has('image'))
                                <span id="inputEmail8-error" class="invalid-feedback" style="display: inline;">{{ $errors->first('image') }}</span>
                            @endif
                        </div>
                        </div>
                        <div class="form-group col-md-6">
                            <div class="custom-file">
                            @if(auth()->user()->logo)
                                <img src="@if(Storage::disk('public')->has(auth()->user()->logo)) {{url('storage/'.auth()->user()->logo)}} @else {{url(auth()->user()->logo)}} @endif" class="img-thumbnail logo_image" width="100" />
                                <i class="glyph-icon simple-icon-trash delete_logo"></i>

                            @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="sort_number">{{ __('Sort No.') }} <span class="requirement">*</span></label>
                        <div class="input-group phone_inputs">
                            <input name="sort_number" type="number" class="form-control" id="sort_number" placeholder="{{__('Sort No.')}}">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail1">{{ __('Activate') }}</label>
                        <div class="custom-switch custom-switch-primary mb-2">
                            <input name="active" class="custom-control-input" id="active_Checkbox" type="checkbox" checked>
                            <label class="custom-switch-btn" for="Tax_Invoice_Values_Checkbox"></label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="inputEmail5">{{__('Parent')}} <span class="requirement">*</span></label>
                        <select name="parent_id" id="inputEmail5" class="form-control">
                        <option value="" disabled selected>{{__('Select parent category')}}</option>

                            @foreach(App\Models\Bank::active()->get() as $bank)
                            <option value="{{$bank->id}}">
                                {{ $bank->name }}
                            </option>
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

