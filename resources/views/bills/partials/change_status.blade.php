<div class="modal fade" id="changeStatusModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('bills.change_status', ['id'=> $bill->id]) }}" id="form" class="modal-content">
      @csrf
      <div class="modal-header">
        <h5 class="modal-title" id="changeStatusModalTitle">{{ __('Are you Sure to Change Status ?')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="form-check">
          <input class="form-check-input" name="status" value="paid_cash" id="paid_cash" type="radio" checked />
          <label class="form-check-label" for="paid_cash">{{ __('Paid Cash') }}</label>
        </div><!-- form-check -->
        <hr class="my-3">
        <div class="form-check">
          <input class="form-check-input" name="status" value="paid_bank_transfer" id="paid_bank_transfer" type="radio" />
          <label class="form-check-label" for="paid_bank_transfer">{{ __('Paid Bank Transfer') }}</label>
        </div><!-- form-check -->
      </div><!-- modal-body -->
      <div class="modal-footer p-3 d-flex align-items-center justify-content-end gap-3">
        <button type="button" class="btn text-secondary waves-effect m-0" data-bs-dismiss="modal" id="changeStatus_cancel">{{__('Close')}}</button>
        <button type="submit" class="btn btn-primary px-5 m-0 waves-effect waves-light" id="changeStatus_btn">{{__('Save')}}</button>
      </div><!-- modal-footer -->
    </form><!-- modal-content -->
  </div><!-- modal-dialog -->
</div><!-- modal -->

@push('footer-scripts')
  {!! JsValidator::formRequest('App\Http\Requests\RefundRequest', '#form') !!}
  <script type="text/javascript">
    $('#form').submit(function(){
      $(this).find(':submit').attr( 'disabled','disabled' );
      setTimeout(() => {
        $(this).find(':submit').attr( 'disabled',false );
      }, 10000);
    });
  </script>
@endpush
