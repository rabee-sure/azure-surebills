<div class="modal fade" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="refundModalLabel">{{ __('Are you Sure to Refund Bill ?')}}</h5>
                <button id="refund_close" type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('bills.refund', ['id'=> $bill->id]) }}" class="repeater" id="form" >
                @csrf
                <input type="hidden" id="type" name="type" value="refund">

                <div class="modal-body">
                    <div class="select_refund">
                        @if($bill->is_able_total_refund)
                            <label for="ConfirmRefund" class="position-relative d-block">
                                <input  type="radio" id="ConfirmRefund" name="refund" class="position-absolute w-100 h-100" value="refund" checked>
                                <div class="txt bg-light border text-body p-2 mb-2 d-flex align-items-center justify-content-start">
                                    <div class="checkmark rounded-circle position-relative d-flex align-items-center justify-content-center"><p class="rounded-circle bg-white m-0 d-block"></p></div>
                                    <span class="d-block">{{__('Total refund')}}</span>
                                </div><!-- txt -->
                            </label>
                        @elseif(auth()->user()->balance > 1)

                            <label for="PartialRefund" class="position-relative d-block">
                                <input type="radio" id="PartialRefund" name="refund" class="position-absolute w-100 h-100" value="partial_refund">
                                <div class="txt bg-light border text-body p-2 d-flex align-items-center justify-content-start">
                                <div class="checkmark rounded-circle position-relative d-flex align-items-center justify-content-center"><p class="rounded-circle bg-white m-0 d-block"></p></div>
                                <span class="d-block">{{__('Partial Refund')}}</span>
                                </div><!-- txt -->
                            </label>
                        @else
                            {{__('Your balance is not allowed to perform this transaction')}}
                        @endif

                    </div><!-- select_refund -->
                    <div id="amount_partial_refund" class="form-group row mt-3">
                        <label for="amount" class="col-sm-2 col-form-label">{{__('Amount')}}</label>
                        <div class="col-sm-10">
                        <input type="number" min="1" class="form-control" id="amount" name="amount" placeholder="{{__('Amount')}}">
                        </div><!-- col-sm-10 -->
                    </div><!-- form-group -->
                </div><!-- modal-body -->
                <div class="modal-footer"> 
                    <button type="submit" class="btn btn-primary" id="refund_btn" @if(!$bill->is_able_total_refund && auth()->user()->balance < 1) disabled @endif>
                        {{__('Save')}}
                    </button>
                    <button id="refund_cancel" type="button" class="btn btn-secondary ml-2" data-dismiss="modal">
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
    $("#refund_cancel").click(function(){
        $('#amount_partial_refund').hide();
        $('input#type').val('refund');
        $('#ConfirmRefund').prop("checked", true);
        $("#amount").val("");
        $('#amount-error').text('');
    });    

    $("#refund_close").click(function(){
        $('#amount_partial_refund').hide();
        $('input#type').val('refund');
        $('#ConfirmRefund').prop("checked", true);
        $("#amount").val("");
        $('#amount-error').text('');
    });

    $("#amount_partial_refund").hide(); 
    $('input[type=radio][name=refund]').change(function(){
        if(this.value == 'partial_refund'){
            $('#amount_partial_refund').show();
            $('input#type').val('partial_refund');
        }else{
            $('#amount_partial_refund').hide();
            $('input#type').val('refund');
            $("#amount").val("");
        }
    });

$('#form').submit(function(){
    if ($('#amount-error').text() == ''){
        $(this).find(':submit').attr( 'disabled','disabled' );
         //the rest of your code
        setTimeout(() => {
            $(this).find(':submit').attr( 'disabled',false );
        }, 10000)   
    }

});
</script>
@endpush
