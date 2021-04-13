<div class="d-inline-block" data-toggle="modal" data-target="#request_transfer_Modal">
    <button type="button" class="btn btn-success btn-md top-right-button mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Request Transfer') }}">
        {{ __('Request Transfer') }}
    </button>
</div>

<!-- Modal -->
<div class="modal fade" id="request_transfer_Modal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="request_transfer_ModalLabel">{{ __('Request Transfer') }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="{{ route('transfers.request')}}" method="post">
                <div class="modal-body">
                    @csrf
                    @method('POST')
                    @if($transfers->where('status', 'pending')->count())
                        <h4 class="text-center m-0">{{ __('Sorry, you cannot request a transfer now. Please wait for the Transfer of the previous transfer')}}</h4>
                    @else
                        <h4 class="text-center m-0">{{ __('Are You sure you want Request transfer?')}}</h4>
                    @endif
                </div>
                <div class="modal-footer">

                @if(!$transfers->where('status', 'pending')->count())
                    <button type="submit" class="btn btn-danger login_button mr-3">
                        {{__('Request')}}
                    </button>
                @endif
                <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">
                    {{__('Close')}}
                </button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- Modal -->