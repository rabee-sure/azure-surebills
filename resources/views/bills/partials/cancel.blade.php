<div class="modal fade" id="cancelModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="d-flex align-items-center justify-content-center text-warning mb-3">
          <i class="icon-base ti ti-info-triangle icon-50px"></i>
        </div>
        <h5 class="m-0 text-center">@if($bill->debit_note_bill_id == null) {{ __('Are you Sure to Cancel Bill ?')}} @else {{ __('Are you Sure to Cancel Debit Note ?')}} @endif</h5>
      </div><!-- modal-body -->
      <form action="{{ route('bills.cancel', ['id'=> $bill->id]) }}" method="post" class="modal-footer">
        @csrf
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn btn-danger">@if($bill->debit_note_bill_id == null) {{__('Confirm Cancel Bill')}} @else {{__('Confirm Cancel Debit Note')}} @endif</button>
      </form><!-- modal-footer -->
    </div><!-- modal-content -->
  </div>
</div><!-- modal -->
