<div class="dz-preview dz-file-preview border m-0 bg-white rounded-3 text-body shadow-sm overflow-hidden dz-processing dz-image-preview dz-complete" id="file-{{$file->id}}">
  <div class="d-flex align-items-center justify-content-start align-self-stretch p-1 position-relative">
    <figure class="m-0 rounded-3 overflow-hidden position-relative flex-shrink-0">
      <div class="dz-error-mark"><span><i></i></span></div><div class="dz-success-mark"><span><i></i></span></div>
      @if(!empty($file->thumbnail_url))
      <img data-dz-thumbnail="" class='w-100 h-100' src="{{ $file->thumbnail_url }}" alt="" />
      @endif
      <i class="fal fa-file-alt border rounded-3 overflow-clip d-flex align-items-center justify-content-center position-absolute top-0 start-0 bg-white fs-1 w-100 h-100 simple-icon-doc preview-icon"></i>
    </figure>
    <figcaption class="flex-grow-1 align-self-stretch d-flex align-items-start justify-content-between flex-column">
      <div class="file_name d-block text-body"><span class="d-block text-body" data-dz-name="">{{$file->file_name}}</span><small class="d-block text-danger" id="error_message"></small></div>
      <div class="d-block text-secondary dz-size" data-dz-size=""><strong>{{ round($file->size/1024,2) }}</strong> KB</div>
      <div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>
      <span id="error_message"></span>
    </figcaption>
  </div>
</div>

@push('footer-scripts')
  <script type="text/javascript">
    $(document).ready(function() {
      $('#file-{{$file->id}}').on('click', function() {
        var url = @json($file->download_url ?? null);
        if (!url) {
          url = '/download/{{ $file->id }}/' + encodeURIComponent(@json($file->file_name));
        }
        window.open(url, '_blank', 'download');
      });
    });
  </script>
@endpush
