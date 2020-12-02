<div class="dropzone">
    <div class="dz-message" data-dz-message><span>{{ __('Drop files here to upload') }}</span></div>
</div>

<p class="text-danger mt-2 dropzone_error" role="alert" style="display: none;"></p>

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
        maxFilesize: 5,
        maxFiles: 5,
        headers: {
          'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        success: function (file, response) {
          $('form').append('<input type="hidden" name="document[]" value="' + response.name + '">')
          uploadedDocumentMap[file.name] = response.name;
        },
        error: function (file, errorMessage ) {
          var id = Math.random(); // returns a random number
          file.previewElement.classList.add('error_file')
          file.previewElement.setAttribute("id", id)
          var x = document.getElementById(id)
          if(errorMessage.error)
          {
              x.querySelector('#error_message').innerHTML = errorMessage.error.file[0];
          }
          else
          {
            x.querySelector('#error_message').innerHTML = errorMessage;
          }

        },
        removedfile: function (file) {
            $(".dropzone_error").hide();
            file.previewElement.remove()
            var name = ''
            if (typeof file.file_name !== 'undefined') {
                name = file.file_name
            } else {
                name = uploadedDocumentMap[file.name]
            }
            $('form').find('input[name="document[]"][value="' + name + '"]').remove();

            @if($documents)
                var files = {!! json_encode($documents) !!}
                if(files.length > 0)
                {
                    $('.dropzone')[0].dropzone.options.maxFiles = 5-$('.dropzone')[0].dropzone.files.length;
                }
            @endif
            console.log($('.dropzone')[0].dropzone.files.length);
        },

        init: function () {
          @if($documents)
            var files =
              {!! json_encode($documents) !!}
            for (var i in files) {

              var file = files[i]
              $('.dropzone')[0].dropzone.files.push(file);
              this.options.addedfile.call(this, file)
              if(file.mime_type.includes("image")){
                this.options.thumbnail.call(this, file, '/storage/'+file.id+'/'+file.file_name)
              }

              file.previewElement.classList.add('dz-complete')
              file.previewElement.setAttribute("id", file.id)
              $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')

              file.previewElement.addEventListener("click", function(click) {
                var preview_file = files.find(x => x.id == this.getAttribute("id")) ;
                window.open('/storage/'+preview_file.id+'/'+preview_file.file_name, '_blank');
              });
              $('.dropzone')[0].dropzone.options.maxFiles = 5-files.length;
            }
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
        previewTemplate: '<div class="dz-preview dz-file-preview mb-3"><div class="d-flex flex-row "><div class="p-0 w-30 position-relative"><div class="dz-error-mark"><span><i></i></span></div><div class="dz-success-mark"><span><i></i></span></div><div class="preview-container"><img data-dz-thumbnail class="img-thumbnail border-0" /><i class="simple-icon-doc preview-icon" ></i></div></div><div class="pl-3 pt-2 pr-2 pb-1 w-70 dz-details position-relative"><div><span data-dz-name></span> <span id="error_message"></span> </div><div class="text-primary text-extra-small" data-dz-size /><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div></div><a href="#/" class="remove" data-dz-remove><i class="glyph-icon simple-icon-trash"></i></a></div>'
      });

    }

  </script>
@endpush

 @push('header-css')
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
    <style type="text/css">
    .error_file{
      background: #e6cccc !important;
    }
    #error_message {
    font-size: 9px;
    color: #7d0909;
    overflow: hidden;
    white-space: none;
    text-overflow: ellipsis;
    line-height: 1;
    display: block;
    margin: -3px 0 0 3px;
}

  </style>
@endpush
