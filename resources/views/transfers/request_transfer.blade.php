<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#request_transfer_Modal">{{ __('Request Transfer') }}</button>

<div class="modal fade" id="request_transfer_Modal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        @if($transfers->where('status', 'pending')->count() || $transfers->where('status', 'send_to_sps')->count())
          <h5 class="m-0 text-center">{{ __('Sorry, you cannot request a transfer now. Please wait for the Transfer of the previous transfer')}}</h5>
        @else
          <h5 class="m-0 text-center">{{ __('Are You sure you want Request transfer?')}}</h5>
        @endif
      </div><!-- modal-body -->
      <form action="{{ route('transfers.request')}}" method="post" class="modal-footer">
        @csrf
        @method('POST')
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        @if(!$transfers->where('status', 'pending')->count() &&  !$transfers->where('status', 'send_to_sps')->count())
          <button type="submit" class="btn btn-primary">{{__('Request')}}</button>
        @endif
      </form><!-- modal-footer -->
    </div><!-- modal-content -->
  </div>
</div><!-- modal -->
