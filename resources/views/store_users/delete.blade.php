<button type="button" class="btn btn-icon text-white btn-sm btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#delete_user_Modal_{{$user->id}}">
  <span class="w-100 h-100 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Suspend') }}"><i class="icon-base ti ti-user-off icon-18px"></i></span>
</button>

<div class="modal fade" id="delete_user_Modal_{{$user->id}}" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div><!-- modal-header -->
      <div class="modal-body">
        <div class="d-flex align-items-center justify-content-center text-warning mb-3">
          <i class="icon-base ti ti-info-triangle icon-50px"></i>
        </div>
        <h5 class="m-0 text-center">{{ __('Are You sure Suspend this User?')}}</h5>
      </div><!-- modal-body -->
      <form action="{{ route('users.destroy', $user->id)}}" method="post" class="modal-footer">
        @csrf
        @method('DELETE')
        <button type="button" class="btn btn-label-secondary" data-bs-dismiss="modal">{{__('Close')}}</button>
        <button type="submit" class="btn btn-danger">{{__('Suspend')}}</button>
      </form><!-- modal-footer -->
    </div><!-- modal-content -->
  </div>
</div><!-- modal -->
