
<div class="dropzone">
    <div class="dz-message" data-dz-message><span>{{ __('Drop files here to upload') }}</span></div>
</div>

@error('document')
  <p class="text-danger mt-2" role="alert">{{ $message }}</p>
@enderror


@push('footer-scripts')
  <script src="{{ asset('js/dropzone.min.js') }}"></script>
  <script type="text/javascript">
    var uploadedDocumentMap = {}
    if ($().dropzone && !$(".dropzonex").hasClass("disabled")) {
      $(".dropzone").dropzone({
        url: "/images-upload",
        maxFilesize: 2,
        maxFiles: {{ $max_files ?? 5}}, // MB
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
          $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
          uploadedDocumentMap[file.name] = response.name
        },
        removedfile: function (file) {
          file.previewElement.remove()
          var name = ''
          if (typeof file.file_name !== 'undefined') {
            name = file.file_name
          } else {
            name = uploadedDocumentMap[file.name]
          }
          $('form').find('input[name="document[]"][value="' + name + '"]').remove()
        },
        init: function () {
          @if($documents)
            var files =
              {!! json_encode($documents) !!}
            for (var i in files) {
              var file = files[i]
              this.options.addedfile.call(this, file)
              file.previewElement.classList.add('dz-complete')
              $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
            }
          @endif
        },
        thumbnailWidth: 200,
        previewTemplate: '<div class="dz-preview dz-file-preview mb-3"><div class="d-flex flex-row "><div class="p-0 w-30 position-relative"><div class="dz-error-mark"><span><i></i></span></div><div class="dz-success-mark"><span><i></i></span></div><div class="preview-container"><img data-dz-thumbnail class="img-thumbnail border-0" /><i class="simple-icon-doc preview-icon" ></i></div></div><div class="pl-3 pt-2 pr-2 pb-1 w-70 dz-details position-relative"><div><span data-dz-name></span></div><div class="text-primary text-extra-small" data-dz-size /><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div></div><a href="#/" class="remove" data-dz-remove><i class="glyph-icon simple-icon-trash"></i></a></div>'
      });
    }
  </script>
@endpush

 @push('header-css')
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
@endpush