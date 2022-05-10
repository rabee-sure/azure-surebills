<button type="button" class="addCustomerBtn d-flex btn-primary border-0 shadow-none align-items-center justify-content-center text-white rounded-pill" data-bs-toggle="modal" data-bs-target="#add_customer_Modal">{{ __('Add Customer') }} </button>

<div class="modal fade addCustomerModal" id="add_customer_Modal" tabindex="-1" role="dialog" aria-labelledby="add_customer_ModalLabel" aria-hidden="true">
  <form method="POST" action="{{ route('customers.store') }}" id="customers_store" class="modal-dialog modal-dialog-scrollable" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title" id="add_customer_ModalLabel">{{ __('Add Customer') }}</h5>
        <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
      </div>
      <div class="modal-body">
        @csrf
        <div class="form-group mb-3">
          <label for="Name" class="d-block mb-1">{{__('Name')}} <span class="requirement text-danger">*</span></label>
          <input name="name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Name" placeholder="{{__('Name')}}">
        </div>
        <div class="form-group mb-3">
          <label for="Mobile" class="d-block mb-1">{{ __('Mobile') }} <span class="requirement text-danger">*</span></label>
          <div class="phoneInput overflow-hidden position-relative">
            <span class="d-flex align-items-center justify-content-center position-absolute rounded-3">+966</span>
            <input name="mobile" type="tel" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Mobile" placeholder="{{__('Mobile')}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric">
          </div><!-- phoneInput -->
        </div>
        <div class="form-group mb-3">
          <label for="Email" class="d-block mb-1">{{__('Email')}}</label>
          <input  name="email" type="email" inputmode="email" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Email" placeholder="{{__('Email')}}">
        </div>
        <div class="form-group mb-3">
          <label for="Notes" class="d-block mb-1">{{__('Notes')}}</label>
          <input name="notes" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="Notes" placeholder="{{__('Notes')}}">
        </div>
        @if($user->settings->add_tax_invoice)
          <div class="form-group mb-3">
              <label for="bullding_no" class="d-block mb-1">{{__('bullding_no')}}</label>
              <input name="bullding_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="bullding_no" placeholder="{{__('bullding_no')}}">
          </div>
          <div class="form-group mb-3">
              <label for="street_name" class="d-block mb-1">{{__('street_name')}}</label>
              <input name="street_name" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="street_name" placeholder="{{__('street_name')}}">
          </div>
          <div class="form-group mb-3">
              <label for="district" class="d-block mb-1">{{__('district')}}</label>
              <input name="district" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="district" placeholder="{{__('district')}}">
          </div>
          <div class="form-group mb-3">
              <label for="city" class="d-block mb-1">{{__('City')}}</label>
              <input name="city" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="city" placeholder="{{__('City')}}">
          </div>
          <div class="form-group mb-3">
              <label for="postal_code" class="d-block mb-1">{{__('postal_code')}}</label>
              <input name="postal_code" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="postal_code" placeholder="{{__('postal_code')}}">
          </div>
          <div class="form-group mb-3">
              <label for="additional_no" class="d-block mb-1">{{__('additional_no')}}</label>
              <input name="additional_no" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="additional_no" placeholder="{{__('additional_no')}}">
          </div>
          <div class="form-group mb-3">
              <label for="other_buyer_id" class="d-block mb-1">{{__('other_buyer_id')}}</label>
              <input name="other_buyer_id" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="other_buyer_id" placeholder="{{__('other_buyer_id')}}">
          </div>
          <div class="form-group mb-3">
              <label for="vat_registration_number" class="d-block mb-1">{{__('vat_registration_number')}}</label>
              <input name="vat_registration_number" type="text" class="form-control shadow-none bg-white border w-100 rounded-3 text-body" id="vat_registration_number" placeholder="{{__('vat_registration_number')}}">
          </div>
        @endif
      </div>
      <div class="modal-footer p-2">
          <button type="submit" class="border-0 shadow-none rounded-3 btn-primary">{{__('Add')}}</button>
          <button type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{__('Close')}}</button>
      </div>
    </div>
  </form>
</div>
<!-- Modal -->

