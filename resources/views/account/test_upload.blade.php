@extends('layouts.app')

@section('title', __('Change Password'))

@push('header-css')
  <link rel="stylesheet" href="{{ asset('css/dropzone.min.css') }}" />
@endpush

@section('content')
  <div class="card mb-4">
      <div class="card-body">
          <h5 class="mb-4">Dropzone</h5>
          <form action="/file-upload">
              <div class="dropzone">
              </div>
          </form>
      </div>
  </div>

@endsection

@push('footer-scripts')
      <script src="{{ asset('js/dropzone.min.js') }}"></script>

      <script type="text/javascript">
        if ($().dropzone && !$(".dropzonex").hasClass("disabled")) {
          $(".dropzone").dropzone({
            url: "/images-upload",
            maxFilesize: 2, // MB
            addRemoveLinks: true,
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
              @if(isset($project) && $project->document)
                var files =
                  {!! json_encode($project->document) !!}
                for (var i in files) {
                  var file = files[i]
                  this.options.addedfile.call(this, file)
                  file.previewElement.classList.add('dz-complete')
                  $('form').append('<input type="hidden" name="document[]" value="' + file.file_name + '">')
                }
              @endif
            },
            thumbnailWidth: 160,
            previewTemplate: '<div class="dz-preview dz-file-preview mb-3"><div class="d-flex flex-row "><div class="p-0 w-30 position-relative"><div class="dz-error-mark"><span><i></i></span></div><div class="dz-success-mark"><span><i></i></span></div><div class="preview-container"><img data-dz-thumbnail class="img-thumbnail border-0" /><i class="simple-icon-doc preview-icon" ></i></div></div><div class="pl-3 pt-2 pr-2 pb-1 w-70 dz-details position-relative"><div><span data-dz-name></span></div><div class="text-primary text-extra-small" data-dz-size /><div class="dz-progress"><span class="dz-upload" data-dz-uploadprogress></span></div><div class="dz-error-message"><span data-dz-errormessage></span></div></div></div><a href="#/" class="remove" data-dz-remove><i class="glyph-icon simple-icon-trash"></i></a></div>'
          });
        }
      </script>
@endpush

