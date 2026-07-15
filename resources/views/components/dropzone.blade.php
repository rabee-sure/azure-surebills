@push('css_styles')
  <link rel="stylesheet" href="{{ asset('assets/v2/vendor/libs/dropzone/dropzone.css') }}?v={{ config('app.asset_version') }}" />
@endpush

<div class="dropzone needsclick" id="dropzone-documents">
  <div class="dz-message needsclick" data-dz-message>
    {{ __('Drop files here to upload') }}
  </div>
</div>

<p class="text-danger mb-3 dropzone_error" role="alert" style="display: none;"></p>

@error('document')
  <p class="text-danger mb-3" role="alert">{{ $message }}</p>
@enderror

@push('footer-scripts')
  <script src="{{ asset('assets/v2/vendor/libs/dropzone/dropzone.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    Dropzone.autoDiscover = false;
    const previewTemplateDiv = `
      <div class="dz-preview dz-file-preview">
        <div class="dz-details cursor-pointer">
          <div class="dz-thumbnail">
            <span class="dz-nopreview">
              <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 367.6 367.6" width="48px" height="48px" fill="currentColor"><path d="M328.6 81.6c-.4 0-.4-.4-.8-.8s-.4-.8-.8-1.2L258.2 2.4c-.4-.4-1.2-.8-1.6-1.2-.4 0-.4-.4-.8-.4-.8-.4-2-.8-3.2-.8H83.8C59 0 38.6 20.4 38.6 45.2v277.2c0 24.8 20.4 45.2 45.2 45.2h200c24.8 0 45.2-20.4 45.2-45.2v-238c0-.8-.4-2-.4-2.8m-68.4-54.4 44.4 50h-44.4zM313.8 322c0 16.8-13.2 30.4-30 30.4h-200c-16.8 0-30-13.6-30-30V44.8c0-16.8 13.6-30 30-30H245v69.6c0 4 3.2 7.6 7.6 7.6h61.2z"/><path d="M92.6 92h66.8c4 0 7.6-3.2 7.6-7.6s-3.2-7.6-7.6-7.6H92.6c-4 0-7.6 3.2-7.6 7.6s3.6 7.6 7.6 7.6M159.4 275.6H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6h66.8c4 0 7.6-3.2 7.6-7.6 0-4-3.6-7.6-7.6-7.6M85 134.8c0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6 0-4-3.2-7.6-7.6-7.6H92.6c-4 0-7.6 3.2-7.6 7.6M271 164.8H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6s-3.2-7.6-7.6-7.6M271 202H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6s-3.2-7.6-7.6-7.6M271 239.2H92.6c-4 0-7.6 3.2-7.6 7.6 0 4 3.2 7.6 7.6 7.6H271c4 0 7.6-3.2 7.6-7.6 0-4-3.2-7.6-7.6-7.6"/></svg>
            </span>
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
      </div>
    `;
    @php
      $maxUploadMb = 4;
      $allowedExtensions = ['png', 'jpg', 'jpeg', 'pdf', 'doc', 'docx', 'xlsx', 'csv'];
      $fileTooBigMessage = __('file max size should be less than or equal', ['value' => $maxUploadMb]);
      $invalidFileTypeMessage = __('file extension should be in', ['value' => implode(', ', $allowedExtensions)]);
    @endphp
    var uploadedDocumentMap = {}

    var maxUploadMb = {{ $maxUploadMb }};
    var maxUploadBytes = maxUploadMb * 1024 * 1024;
    var fileTooBigMessage = @json($fileTooBigMessage);

    var allowedExtensions = @json($allowedExtensions);
    var acceptedFilesList = '.' + allowedExtensions.join(',.');
    var invalidFileTypeMessage = @json($invalidFileTypeMessage);

    var dropzoneEl = document.getElementById("dropzone-documents");
    var formEl = dropzoneEl ? dropzoneEl.closest("form") : null;

    if (dropzoneEl && formEl && typeof Dropzone !== "undefined") {
      if (dropzoneEl.dropzone) {
        dropzoneEl.dropzone.destroy();
      }
      new Dropzone(dropzoneEl, {
        url: "/images-upload",
        maxFilesize: maxUploadMb,
        dictFileTooBig: fileTooBigMessage,
        acceptedFiles: acceptedFilesList,
        dictInvalidFileType: invalidFileTypeMessage,
        maxFiles: 5,
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
          var res = response;
          if (typeof res === "string") {
            try {
              res = JSON.parse(res);
            } catch (e) {
              res = null;
            }
          }
          if (res && res.name) {
            var storedPath = res.path || ("tmp/uploads/" + res.name);
            file.storedPath = storedPath;
            $(formEl).append($("<input>", { type: "hidden", name: "document[]", value: storedPath }));
            uploadedDocumentMap[file.name] = storedPath;
          }
          if (file.previewElement) {
            file.previewElement.classList.add("dz-success", "dz-complete");
          }
        },
        error: function (file, errorMessage ) {
          var fileExtension = (file.name.split('.').pop() || '').toLowerCase();
          if (file.size > maxUploadBytes || allowedExtensions.indexOf(fileExtension) === -1) {
            if (file.previewElement) file.previewElement.remove();
            this.removeFile(file);
            var errorMessage = file.size > maxUploadBytes ? fileTooBigMessage : invalidFileTypeMessage;
            $(".dropzone_error").show().text(errorMessage);
            return;
          }
          file.previewElement.classList.add('error_file');
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
            var name = (typeof file.storedPath !== 'undefined' && file.storedPath) ? file.storedPath : uploadedDocumentMap[file.name];
            if (!name && typeof file.file_name !== 'undefined') {
              name = file.file_name;
            }
            if (name) {
              var baseName = function (p) {
                try { var parts = String(p).split('/'); return parts[parts.length - 1]; } catch (e) { return p; }
              };
              $(formEl).find('input[name="document[]"]').filter(function () {
                var v = $(this).val();
                return v === name || baseName(v) === baseName(name);
              }).remove();
            }
            this.options.maxFiles = 5 - this.files.length;
        },

        init: function () {
          var dz = this;
          var uploadContext = @json($upload_context ?? null);
          @if(isset($documents) && count($documents) > 0)
            var files = {!! json_encode($documents, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!};
            for (var i = 0; i < files.length; i++) {
              var fileData = files[i];
              var storedPath = fileData.disk_relative_path || fileData.file_name || '';
              var mockFile = {
                name: fileData.file_name || fileData.name || 'file',
                size: fileData.size || 0,
                file_name: fileData.file_name,
                id: fileData.id,
                mime_type: fileData.mime_type || '',
                storedPath: storedPath,
                download_url: fileData.download_url || null
              };
              dz.options.addedfile.call(dz, mockFile);
              dz.files.push(mockFile);
              if (fileData.thumbnail_url) {
                dz.options.thumbnail.call(dz, mockFile, fileData.thumbnail_url);
              } else if (mockFile.mime_type && mockFile.mime_type.indexOf('image') !== -1) {
                dz.options.thumbnail.call(dz, mockFile, '/storage/' + mockFile.id + '/' + mockFile.file_name);
              }
              mockFile.previewElement.classList.add('dz-complete');
              mockFile.previewElement.setAttribute("id", mockFile.id);
              if (storedPath) {
                $(formEl).append($('<input>', { type: 'hidden', name: 'document[]', value: storedPath }));
              }
              (function(f) {
                mockFile.previewElement.addEventListener("click", function(click) {
                  if (!click.target.closest('[data-dz-remove]')) {
                    var a = document.createElement('a');
                    if (f.download_url) {
                      a.href = f.download_url;
                    } else if (uploadContext) {
                      a.href = '/download/merchant-document/' + encodeURIComponent(uploadContext) + '/' + encodeURIComponent(f.file_name);
                    } else {
                      a.href = '/download/' + f.id + '/' + encodeURIComponent(f.file_name);
                    }
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    a.style.display = 'none';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                  }
                });
              })(fileData);
            }
            dz.options.maxFiles = 5 - files.length;
          @else
            dz.options.maxFiles = 5;
          @endif
          this.on("maxfilesexceeded", function(file){
                file.previewElement.remove();
                this.removeFile(file);
                $(".dropzone_error").show();
                $(".dropzone_error").text('{{__("reach the max num of files")}}');
            });

            this.on("sending", function(file, xhr, formData) {
                $("button[type='submit'], input[type='submit']").attr('disabled', true);
                @isset($upload_context)
                formData.append('upload_context', @json($upload_context));
                @endisset
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
