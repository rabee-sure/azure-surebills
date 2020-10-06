<button type="button" class="btn btn-danger btn-md top-right-button mr-1" data-toggle="modal" data-target="#delete_customer_Modal_{{$customer->id}}">{{ __('Delete') }} </button>
<!-- Modal -->
<div class="modal fade" id="delete_customer_Modal_{{$customer->id}}" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
              <h5 class="modal-title" id="delete_customer_ModalLabel_{{$customer->id}}">{{ __('Delete Customer') }}</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
              </button>
          </div>
          <form action="{{ route('customers.destroy', $customer->id)}}" method="post">
            @csrf
            @method('DELETE')
            @if($customer->bills()->exists())
              {{ __('Sorry, you cannot delete this record because it has dependencies')}}
            @else
              {{ __('Are You sure Delete this Customer?')}}
            @endif
            <div class="modal-footer">
              @if(!$customer->bills()->exists())
                <button type="submit" class="btn btn-danger login_button mr-3">{{__('Delete')}}</button>
              @endif
                  <button type="button" class="btn btn-secondary m-0" data-dismiss="modal">{{__('Close')}}</button>
              </div>
          </form>
    </div>
  </div>
</div>
<!-- Modal -->

