<div class="modal fade" id="edit_customer_Modal" tabindex="-1" aria-hidden="true">
  <form method="POST" action="" id="customers_update" class="modal-dialog modal-lg" role="document">
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
            <label for="edit_Mobile" class="form-label">{{ __('Mobile') }} <span class="requirement text-danger">*</span></label>
            <input name="mobile" type="tel" class="form-control" id="edit_Mobile" placeholder="{{__('Mobile')}}" pattern="[0-9]*" maxlength="9" inputmod="numaric" required autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="edit_Email" class="form-label">{{__('Email')}}</label>
            <input name="email" type="email" inputmode="email" class="form-control" id="edit_Email" placeholder="{{__('Email')}}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="edit_Notes" class="form-label">{{__('Customer Notes')}}</label>
            <input name="notes" type="text" class="form-control" id="edit_Notes" placeholder="{{__('Customer Notes')}}" autocomplete="off">
          </div><!-- col -->
          @if($user->settings->add_tax_invoice)
            <div class="col">
              <label for="edit_bullding_no" class="form-label">{{__('Building Number')}}</label>
              <input name="bullding_no" type="text" class="form-control" id="edit_bullding_no" placeholder="{{__('Building Number')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_street_name" class="form-label">{{__('Street Name')}}</label>
              <input name="street_name" type="text" class="form-control" id="edit_street_name" placeholder="{{__('Street Name')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_district" class="form-label">{{__('District')}}</label>
              <input name="district" type="text" class="form-control" id="edit_district" placeholder="{{__('District')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_city" class="form-label">{{__('City')}}</label>
              <input name="city" type="text" class="form-control" id="edit_city" placeholder="{{__('City')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_postal_code" class="form-label">{{__('Postal Code')}}</label>
              <input name="postal_code" type="text" class="form-control" id="edit_postal_code" placeholder="{{__('Postal Code')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_additional_no" class="form-label">{{__('Additional Number')}}</label>
              <input name="additional_no" type="text" class="form-control" id="edit_additional_no" placeholder="{{__('Additional Number')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_other_buyer_id" class="form-label">{{__('Additional ID')}}</label>
              <input name="other_buyer_id" type="text" class="form-control" id="edit_other_buyer_id" placeholder="{{__('Additional ID')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="edit_vat_registration_number" class="form-label">{{__('VAT Registration Number (optional)')}}</label>
              <input name="vat_registration_number" type="text" class="form-control" id="edit_vat_registration_number" placeholder="{{__('VAT Registration Number (optional)')}}" autocomplete="off">
            </div><!-- col -->
          @endif
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
