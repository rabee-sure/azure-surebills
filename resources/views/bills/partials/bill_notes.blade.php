<div class="card">
  <h5 class="card-title p-4 m-0">{{__('Bill Notes')}}</h5>
  <div class="table-responsive text-nowrap">
    <table class="table table-striped table-hover">
      <thead>
        <tr>
          <th scope="col" class="fw-bold">{{__('Reference Number') }}</th>
          <th scope="col" class="fw-bold">{{__('Date created') }}</th>
          <th scope="col" width="10%" class="fw-bold">{{__('Amount') }}</th>
        </tr>
      </thead>
      <tbody>
        @foreach($billNotes as $note)
          <tr>
            <td><a href="@if($note->model == 'bills'){{route('bills.show', $note)}}@elseif ($note->model == 'refundedbills'){{route('refundedbills.show', $note->id)}} @endif">{{$note->number}}</a></td>
            <td>{{$note->created_at}}</td>
            <td>
              <span class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1">
                {{$note->sub_total + $note->vat - $note->discount}}  <i class="sar-icon"></i>
              </span>
            </td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div><!-- table-responsive -->
</div><!-- card -->
