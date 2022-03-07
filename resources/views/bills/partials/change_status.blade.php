<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="changeStatusModalLabel">{{ __('Are you Sure to Change Status ?')}}</h5>
        <button id="changeStatus_close" type="button" class="close" data-bs-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
      </div>
      <form method="POST" action="{{ route('bills.change_status', ['id'=> $bill->id]) }}" class="repeater" id="form" >
        @csrf
        <div class="modal-body">
          <div class="bill_change_status">
            <label for="paid_cash" class="d-block mb-3 position-relative">
              <input value="paid_cash" id="paid_cash" type="radio" name="status" class="w-100 h-100 position-absolute" checked>
              <span class="d-flex align-items-center justify-content-start">{{ __('Paid Cash') }}</span>
            </label>
            <label for="paid_bank_transfer" class="d-block position-relative m-0">
              <input value="paid_bank_transfer" id="paid_bank_transfer" type="radio" name="status" class="w-100 h-100 position-absolute">
              <span class="d-flex align-items-center justify-content-start">{{ __('Paid Bank Transfer') }}</span>
            </label>
          </div><!-- bill_change_status -->
        </div><!-- modal-body -->
        <div class="modal-footer"> 
          <button type="submit" class="btn btn-primary" id="changeStatus_btn" >{{__('Save')}}</button>
          <button id="changeStatus_cancel" type="button" class="btn btn-secondary ml-2" data-bs-dismiss="modal">{{__('Retreat')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('footer-scripts')
{!! JsValidator::formRequest('App\Http\Requests\RefundRequest', '#form') !!}
<script>

$('#form').submit(function(){


});
</script>
@endpush
