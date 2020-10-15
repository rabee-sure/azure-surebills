<div class="d-inline-block" data-toggle="modal" data-target="#delete_customer_Modal_{{$customer->id}}"><button type="button" class="btn btn-danger btn-md top-right-button mr-1" data-toggle="tooltip" data-placement="top" data-original-title="{{ __('Delete') }}"><!-- {{ __('Delete') }} --><svg version="1.1" id="Capa_1" style="width: 15px;height: auto;fill: #fff;" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
	 viewBox="0 0 512 512" style="enable-background:new 0 0 512 512;" xml:space="preserve">
<g>
	<g>
		<g>
			<polygon points="353.574,176.526 313.496,175.056 304.807,412.34 344.885,413.804 			"/>
			<rect x="235.948" y="175.791" width="40.104" height="237.285"/>
			<polygon points="207.186,412.334 198.497,175.049 158.419,176.52 167.109,413.804 			"/>
			<path d="M17.379,76.867v40.104h41.789L92.32,493.706C93.229,504.059,101.899,512,112.292,512h286.74
				c10.394,0,19.07-7.947,19.972-18.301l33.153-376.728h42.464V76.867H17.379z M380.665,471.896H130.654L99.426,116.971h312.474
				L380.665,471.896z"/>
		</g>
	</g>
</g>
<g>
	<g>
		<path d="M321.504,0H190.496c-18.428,0-33.42,14.992-33.42,33.42v63.499h40.104V40.104h117.64v56.815h40.104V33.42
			C354.924,14.992,339.932,0,321.504,0z"/>
	</g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
<g>
</g>
</svg></button></div>
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
        <div class="modal-body">
          @csrf
          @method('DELETE')
          @if($customer->bills()->exists())
            <h4 class="text-center m-0">{{ __('Sorry, you cannot delete this record because it has dependencies')}}</h4>
          @else
            <h4 class="text-center m-0">{{ __('Are You sure Delete this Customer?')}}</h4>
          @endif
        </div>
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

