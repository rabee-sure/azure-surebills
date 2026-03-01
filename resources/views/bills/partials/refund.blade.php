<div class="modal fade" id="refundModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form method="POST" action="{{ route('bills.refund', ['id'=> $bill->id]) }}" id="form" class="modal-content">
      @csrf
      <input type="hidden" id="type" name="type" value="refund">
      <div class="modal-header">
        <h5 class="modal-title" id="refundModalTitle">{{ __('Are you Sure to Refund Bill ?')}}</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="form-check">
          <input class="form-check-input" name="refund" value="refund" id="ConfirmRefund" type="radio" checked />
          <label class="form-check-label" for="ConfirmRefund">{{__('Total refund')}}</label>
        </div><!-- form-check -->
        <hr class="my-3">
        <div class="form-check">
          <input class="form-check-input" name="refund" value="partial_refund" id="PartialRefund" type="radio" />
          <label class="form-check-label" for="PartialRefund">{{__('Partial Refund')}}</label>
        </div><!-- form-check -->
        <hr class="my-3">
        <div class="row" id="amount_partial_refund">
          <label class="col-sm-3 col-form-label" for="amount">{{__('Amount')}}</label>
          <div class="col-sm-9">
            <div class="input-group input-group-merge" dir="ltr">
              <span class="input-group-text" id="basic-amount"><i class="sar-icon"></i></span>
              <input type="number" name="amount" min="1" id="amount" class="form-control" aria-label="{{ __('Price') }}" aria-describedby="basic-amount">
            </div>
          </div><!-- col -->
        </div><!-- row -->
      </div><!-- modal-body -->
      <div class="modal-footer p-3 d-flex align-items-center justify-content-end gap-3">
        <button type="button" class="btn text-secondary waves-effect m-0" data-bs-dismiss="modal" id="refund_cancel">{{__('Close')}}</button>
        <button type="submit" class="btn btn-primary px-5 m-0 waves-effect waves-light" id="refund_btn">{{__('Save')}}</button>
      </div><!-- modal-footer -->
    </form><!-- modal-content -->
  </div><!-- modal-dialog -->
</div><!-- modal -->

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
        setTimeout(() => { $(this).find(':submit').attr( 'disabled',false ); }, 10000)
      }
    });
  </script>
@endpush
