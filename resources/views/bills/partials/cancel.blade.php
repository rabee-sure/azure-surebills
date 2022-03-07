<div class="modal fade" 
id="cancelModal" tabindex="-1" 
role="dialog" 
aria-labelledby="cancelModalLabel" 
aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelModalLabel">
                    {{ __('Are you Sure to Cancel Bill ?')}}
                </h5>
                <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-footer">
                <form method="POST" action="{{ route('bills.cancel', ['id'=> $bill->id]) }}" class="repeater" id="bill_create">
                    @csrf
                    <button type="submit" class="btn btn-primary">
                        {{__('Confirm Cancel Bill')}}
                    </button>
                    <button type="button" class="btn btn-secondary ml-2" data-bs-dismiss="modal">
                        {{__('Cancel')}}
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>