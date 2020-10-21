<div class="dz-preview mb-3 dz-processing dz-image-preview dz-complete">
  <div class="d-flex flex-row ">
    <div class="p-0 w-30 position-relative">
      <div class="preview-container">
        <img data-dz-thumbnail="" class="img-thumbnail border-0" alt="5f8ff69177e1c_download" src="/storage/{{$file->id}}/{{$file->file_name}}">
      </div>
    </div>
    <div class="pl-3 pt-2 pr-2 pb-1 w-70 dz-details position-relative">
      <div><span data-dz-name="">{{$file->file_name}}</span> <span id="error_message"></span></div>
      <div class="text-primary text-extra-small" data-dz-size=""><strong>{{ round($file->size/1024,2) }}</strong> KB</div>
    </div>
    <a href="/storage/{{$file->id}}/{{$file->file_name}}" class="remove" data-dz-remove=""><i class="glyph-icon simple-icon-eye"></i></a>
  </div>
</div>
