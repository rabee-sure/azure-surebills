<div class="modal fade billCancelModal" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="cancelModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-body d-flex align-items-center justify-content-center flex-column">
        <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
          <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
        </div><!-- closeBtn -->
        <span class="d-block fw-bold text-center text-body mb-4 fs-5">{{ __('Are you Sure to Cancel Bill ?')}}</span>
        <form method="POST" action="{{ route('bills.cancel', ['id'=> $bill->id]) }}" class="repeater" id="bill_create">
          @csrf
          <button type="submit" class="border-0 shadow-none rounded-3 btn-danger mx-2">{{__('Confirm Cancel Bill')}}</button>
          <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{__('Cancel')}}</button>
        </form>
      </div><!-- modal-body -->
    </div>
  </div>
</div>