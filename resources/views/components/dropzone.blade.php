@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/dropzone/dropzone.css') }}?v={{ config('app.asset_version') }}" />
@endpush

<div class="dropzone needsclick" id="dropzone-multi">
  <div class="dz-message needsclick" data-dz-message>{{ __('Drop files here to upload') }}</div>
</div><!-- dropzone -->

<p class="text-danger mb-3 dropzone_error" role="alert" style="display: none;"></p>

@error('document')
  <p class="text-danger mb-3" role="alert">{{ $message }}</p>
@enderror


@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/libs/dropzone/dropzone.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
   Dropzone.autoDiscover = false;
   const previewTemplateDiv = `<div class="dz-preview dz-file-preview">
<div class="dz-details">
  <div class="dz-thumbnail">
    <img data-dz-thumbnail>
    <span class="dz-nopreview">No preview</span>
    <div class="dz-success-mark"></div>
    <div class="dz-error-mark"></div>
    <div class="dz-error-message"><span data-dz-errormessage></span></div>
    <div class="progress">
      <div class="progress-bar progress-bar-primary" role="progressbar" aria-valuemin="0" aria-valuemax="100" data-dz-uploadprogress></div>
    </div>
  </div>
  <div class="dz-filename" data-dz-name></div>
  <div class="dz-size" data-dz-size></div>
</div>
<a class="dz-remove" href="javascript:undefined;" data-dz-remove>{{ __('Remove') }}</a>
</div>`;
    var uploadedDocumentMap = {}

    if ($().dropzone && !$(".dropzonex").hasClass("disabled")) {
      $(".dropzone").dropzone({
        url: "/images-upload",
        maxFilesize: 5,
        maxFiles: 5,
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
          if (response && response.name) {
            $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
            uploadedDocumentMap[file.name] = response.name;
          }
        },
        error: function (file, errorMessage ) {
          var id = Math.random(); // returns a random number
          file.previewElement.classList.add('error_file')
          file.previewElement.setAttribute("id", id)
          var errorEl = file.previewElement.querySelector('[data-dz-errormessage]');
          if (errorEl) {
            var msg = '';
            if (errorMessage && errorMessage.error && errorMessage.error.file && errorMessage.error.file[0]) {
              msg = errorMessage.error.file[0];
            } else if (typeof errorMessage === 'string') {
              msg = errorMessage;
            } else if (errorMessage && errorMessage.message) {
              msg = errorMessage.message;
            }
            errorEl.innerHTML = msg;
          }
        },
        removedfile: function (file) {
            $(".dropzone_error").hide();
            if (file.previewElement) file.previewElement.remove();
            var name = (typeof file.file_name !== 'undefined') ? file.file_name : uploadedDocumentMap[file.name];
            if (name) {
              $('form').find('input[name="document[]"][value="' + name + '"]').remove();
            }
            var dz = $('.dropzone')[0].dropzone;
            if (dz) dz.options.maxFiles = 5 - dz.files.length;
        },

        init: function () {
          @if(isset($documents) && count($documents) > 0)
            var files = {!! json_encode($documents) !!};
            var dropzoneInstance = this;
            for (var i = 0; i < files.length; i++) {
              var fileData = files[i];
              var mockFile = {
                name: fileData.file_name || fileData.name || 'file',
                size: fileData.size || 0,
                file_name: fileData.file_name,
                id: fileData.id,
                mime_type: fileData.mime_type || ''
              };
              dropzoneInstance.options.addedfile.call(dropzoneInstance, mockFile);
              dropzoneInstance.files.push(mockFile);
              if (mockFile.mime_type && mockFile.mime_type.indexOf('image') !== -1) {
                dropzoneInstance.options.thumbnail.call(dropzoneInstance, mockFile, '/storage/' + mockFile.id + '/' + mockFile.file_name);
              }
              mockFile.previewElement.classList.add('dz-complete');
              mockFile.previewElement.setAttribute("id", mockFile.id);
              if (mockFile.file_name) {
                $('form').append('<input type="hidden" name="document[]" value="' + mockFile.file_name + '">');
              }
              (function(f) {
                mockFile.previewElement.addEventListener("click", function(click) {
                  if (!click.target.closest('[data-dz-remove]')) {
                    window.open('/download/' + f.id + '/' + f.file_name, '_blank');
                  }
                });
              })(fileData);
            }
            dropzoneInstance.options.maxFiles = 5 - files.length;
          @else
            $('.dropzone')[0].dropzone.options.maxFiles = 5;
          @endif
          this.on("maxfilesexceeded", function(file){
                file.previewElement.remove();
                this.removeFile(file);
                $(".dropzone_error").show();
                $(".dropzone_error").text('{{__("reach the max num of files")}}');
            });

            this.on("sending", function(file) {

                $("button[type='submit'], input[type='submit']").attr('disabled', true);
            });

            this.on("complete", function (file) {
                $("button[type='submit'], input[type='submit']").removeAttr('disabled');
            });
        },
        thumbnailWidth: 200,
        previewTemplate: previewTemplateDiv
      });
    }

  </script>
@endpush
