<button type="button" class="rounded-3 border-0 shadow-none p-0 mx-1 btn-danger d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#delete_customer_Modal_{{$customer->id}}">
  <span class="w-100 h-100 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}"><i class="fal fa-trash-alt"></i></span>
</button>

<!-- Delete Customer Modal -->
<div class="modal fade deleteCustomerModal" id="delete_customer_Modal_{{$customer->id}}" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-body d-flex align-items-center justify-content-center flex-column">
        <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
          <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
        </div><!-- closeBtn -->
        @if($customer->bills()->exists())
          <span class="d-block text-center text-body mb-4 fs-5 text-break text-wrap">{{ __('Sorry, you cannot delete this record because it has dependencies')}}</span>
        @else
          <span class="d-block text-center text-body mb-4 fs-5 text-break text-wrap">{{ __('Are You sure Delete this Customer?')}}</span>
        @endif
        <form action="{{ route('customers.destroy', $customer->id)}}" method="post" class="form w-100">
          @csrf
          @method('DELETE')
          @if(!$customer->bills()->exists())
            <button type="submit" class="border-0 shadow-none rounded-3 btn-danger formBtn mx-2">{{__('Delete')}}</button>
          @endif
          <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{__('Close')}}</button>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Delete Customer Modal -->
