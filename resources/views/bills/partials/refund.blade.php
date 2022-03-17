<div class="modal fade statusBillModal" id="refundModal" tabindex="-1" role="dialog" aria-labelledby="refundModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-header d-flex align-items-center justify-content-between">
        <h5 class="modal-title" id="refundModalLabel">{{ __('Are you Sure to Refund Bill ?')}}</h5>
        <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
      </div>
      <form method="POST" action="{{ route('bills.refund', ['id'=> $bill->id]) }}" class="repeater" id="form" >
        @csrf
        <input type="hidden" id="type" name="type" value="refund">
        <div class="modal-body">
          <div class="bill_change_status">
            <label for="ConfirmRefund" class="d-block mb-3 position-relative">
              <input  type="radio" id="ConfirmRefund" name="refund" class="position-absolute w-100 h-100" value="refund" checked>
              <span class="d-flex align-items-center justify-content-start">{{__('Total refund')}}</span>
            </label>
            <label for="PartialRefund" class="position-relative d-block">
              <input type="radio" id="PartialRefund" name="refund" class="position-absolute w-100 h-100" value="partial_refund">
              <span class="d-flex align-items-center justify-content-start">{{__('Partial Refund')}}</span>
            </label>
          </div><!-- select_refund -->
          <div id="amount_partial_refund" class="form-group row mt-3">
            <label for="amount" class="col-sm-2 col-form-label">{{__('Amount')}}</label>
            <div class="col-sm-10">
              <input type="tel" min="1" class="form-control" id="amount" name="amount" placeholder="{{__('Amount')}}">
            </div><!-- col-sm-10 -->
          </div><!-- form-group -->
        </div><!-- modal-body -->
        <div class="modal-footer p-2"> 
          <button type="submit" class="border-0 shadow-none rounded-3 btn-primary" id="refund_btn" >{{__('Save')}}</button>
          <button id="refund_cancel" type="button" class="border-0 shadow-none rounded-3 btn-light" data-bs-dismiss="modal">{{__('Retreat')}}</button>
        </div>
      </form>
    </div>
  </div>
</div>

@push('footer-scripts')

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
