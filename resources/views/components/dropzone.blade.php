<div class="dropzone border rounded-3 bg-light mb-3 p-3 overflow-hidden">
  <div class="dz-message my-5" data-dz-message>
    <span class="d-block fs-6 text-body">{{ __('Drop files here to upload') }}</span>
  </div><!-- dz-message -->
</div><!-- dropzone -->

<p class="text-danger mb-3 dropzone_error" role="alert" style="display: none;"></p>

@error('document')
  <p class="text-danger mb-3" role="alert">{{ $message }}</p>
@enderror


@push('footer-scripts')
  <script src="{{ asset('new/js/dropzone/dropzone.min.js') }}?v={{ config('app.asset_version') }}"></script>
  <script type="text/javascript">
    var previewTemplateDiv = '<div class="dz-preview dz-file-preview border m-0 bg-white rounded-3 text-body shadow-sm overflow-hidden">';
          previewTemplateDiv += '<div class="d-flex align-items-center justify-content-start align-self-stretch p-1 position-relative">';
            previewTemplateDiv += '<figure class="m-0 rounded-3 overflow-hidden position-relative flex-shrink-0">';
              previewTemplateDiv += '<div class="dz-error-mark"><span><i></i></span></div><div class="dz-success-mark"><span><i></i></span></div>';
              previewTemplateDiv += "<img data-dz-thumbnail class='w-100 h-100' onerror=\"this.onerror=null;this.src='{{ asset('new/images/Image-not-found.png') }}';\" />";
              previewTemplateDiv += '<i class="fal fa-file-alt border rounded-3 overflow-clip d-flex align-items-center justify-content-center position-absolute top-0 start-0 bg-white fs-1 w-100 h-100 simple-icon-doc preview-icon"></i>';
            previewTemplateDiv += '</figure><figcaption class="flex-grow-1 align-self-stretch d-flex align-items-start justify-content-between flex-column">';
              previewTemplateDiv += '<div class="file_name d-block text-body"><span class="d-block text-body" data-dz-name></span><small class="d-block text-danger" id="error_message"></small></div>';
              previewTemplateDiv += '<div class="d-block text-secondary dz-size" data-dz-size />';
              previewTemplateDiv += '<div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div>';
              previewTemplateDiv += '<div class="dz-error-message"><span data-dz-errormessage></span></div>';
            previewTemplateDiv += '</figcaption><a href="#/" class="removeImg d-flex align-items-center justify-content-center rounded-circle bg-danger text-white position-absolute" data-dz-remove><i class="fal fa-times"></i></a>';
        previewTemplateDiv += '</div></div>';
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
            var files = {!! json_encode($documents) !!}
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
        previewTemplate: previewTemplateDiv
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
