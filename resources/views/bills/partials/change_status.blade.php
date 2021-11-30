<div class="modal fade" id="changeStatusModal" tabindex="-1" role="dialog" aria-labelledby="changeStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeStatusModalLabel">{{ __('Are you Sure to Change Status ?')}}</h5>
                <button id="changeStatus_close" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('bills.change_status', ['id'=> $bill->id]) }}" class="repeater" id="form" >
                @csrf
                <div class="modal-body">
                    <div class="radio">
                        <label><input value="paid_cash"  type="radio" name="status" checked>{{ __('Paid Cash') }}</label>
                    </div>
                    <div class="radio">
                        <label><input  value="paid_bank_transfer" type="radio" name="status">{{ __('Paid Bank Transfer') }}</label>
                    </div>
                </div><!-- modal-body -->
                <div class="modal-footer"> 
                    <button type="submit" class="btn btn-primary" id="changeStatus_btn" >
                        {{__('Save')}}
                    </button>
                    <button id="changeStatus_cancel" type="button" class="btn btn-secondary ml-2" data-dismiss="modal">
                        {{__('Retreat')}}
                    </button>
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
