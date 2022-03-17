<div class="dz-preview border bg-white rounded-3 m-0 text-body shadow-sm overflow-hidden dz-processing dz-image-preview dz-complete">
  <div class="d-flex align-items-center justify-content-start align-self-stretch p-2 position-relative">
    <figure class="m-0 rounded-3 overflow-hidden">
      <img data-dz-thumbnail="" class="w-100 h-100" alt="5f8ff69177e1c_download" src="/storage/{{$file->id}}/{{$file->file_name}}">
      <a target="_blank" href="/storage/{{$file->id}}/{{$file->file_name}}" class="removeImg position-absolute" data-dz-remove=""><i class="fas fa-times-square"></i></a>
    </figure>
    <figcaption>
      <span data-dz-name="">{{$file->file_name}}</span>
      <span id="error_message"></span>
      <div class="text-primary text-extra-small" data-dz-size=""><strong>{{ round($file->size/1024,2) }}</strong> KB</div>
    </figcaption>
  </div>
</div>
