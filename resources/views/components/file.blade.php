<div class="dz-preview dz-file-preview border m-0 bg-white rounded-3 text-body shadow-sm overflow-hidden">
  <div class="d-flex align-items-center justify-content-start align-self-stretch p-1 position-relative">

    <figure class="m-0 rounded-3 overflow-hidden position-relative flex-shrink-0">
        <img src="{{ $file['url'] }}"
             class="w-100 h-100"
             style="object-fit: cover;" />
    </figure>

    <figcaption class="flex-grow-1 align-self-stretch d-flex align-items-start justify-content-between flex-column">

        <div class="file_name d-block text-body">
            <span class="d-block text-body">
                {{ $file['name'] }}
            </span>
        </div>

        <div class="d-block text-secondary">
            <a href="{{ $file['url'] }}" target="_blank" class="text-decoration-none">
                {{ __('View File') }}
            </a>
        </div>

    </figcaption>

  </div>
</div>

<input type="hidden" name="document[]" value="{{ $file['name'] }}">
