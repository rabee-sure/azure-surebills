<button type="button" class="btn btn-icon text-white btn-sm btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#delete_role_Modal_{{$role->id}}">
  <span class="w-100 h-100 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}"><i class="icon-base ti ti-trash icon-18px"></i></span>
</button>

<<<<<<< HEAD
<div class="modal fade" id="delete_role_Modal_{{$role->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="d-flex align-items-center justify-content-center text-warning mb-3">
          <i class="icon-base ti ti-info-triangle icon-50px"></i>
        </div>
        @can('deleteMerchantRole', $role)
          <h5 class="m-0 text-center">{{ __('Are You sure Delete this Role?')}}</h5>
        @endcan
        @cannot('deleteMerchantRole', $role)
          <h5 class="m-0 text-center">{{ __('Sorry, you cannot delete this record because it has dependencies')}}</h5>
        @endcannot
      </div><!-- modal-body -->
      <form action="{{ route('roles.destroy', $role->id)}}" method="post" class="modal-footer form-delete-role">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        @can('deleteMerchantRole', $role)
          <button type="submit" class="btn btn-danger btn-submit-with-spinner" data-loading-text="{{ __('Deleting...') }}">
            <span class="btn-spinner d-none me-2" role="status">
              <span class="spinner-border spinner-border-sm" aria-hidden="true"></span>
            </span>
            <span class="btn-text">{{__('Delete')}}</span>
          </button>
        @endcan
      </form><!-- modal-footer -->
    </div><!-- modal-content -->
  </div>
</div><!-- modal -->
=======
<!-- Delete Role Modal -->
<div class="modal fade deleteCustomerModal" id="delete_role_Modal_{{$role->id}}" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-body d-flex align-items-center justify-content-center flex-column">
        <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
          <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
        </div><!-- closeBtn -->
        <form action="{{ route('roles.destroy', $role->id)}}" method="post" class="form w-100">
          @csrf
          @method('DELETE')
          @can('deleteMerchantRole', $role)
            <span class="d-block text-center text-body mb-4 fs-5 text-break text-wrap">{{ __('Are You sure Delete this Role?')}}</span>
          @endcan
          @cannot('deleteMerchantRole', $role)
            <span class="d-block text-center text-body mb-4 fs-5 text-break text-wrap">{{ __('Sorry, you cannot delete this record because it has dependencies')}}</span>
          @endcannot
          <div class="d-flex align-items-center justify-content-center flex-wrap">
            @can('deleteMerchantRole', $role)
              <button type="submit" class="border-0 shadow-none rounded-3 btn-danger formBtn mx-2">{{__('Delete')}}</button>
            @endcan
            <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{__('Close')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Delete Role Modal -->
>>>>>>> 79152f3b8ca19cc1464254750d139cfac6ccb9f4
