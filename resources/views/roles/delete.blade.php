<button type="button" class="rounded-3 border-0 shadow-none p-0 mx-1 btn-danger d-flex align-items-center justify-content-center" data-bs-toggle="modal" data-bs-target="#delete_role_Modal_{{$role->id}}">
  <span class="w-100 h-100 d-flex align-items-center justify-content-center" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Delete') }}"><i class="fal fa-trash-alt"></i></span>
</button>

<!-- Delete Role Modal -->
<div class="modal fade deleteCustomerModal" id="delete_role_Modal_{{$role->id}}" tabindex="-1" role="dialog" aria-hidden="true">
 <div class="modal-dialog" role="document">
    <div class="modal-content border-0 shadow-sm rounded-3">
      <div class="modal-body d-flex align-items-center justify-content-center flex-column">
        <div class="closeBtn d-flex align-items-center justify-content-end mb-3 w-100">
          <button type="button" class="d-flex align-items-center justify-content-center border-0 bg-transparent p-0 text-body fs-4" data-bs-dismiss="modal" aria-label="Close"><i class="fal fa-times-circle"></i></button>
        </div><!-- closeBtn -->
        <form action="{{ route('roles.destroy', $role->id)}}" method="post">
          @csrf
          @method('DELETE')
          @if(App\Models\User::whereHas('roles', function($q) use ($role){ $q->where('name', $role->name); })->count() > 0)
            <span class="d-block text-center text-body mb-4 fs-5">{{ __('Sorry, you cannot delete this record because it has dependencies')}}</span>
          @else
            <span class="d-block text-center text-body mb-4 fs-5">{{ __('Are You sure Delete this Role?')}}</span>
          @endif
          <div class="d-flex align-items-center justify-content-center flex-wrap">
            @if(App\Models\User::whereHas('roles', function($q) use ($role){ $q->where('name', $role->name); })->count() == 0)
              <button type="submit" class="border-0 shadow-none rounded-3 btn-danger formBtn mx-2">{{__('Delete')}}</button>
            @endif
            <button type="button" class="border-0 shadow-none rounded-3 btn-light mx-2" data-bs-dismiss="modal">{{__('Close')}}</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>
<!-- Delete Role Modal -->