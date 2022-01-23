<button type="button" class="btn btn-primary btn-md top-right-button mr-1" data-toggle="modal" data-target="#add_customer_Modal">{{ __('Add Customer') }} </button>
<!-- Modal -->
<div class="modal fade" id="add_customer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add Customer') }}</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            </div>
            <form method="POST" action="{{ route('customers.store') }}" id="customers_store">
                <div class="modal-body">
                        @csrf
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
                            <label for="Notes">{{__('Notes')}}</label>
                            <input name="notes" type="text" class="form-control" id="Notes" placeholder="{{__('Notes')}}">
                        </div>                        

                        <div class="form-group">
                            <label for="bullding_no">{{__('bullding_no')}}</label>
                            <input name="bullding_no" type="text" class="form-control" id="bullding_no" placeholder="{{__('bullding_no')}}">
                        </div> 
                        <div class="form-group">
                            <label for="street_name">{{__('street_name')}}</label>
                            <input name="street_name" type="text" class="form-control" id="street_name" placeholder="{{__('street_name')}}">
                        </div>
                        <div class="form-group">
                            <label for="district">{{__('district')}}</label>
                            <input name="district" type="text" class="form-control" id="district" placeholder="{{__('district')}}">
                        </div>
                        <div class="form-group">
                            <label for="city">{{__('city')}}</label>
                            <input name="city" type="text" class="form-control" id="city" placeholder="{{__('city')}}">
                        </div>
                        <div class="form-group">
                            <label for="postal_code">{{__('postal_code')}}</label>
                            <input name="postal_code" type="text" class="form-control" id="postal_code" placeholder="{{__('postal_code')}}">
                        </div>
                        <div class="form-group">
                            <label for="additional_no">{{__('additional_no')}}</label>
                            <input name="additional_no" type="text" class="form-control" id="additional_no" placeholder="{{__('additional_no')}}">
                        </div>
                        <div class="form-group">
                            <label for="other_buyer_id">{{__('other_buyer_id')}}</label>
                            <input name="other_buyer_id" type="text" class="form-control" id="other_buyer_id" placeholder="{{__('other_buyer_id')}}">
                        </div> 
                        <div class="form-group">
                            <label for="vat_registration_number">{{__('vat_registration_number')}}</label>
                            <input name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{__('vat_registration_number')}}">
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

