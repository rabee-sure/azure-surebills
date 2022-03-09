<button type="button" class="requestTransferBtn d-flex align-items-center justify-content-center mb-2 btn-primary border-0 rounded-pill" data-bs-toggle="modal" data-bs-target="#request_transfer_Modal">{{ __('Request Transfer') }}</button>

<div class="modal fade billCancelModal" id="request_transfer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-body d-flex align-items-center justify-content-center flex-column">
        <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
          <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
        </div><!-- closeBtn -->
        @if($transfers->where('status', 'pending')->count() || $transfers->where('status', 'send_to_sps')->count())
          <span class="d-block fw-bold text-center text-body mb-4 fs-5">{{ __('Sorry, you cannot request a transfer now. Please wait for the Transfer of the previous transfer')}}</span>
        @else
          <span class="d-block fw-bold text-center text-body mb-4 fs-5">{{ __('Are You sure you want Request transfer?')}}</span>
        @endif
        <form action="{{ route('transfers.request')}}" method="post">
          @csrf
          @method('POST')
          @if(!$transfers->where('status', 'pending')->count() &&  !$transfers->where('status', 'send_to_sps')->count())
            <button type="submit" class="border-0 shadow-none rounded-3 btn-primary mx-2 formBtn">{{__('Request')}}</button>
          @endif
          <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{__('Close')}}</button>
        </form>
      </div>
    </div>
  </div>
</div>