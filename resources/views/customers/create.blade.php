<button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#add_customer_Modal">
  <span class="icon-xs icon-base ti ti-plus me-2"></span> {{ __('Add Customer') }}
</button>

<div class="modal fade" id="add_customer_Modal" tabindex="-1" aria-hidden="true">
  <form method="POST" action="{{ route('customers.store') }}" id="customers_store" class="modal-dialog modal-lg" role="document">
    @csrf
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel1">{{ __('Add Customer') }}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="row row-cols-1 row-cols-md-2 g-6">
          <div class="col">
            <label for="Name" class="form-label">{{__('Name')}} <span class="requirement text-danger">*</span></label>
            <input name="name" type="text" class="form-control" id="Name" placeholder="{{__('Name')}}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="Mobile" class="form-label">{{ __('Mobile') }} <span class="requirement text-danger">*</span></label>
            <input name="mobile" type="tel" class="form-control" id="Mobile" placeholder="{{__('Mobile')}}"  pattern="[0-9]*" maxlength="9" inputmod="numaric" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="Email" class="form-label">{{__('Email')}}</label>
            <input value="{{ old('email') }}" name="email" type="email" inputmode="email" class="form-control" id="Email" placeholder="{{__('Email')}}" autocomplete="off">
          </div><!-- col -->
          <div class="col">
            <label for="Notes" class="form-label">{{__('Customer Notes')}}</label>
            <input name="notes" type="text" class="form-control" id="Notes" placeholder="{{__('Customer Notes')}}" autocomplete="off">
          </div><!-- col -->
          @if($user->settings->add_tax_invoice)
            <div class="col">
              <label for="bullding_no" class="form-label">{{__('Building Number')}}</label>
              <input name="bullding_no" type="text" class="form-control" id="bullding_no" placeholder="{{__('Building Number')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="street_name" class="form-label">{{__('Street Name')}}</label>
              <input name="street_name" type="text" class="form-control" id="street_name" placeholder="{{__('Street Name')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="district" class="form-label">{{__('District')}}</label>
              <input name="district" type="text" class="form-control" id="district" placeholder="{{__('District')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="city" class="form-label">{{__('City')}}</label>
              <input name="city" type="text" class="form-control" id="city" placeholder="{{__('City')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="postal_code" class="form-label">{{__('Postal Code')}}</label>
              <input name="postal_code" type="text" class="form-control" id="postal_code" placeholder="{{__('Postal Code')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="additional_no" class="form-label">{{__('Additional Number')}}</label>
              <input name="additional_no" type="text" class="form-control" id="additional_no" placeholder="{{__('Additional Number')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="other_buyer_id" class="form-label">{{__('Additional ID')}}</label>
              <input name="other_buyer_id" type="text" class="form-control" id="other_buyer_id" placeholder="{{__('Additional ID')}}" autocomplete="off">
            </div><!-- col -->
            <div class="col">
              <label for="vat_registration_number" class="form-label">{{__('VAT Registration Number (optional)')}}</label>
              <input name="vat_registration_number" type="text" class="form-control" id="vat_registration_number" placeholder="{{__('VAT Registration Number (optional)')}}" autocomplete="off">
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
