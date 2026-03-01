<button type="button" class="btn btn-icon text-white btn-sm btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#delete_customer_Modal_{{$customer->id}}">
  <span class="w-100 h-100 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}"><i class="icon-base ti ti-trash icon-18px"></i></span>
</button>

<div class="modal fade" id="delete_customer_Modal_{{$customer->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="d-flex align-items-center justify-content-center text-warning mb-3">
          <i class="icon-base ti ti-info-triangle icon-50px"></i>
        </div>
        @if($customer->bills()->exists())
          <h5 class="m-0 text-center">{{ __('Sorry, you cannot delete this record because it has dependencies')}}</h5>
        @else
          <h5 class="m-0 text-center">{{ __('Are You sure Delete this Customer?')}}</h5>
        @endif
      </div><!-- modal-body -->
      <form action="{{ route('customers.destroy', $customer->id)}}" method="post" class="modal-footer form-delete-customer">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        @if(!$customer->bills()->exists())
          <button type="submit" class="btn btn-danger btn-submit-with-spinner" data-loading-text="{{ __('Deleting...') }}">
            <span class="btn-spinner d-none me-2" role="status">
              <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            </span>
            <span class="btn-text">{{__('Delete')}}</span>
          </button>
        @endif
      </form><!-- modal-footer -->
    </div><!-- modal-content -->
  </div>
</div><!-- modal -->
