@extends('layouts.app')

@section('title', __('Home'))

@section('content')

  @php
    $settings =  Spatie\Valuestore\Valuestore::make(storage_path('app/settings.json'));
    $mobile_number = $settings->get('mobile_number');
  @endphp

  @if(!$user->verified && !$user->mainStoreUser && $user->source == 'sure bills')
    @if($user->is_uploaded_documents)
      <div class="alert alert-warning d-flex align-items-center" role="alert">
        <span class="alert-icon rounded"><i class="icon-base ti ti-bell icon-md"></i></span>
        {{ __('Your account is being verified so that you can withdraw the collected amounts. The documentation process may take up to two business days. In the event that the documentation is not completed before :date, please contact us on :mobile', ['mobile' => $mobile_number, 'date' => $user->two_business_days]) }}
      </div><!-- alert -->
    @else
      <div class="alert alert-warning d-flex align-items-center" role="alert">
        <span class="alert-icon rounded"><i class="icon-base ti ti-bell icon-md"></i></span>
        {{ __('Your account is not verified. Please upload the necessary documents to verify your account and avoid delays in transferring dues.') }} {{__('To upload files, please click on the')}} <a href="/account" title="{{ __('Account Settings') }}" class="alert-link">{{ __('Account Settings') }}.</a>
      </div><!-- alert -->
    @endif
  @endif

  @if (session('status'))
    <div class="alert alert-success d-flex align-items-center" role="alert">
      <span class="alert-icon rounded"><i class="icon-base ti ti-check icon-md"></i></span>
      {{ session('status') }}
    </div><!-- alert -->
  @endif

  @canany(['show statement', 'show bills'])
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-6 mb-6">
      @can('show statement')
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 mb-1 me-2">
                  {{ round2($balance) }} <i class="sar-icon"></i>
                </h5>
                <p class="mb-0 text-capitalize">{{ __('electronic payment balance') }}</p>
              </div>
              <div class="card-icon text-primary">
                <svg id="Layer_3" viewBox="0 0 64 64" xmlns="http://www.w3.org/2000/svg" data-name="Layer 3" width="54px" height="54px" fill="currentColor"><path d="m53 32h-4.775a12.944 12.944 0 0 0 -.225-16.295v-7.705a7.009 7.009 0 0 0 -7-7h-24a7.009 7.009 0 0 0 -7 7v48a7.009 7.009 0 0 0 7 7h24a6.971 6.971 0 0 0 6.7-5h5.3a4 4 0 0 0 4-4v-18a4 4 0 0 0 -4-4zm2 4v16h-34v-16a2 2 0 0 1 2-2h6.7a12.968 12.968 0 0 0 16.6 0h6.7a2 2 0 0 1 2 2zm-17-1a11 11 0 1 1 11-11 11.013 11.013 0 0 1 -11 11zm-2.723-32-1.143 2h-10.268l-1.143-2zm5.723 58h-24a5.006 5.006 0 0 1 -5-5v-48a5.006 5.006 0 0 1 5-5h3.42l2 3.5a1 1 0 0 0 .868.5h11.426a1 1 0 0 0 .868-.5l2-3.5h3.418a5.006 5.006 0 0 1 5 5v5.775a12.979 12.979 0 0 0 -18.225 18.225h-4.775a4 4 0 0 0 -4 4v18a4 4 0 0 0 4 4h22.567a4.968 4.968 0 0 1 -4.567 3zm12-5h-30a2 2 0 0 1 -2-2h34a2 2 0 0 1 -2 2z"/><path d="m31 37a1 1 0 0 0 -1-1h-6a1 1 0 0 0 -1 1v4a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1zm-2 3h-4v-2h4z"/><path d="m47 36h6v2h-6z"/><path d="m23 48h6v2h-6z"/><path d="m31 48h6v2h-6z"/><path d="m39 48h6v2h-6z"/><path d="m47 48h6v2h-6z"/><path d="m35.923 28.437-4.186-4.567-1.474 1.352 5 5.454a1 1 0 0 0 .737.324h.049a1 1 0 0 0 .751-.4l9-12-1.6-1.2z"/></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 mb-1 me-2">
                  {{ round2($user->pending_balance) }} <i class="sar-icon"></i>
                </h5>
                <p class="mb-0 text-capitalize">{{ __('Pending Balance') }}</p>
              </div>
              <div class="card-icon text-warning">
                <svg id="Layer_1" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" width="54px" height="54px" fill="currentColor"><path d="m185.006 89.506c-52.658 0-95.499 42.842-95.499 95.5s42.841 95.528 95.499 95.528 95.527-42.854 95.527-95.528-42.854-95.5-95.527-95.5zm7.001 176.704v-8.694c0-3.867-3.135-7.002-7.001-7.002s-7.001 3.135-7.001 7.002v8.694c-39.36-3.369-70.807-34.827-74.174-74.201h8.665c3.867 0 7.001-3.135 7.001-7.002s-3.135-7.002-7.001-7.002h-8.665c3.368-39.359 34.815-70.806 74.174-74.173v8.664c0 3.867 3.135 7.002 7.001 7.002s7.001-3.135 7.001-7.002v-8.665c39.374 3.366 70.833 34.813 74.202 74.173h-8.665c-3.867 0-7.001 3.135-7.001 7.002s3.135 7.002 7.001 7.002h8.665c-3.368 39.375-34.828 70.834-74.202 74.202zm26.844-81.204c0 3.867-3.135 7.002-7.001 7.002h-26.844c-3.867 0-7.001-3.135-7.001-7.002v-33.959c0-3.867 3.135-7.002 7.001-7.002s7.001 3.135 7.001 7.002v26.957h19.843c3.867 0 7.001 3.135 7.001 7.002zm277.144 225.724c3.867 0 7.001-3.135 7.001-7.002v-46.148c0-3.867-3.135-7.002-7.001-7.002h-59.131c32.212-18.411 53.971-53.114 53.971-92.806 0-58.88-47.902-106.782-106.781-106.782-9.064 0-17.866 1.139-26.276 3.273-3.64-5.615-9.947-9.254-16.952-9.254h-16.902c-2.983-10.379-7.143-20.404-12.417-29.924l11.96-11.981c7.873-7.873 7.873-20.682.002-28.552l-27.98-28.009c-7.88-7.879-20.702-7.878-28.582 0l-11.983 11.983c-9.491-5.265-19.517-9.424-29.922-12.411v-16.93c0-11.129-9.054-20.184-20.183-20.184h-39.6c-11.129 0-20.183 9.055-20.183 20.184v16.93c-10.398 2.985-20.431 7.145-29.943 12.412l-11.962-11.983c-3.804-3.805-8.879-5.899-14.291-5.899s-10.487 2.095-14.293 5.901l-27.962 27.989c-3.827 3.806-5.934 8.881-5.934 14.291s2.107 10.485 5.916 14.272l11.965 11.986c-5.274 9.52-9.435 19.546-12.417 29.924h-16.901c-11.145 0-20.211 9.066-20.211 20.211v39.572c0 11.145 9.066 20.211 20.211 20.211h16.901c2.988 10.406 7.147 20.434 12.414 29.927l-11.943 11.965c-3.827 3.806-5.934 8.881-5.934 14.291s2.107 10.485 5.918 14.274l27.98 28.009c3.804 3.804 8.879 5.898 14.291 5.898s10.487-2.095 14.295-5.903l11.958-11.979c9.513 5.268 19.545 9.427 29.943 12.412v16.93c0 11.145 9.054 20.212 20.183 20.212h39.6c11.129 0 20.183-9.067 20.183-20.212v-16.909c10.459-3.016 20.492-7.18 29.948-12.428l11.958 11.979c7.879 7.879 20.702 7.879 28.582 0l2.508-2.508c8.865 12.042 20.191 22.164 33.232 29.617h-59.145c-3.867 0-7.001 3.135-7.001 7.002v39.146h-31.181c-3.867 0-7.001 3.135-7.001 7.002v46.119c0 3.867 3.135 7.002 7.001 7.002h31.181v39.146c0 3.867 3.135 7.002 7.001 7.002h223.909c3.867 0 7.001-3.135 7.001-7.002v-46.148c0-3.867-3.135-7.002-7.001-7.002h-31.181v-32.115h31.181zm-210.406-97.163c-2.379 2.381-6.399 2.38-8.775.005l-15.761-15.789c-2.28-2.285-5.827-2.715-8.587-1.039-11.176 6.782-23.339 11.831-36.149 15.007-3.124.774-5.317 3.578-5.317 6.796v22.28c0 3.423-2.772 6.208-6.18 6.208h-39.6c-3.407 0-6.18-2.785-6.18-6.208v-22.309c0-3.223-2.2-6.029-5.33-6.799-12.705-3.123-24.866-8.164-36.147-14.982-2.759-1.668-6.3-1.235-8.577 1.046l-15.757 15.785c-1.159 1.158-2.717 1.797-4.389 1.797s-3.23-.639-4.387-1.795l-27.994-28.022c-1.164-1.157-1.805-2.706-1.805-4.361s.641-3.204 1.823-4.38l15.761-15.789c2.276-2.28 2.703-5.818 1.033-8.574-6.812-11.245-11.852-23.396-14.978-36.112-.77-3.13-3.576-5.331-6.799-5.331h-22.28c-3.423 0-6.208-2.784-6.208-6.207v-39.572c0-3.423 2.785-6.207 6.208-6.207h22.28c3.223 0 6.03-2.201 6.799-5.331 3.115-12.671 8.155-24.823 14.982-36.119 1.665-2.755 1.237-6.289-1.037-8.567l-15.779-15.808c-1.164-1.157-1.805-2.706-1.805-4.361s.641-3.204 1.821-4.378l27.976-28.003c1.159-1.159 2.718-1.798 4.39-1.798s3.23.639 4.385 1.793l15.761 15.789c2.276 2.283 5.818 2.713 8.577 1.046 11.281-6.818 23.443-11.859 36.147-14.982 3.13-.77 5.33-3.576 5.33-6.799v-22.309c0-3.408 2.772-6.18 6.18-6.18h39.6c3.407 0 6.18 2.771 6.18 6.18v22.309c0 3.223 2.2 6.029 5.33 6.799 12.719 3.127 24.869 8.166 36.112 14.979 2.756 1.671 6.299 1.242 8.579-1.037l15.789-15.789c2.38-2.381 6.4-2.379 8.777-.003l27.98 28.009c2.413 2.412 2.413 6.338-.005 8.755l-15.76 15.789c-2.274 2.278-2.702 5.813-1.037 8.567 6.826 11.295 11.867 23.447 14.981 36.119.77 3.13 3.576 5.331 6.799 5.331h22.281c.651 0 1.277.118 1.872.308-38.417 16.19-65.453 54.223-65.453 98.451 0 18.499 4.729 35.912 13.039 51.1l-4.695 4.695zm5.659-55.795c0-51.158 41.633-92.778 92.806-92.778s92.778 41.62 92.778 92.778-41.62 92.806-92.778 92.806-92.806-41.632-92.806-92.806zm-12.161 106.81h209.906v32.145h-209.906zm209.906 124.412h-209.906v-32.145h209.906zm-38.183-46.148h-209.906v-32.115h209.906zm-31.317-175.699c6.389 8.236 7.695 17.721 3.677 26.704-5.004 11.187-17.704 19.562-32.115 21.398v10.073c0 3.867-3.135 7.002-7.001 7.002s-7.001-3.135-7.001-7.002v-10.387c-13.341-2.353-25.116-10.609-33.314-23.679-2.055-3.275-1.065-7.597 2.21-9.652 3.276-2.053 7.597-1.064 9.652 2.211 10.432 16.631 24.407 18.246 31.809 17.728 10.32-.722 19.769-6.236 22.978-13.41 1.86-4.157 1.2-8.331-1.96-12.406-4.032-5.201-15.815-8.378-26.21-11.181-13.136-3.541-26.719-7.202-33.63-16.119-6.388-8.234-7.693-17.717-3.676-26.698 5.007-11.193 17.717-19.572 32.142-21.406v-10.071c0-3.867 3.135-7.002 7.001-7.002s7.001 3.135 7.001 7.002v10.388c13.343 2.354 25.117 10.61 33.314 23.678 2.055 3.275 1.065 7.597-2.21 9.652-3.276 2.053-7.597 1.063-9.652-2.211-8.95-14.267-20.511-17.483-28.292-17.782-.239.004-.479-.002-.715-.02-1.015-.02-1.957.015-2.813.074-10.328.722-19.784 6.239-22.994 13.416-1.859 4.155-1.2 8.328 1.959 12.4 4.031 5.201 15.813 8.377 26.209 11.179 13.136 3.542 26.72 7.203 33.632 16.121zm-265.698 228.85c0 3.867-3.135 7.002-7.001 7.002h-48.643c-34.136 0-61.908-27.76-61.908-61.881v-65.211l-14.961 17.171c-2.54 2.916-6.962 3.22-9.878.68-2.916-2.541-3.22-6.963-.679-9.879l27.241-31.266c1.329-1.525 3.253-2.401 5.277-2.402h.002c2.023 0 3.947.875 5.276 2.399l27.27 31.266c2.542 2.915 2.239 7.338-.675 9.879-2.914 2.542-7.336 2.24-9.878-.674l-14.991-17.188v65.226c0 26.399 21.49 47.877 47.905 47.877h48.643c3.867 0 7.001 3.135 7.001 7.002zm204.408-479.993c0-3.867 3.135-7.002 7.001-7.002h48.671c34.121 0 61.88 27.76 61.88 61.881v65.195l14.963-17.157c2.542-2.915 6.964-3.215 9.879-.676 2.914 2.542 3.216 6.965.675 9.879l-27.241 31.237c-1.329 1.524-3.252 2.399-5.275 2.4h-.002c-2.022 0-3.945-.874-5.274-2.397l-27.269-31.237c-2.543-2.913-2.243-7.336.67-9.879 2.913-2.544 7.336-2.243 9.879.67l14.993 17.175v-65.21c0-26.399-21.478-47.877-47.877-47.877h-48.671c-3.867 0-7.001-3.135-7.001-7.002z"/></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 mb-1 me-2">
                  {{ round2($user->paid_cash_balance) }} <i class="sar-icon"></i>
                </h5>
                <p class="mb-0 text-capitalize">{{ __('Paid Cash Balance') }}</p>
              </div>
              <div class="card-icon text-primary">
                <svg id="Layer_1" enable-background="new 0 0 512 512" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" width="54px" height="54px" fill="currentColor"><path d="m323.3 412.8v-133.5c.2-10-8.5-18.8-18.6-18.6-60.6 0-144.2 0-204.4 0-10.1-.2-18.8 8.6-18.7 18.6v98.4l-25 10.7v-5.6c0-7.1-5.8-12.9-12.9-12.9h-29.6c-7.1 0-12.9 5.8-12.9 12.9v97.2c0 7.1 5.8 12.9 12.9 12.9h29.6c8.1.1 13.8-7.3 12.9-15.1l110.2 29.7c14 3.8 29.1 2.9 42.4-2.7l119.8-49.8c22.7-9.4 17.4-38.6-5.7-42.2zm-277.8 67.1c0 1-.8 1.8-1.8 1.8h-29.6c-1 0-1.8-.8-1.8-1.8v-97.2c0-1 .8-1.8 1.8-1.8h29.6c1 0 1.8.8 1.8 1.8zm136.8-208.2h40.3v61.5h-40.3zm-89.7 7.6c-.1-4.1 3.5-7.6 7.6-7.6h71v67.1c0 3.1 2.5 5.5 5.5 5.5h51.3c3.1 0 5.5-2.5 5.5-5.5v-67.1h71c4.1-.1 7.6 3.5 7.6 7.6v133.4c-1.2.2-115.6 24.7-116.8 25-4 1-8.3 1-12.3 0-.6-.1-1.1-.2-1.7-.4l-60.2-19.3c9.2-6.6 29.5-6.1 40.4-7.9 23.1-1.8 34-34.6 6.2-38.8 0 0-38.6-3.3-38.6-3.3-12.6-1.8-25.2-1.3-36.6 4.8v-93.5zm232.2 165.5-119.8 49.7c-11 4.6-23.6 5.4-35.3 2.2l-113.2-30.5v-65.9l45.4-19.4c7.6-3.5 16.9-2.8 25.1-1.9 0 0 39.8 3.4 39.8 3.4 4.2.4 7.6 4.1 5.8 8.4-1.9 4.7-7.5 7.8-12.3 8.3-16.8 1.8-36.2 1.2-49.8 13-5.3 4.5-3 13.5 3.6 15.3 0 0 63.9 20.3 63.9 20.3 6.5 2.1 13.3 2.1 19.9.6 0 0 115.4-24.7 115.4-24.7 15.2-3.4 28.6 12.7 11.5 21.2zm173.1-425.6h-29.6c-8.1-.1-13.8 7.3-12.9 15.1l-110.1-29.7c-14.2-3.8-29.3-2.8-42.4 2.7l-39.9 16.5c-21.3-11.8-44.4-20.1-70.9-21-2.2-.2-4.3 1.2-5.2 3.2l-28.3 62.2c-5.1 2.4-16 2.6-16.5 9.7v148.2c0 1.8.9 3.6 2.4 4.6s3.5 1.2 5.2.5c45.4-18.7 90-7.8 137.2 3.8 48.7 12.1 98.9 23.6 146.9 1 1.9-.9 3.2-2.9 3.2-5v-99.4l18.4-7.8v5.6c0 7.1 5.8 12.9 12.9 12.9h29.6c7.1 0 12.9-5.8 12.9-12.9v-97.3c0-7.1-5.8-12.9-12.9-12.9zm-302.5-5.2c54 3.4 93.3 39.9 134 71.1-5-.9-10-2-15.1-3.2-25.9-19.7-52.6-40-84.7-53.6-2.7-1.2-5.9.1-7.2 2.8-4 8.8-15.1 13.9-26.4 12.1-2.5-.4-4.8.9-5.9 3.2l-7.6 16.8c-3.4.4-6.8 1.1-10.2 1.6zm92 61.5c-30.2-7.4-61-14.6-92.2-13.4l3.4-7.5c12.7.7 24.6-4.8 31.2-14.2 20.9 9.5 39.7 21.9 57.6 35.1zm138.6 151.9c-88.7 37.9-182.1-42-272.9-9.3v-136.4c70-26 140 15.2 210.2 18.9-6.4.4-13 .9-19.1 3-2.6 1.1-5.3 1.5-8 .8-48.9-8.8-94.8-24.8-146.1-23.7-3 .1-5.4 2.5-5.4 5.5 0 9.7-8 18.9-19 22-2.4.7-4 2.8-4 5.3v62.7c-.1 3.5 3.6 6.4 7 5.3 10.2-2.4 15.6 4.5 16 13.5-.1 3 2.7 5.7 5.7 5.5 33.5-1 66.2 6.6 97.8 13.8 30.4 7 61.8 14.2 94.6 14.2 4.5.1 11.3.5 11.4-5.6 0-9.6 8-18.8 19-21.9 2.4-.7 4-2.8 4-5.3v-55.6l8.7-3.7v91zm-89.3-150.7 40.5 12.9c-8.1.2-16.3-.2-24.5-1-5.4-3.8-10.7-7.8-16-11.9zm7.5 63.9c10.5.3 52.4 6.1 62.1 3.2v47.8c-11.7 4.7-20.3 14.6-22.5 25.7-65.2 0-122.9-28.4-188.5-28.1-2-10.8-11.5-19.5-22.5-19.4v-52.5c11.7-4.7 20.3-14.6 22.5-25.8 46.8-.1 89.8 14.7 135.1 22.8-7.2 11.2.5 25.6 13.8 26.3zm111.3-28.9s-26.1 11.1-26.1 11.1c-11.6 4.5-23.1 12.1-36 10.8l-48.2-4.1c-1.9-.2-3.7-.9-4.9-2-5.5-6.7 5.1-14.2 11.4-14.8 0 0 25.8-2.8 25.8-2.8 9.4-1.2 27.1-6.6 26.8-17.6-.6-3.6-2.8-6.7-6.5-7.8l-63.8-20.2c-4.9-1.6-10.1-2.1-15.1-1.5-3.3.4-15.5-12-18.5-14.2-8.3-6.2-16.7-12.1-25.4-17.7l32.2-13.3c10.8-4.5 23.7-5.3 35.3-2.2l113.1 30.5v65.8zm44.2 17.6c0 1-.8 1.8-1.8 1.8h-29.6c-1 0-1.8-.8-1.8-1.8 0-.1 0-13.9 0-13.9 0-4.9 0-81.6 0-83.3 0-1 .8-1.8 1.8-1.8h29.6c1 0 1.8.8 1.8 1.8zm-208.8-6.9c-17.4-4.8-34 7.2-33.6 25.4-.1 19.7 18.2 39.5 38.7 39.7 41.2-3 27.9-57.7-5.1-65.1zm14.4 51.1c-19.8 13.3-49.4-21.9-31.4-38.2 20-13.2 49.3 22 31.4 38.2z" fill="currentColor"/></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 mb-1 me-2">
                  {{ round2($user->paid_bank_transfer_balance) }} <i class="sar-icon"></i>
                </h5>
                <p class="mb-0 text-capitalize">{{ __('Paid Bank Transfer Balance') }}</p>
              </div>
              <div class="card-icon text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 512 512" width="54px" height="54px" fill="currentColor"><path d="M473.993 504.5h14.87c8.636 0 15.637-7.001 15.637-15.637v-.018c0-8.636-7.001-15.637-15.637-15.637H251.311c-8.636 0-15.637 7.001-15.637 15.637v.018c0 8.636 7.001 15.637 15.637 15.637h192.734M300.304 473.207h-32.695c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c-.001 8.099-6.566 14.664-14.664 14.664M472.565 350.146H439.87c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.098-6.565 14.664-14.664 14.664M472.565 473.207H439.87c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.099-6.565 14.664-14.664 14.664" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="M299.785 376.138v67.725h-31.658v-58.11M354.258 350.146h31.658v93.717h-31.658zM440.389 350.146h31.658v93.717h-31.658zM353.739 320.801h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.099-6.565 14.664-14.664 14.664h-32.695c-6.454 0-11.934-4.17-13.894-9.962" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="M386.434 473.207h-32.695c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.099-6.565 14.664-14.664 14.664M343.322 239.71l15.577-9.247a21.92 21.92 0 0 1 22.375 0l117.842 69.954c9.581 5.688 5.548 20.384-5.594 20.384H349.396M156.68 284.607H23.137c-8.636 0-15.637-7.001-15.637-15.637v-.018c0-8.636 7.001-15.637 15.637-15.637h138.889M72.13 130.253H39.435c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664H72.13c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.099-6.566 14.664-14.664 14.664M72.13 253.314H39.435c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664H72.13c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.099-6.566 14.664-14.664 14.664M244.392 130.253h-32.695c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c-.001 8.099-6.566 14.664-14.664 14.664" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="M39.953 130.253h31.658v93.717H39.953zM126.084 130.253h31.658v93.717h-31.658zM212.215 197.867v-67.614h31.658v58.166M158.261 130.253h-32.695c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c8.099 0 14.664 6.565 14.664 14.664v.017c0 8.099-6.566 14.664-14.664 14.664" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="M158.261 253.314h-32.695c-8.099 0-14.664-6.565-14.664-14.664v-.017c0-8.099 6.565-14.664 14.664-14.664h32.695c6.426 0 11.887 4.134 13.868 9.887M68.633 47.43 12.884 80.524c-9.581 5.688-5.548 20.384 5.594 20.384h246.871c11.142 0 15.175-14.696 5.594-20.384L153.101 10.57a21.92 21.92 0 0 0-22.375 0L94.059 32.337M322.044 91.135c69.73 0 126.258 56.527 126.258 126.258" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="m469.508 194.837-20.806 24.569-24.569-20.805M189.956 482.865c-69.73 0-126.258-56.527-126.258-126.258" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="m42.492 379.163 20.806-24.569 24.569 20.805" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><ellipse cx="256" cy="287" rx="99.32" ry="99.323" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><ellipse cx="256" cy="287" rx="68.049" ry="68.051" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="M268.214 259.922s-6.951-6.412-17.666-3.184c-6.527 1.966-11.004 9.865-8.926 16.489 2.486 7.924 13.897 11.005 20.73 13.42 17.424 6.159 10.983 30.85-6.741 30.85-10.435 0-16.226-4.368-16.226-4.368M255.611 317.496v8.97M255.611 247.534v8.322" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 mb-1 me-2">
                  {{ round2($user->paid_machine_balance) }} <i class="sar-icon"></i>
                </h5>
                <p class="mb-0 text-capitalize">{{ __('Paid Machine Balance') }}</p>
              </div>
              <div class="card-icon text-primary">
                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 512 512" width="54px" height="54px" fill="currentColor"><path d="M465.253 90.867C485.823 92.522 502 109.74 502 130.737v125.529M222 177.943v-47.206c0-21.034 16.235-38.275 36.857-39.878M502 346.266V462c0 22.091-17.909 40-40 40H262c-22.091 0-40-17.909-40-40v-15.074M259.679 111.352V10l25.58 20.737L310.84 10l25.58 20.737L362 10l25.58 20.737L413.16 10l25.58 20.737L464.321 10v101.352zM270 195.681v-43.09h184V256H291.892M275.28 304H302M345.919 304h32M422 304h32M270 352h32M345.919 352h32M422 352h32M270 400h32M345.919 400h32M422 400h32M270 448h36.8M343.6 448h36.638M417.2 448H454" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="m342 200.793 18.188 18.188 34.233-34.234M149.157 152.609l117.726 42.849c22.502 8.19 34.105 33.071 25.915 55.574l-80.957 222.427c-8.19 22.502-33.071 34.105-55.574 25.915l-117.726-42.85c-22.502-8.19-34.105-33.071-25.915-55.574l80.957-222.427c8.191-22.502 33.072-34.104 55.574-25.914M85.087 430.899l51.996 18.925" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/><path d="M149.221 153.074 153 180.798a27.76 27.76 0 0 0 18.007 22.332l50.751 18.472a27.76 27.76 0 0 0 28.149-5.532l20.824-18.906M207.531 340.773c-18.672-40.043-66.271-57.368-106.314-38.695M171.279 357.678c-9.336-20.022-33.135-28.684-53.157-19.348M306.8 70.737h110.4M502 301.266" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10"/></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="d-flex align-items-center {{app()->getLocale() == 'en' ? 'flex-row-reverse justify-content-end' : 'justify-content-start'}} gap-1 mb-1 me-2">
                  {{ $total_paid }} <i class="sar-icon"></i>
                </h5>
                <p class="mb-0 text-capitalize">{{ __('Total Paid') }}</p>
              </div>
              <div class="card-icon text-success">
                <svg id="Layer_1" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg" data-name="Layer 1" width="54px" height="54px" fill="currentColor"><path d="m277.482 169.173c7.685 15.484 16.028 30.41 24.945 46.1 33.815-51.13 106.1-58.657 149.6-15.157 45.179 45.18 35 120.7-20 152.6h22.742a29.037 29.037 0 0 1 18.3 51.6 29.006 29.006 0 0 1 0 45.08 29.059 29.059 0 0 1 -18.3 51.6h-142.71a29.125 29.125 0 0 1 -25.865-15.842l-7.5.257h-126.5c-42.546 0-78.1-10.223-100.306-35.337-20.267-22.925-25.611-53.856-23.177-85.5 2.571-33.411 30.538-82.714 58.1-131.3 12.521-22.073 24.957-44 34.718-63.886-21.168-9.8-20.915-39.984.413-49.421l-23.016-39.876c-10.67-18.491 4.829-38.927 25.574-38.927h21.254c-1.9-5.217-3.53-8.551-1.854-14.415 2.857-9.995 14.446-18.1 24.888-15.118l30.491 8.718 30.5-8.718c13.7-3.917 29.65 11.356 24.612 25.2l-1.578 4.335h21.255c20.745 0 36.242 20.441 25.572 38.927l-23.015 39.874c21.114 9.343 21.642 39.125.861 49.208zm17.555 59.448c-10.115-17.724-21.814-38.491-30.727-56.747h-129.545c-10.227 21.246-23.388 44.448-36.644 67.817-26.838 47.314-54.07 95.321-56.42 125.854-5.639 73.272 34.991 106.824 110.492 106.824h126.5q2.356 0 4.312-.074a29.011 29.011 0 0 1 10.757-22.9 29.007 29.007 0 0 1 0-45.08 29.059 29.059 0 0 1 18.3-51.6h22.738a97.113 97.113 0 0 1 -39.764-124.095zm147.77-19.286a83.99 83.99 0 1 0 0 118.781 83.992 83.992 0 0 0 0-118.781zm-33.749 40.271a25.653 25.653 0 0 0 -19.12-24.8v-11.066a6.521 6.521 0 0 0 -13.042 0v11.06a25.627 25.627 0 0 0 -11.609 6.673c-16.115 16.115-4.635 43.771 18.131 43.771a12.6 12.6 0 1 1 -12.6 12.6 6.521 6.521 0 0 0 -13.042 0 25.653 25.653 0 0 0 19.119 24.8v11.063a6.521 6.521 0 0 0 13.042 0v-11.059a25.607 25.607 0 0 0 11.609-6.673c16.116-16.116 4.635-43.771-18.129-43.771a12.6 12.6 0 1 1 12.6-12.6 6.521 6.521 0 0 0 13.042 0zm-97 206.311c-14.035 0-21.49 17.145-11.308 27.327a15.968 15.968 0 0 0 11.308 4.71h142.718c14.033 0 21.492-17.143 11.308-27.327a15.968 15.968 0 0 0 -11.308-4.71zm0-45.08c-14.035 0-21.49 17.145-11.308 27.327a15.964 15.964 0 0 0 11.308 4.711h142.718c14.034 0 21.491-17.144 11.308-27.327a15.964 15.964 0 0 0 -11.308-4.711zm142.717-45.079h-142.716c-14.035 0-21.49 17.144-11.308 27.327a15.968 15.968 0 0 0 11.308 4.71h142.717c14.034 0 21.491-17.144 11.308-27.327a15.968 15.968 0 0 0 -11.308-4.71zm-194.538 25.452a86.207 86.207 0 0 1 -121.912 0 6.52 6.52 0 1 0 -9.221 9.221 99.243 99.243 0 0 0 140.354 0 6.52 6.52 0 0 0 -9.221-9.221zm-121.912-121.91a86.206 86.206 0 0 1 121.912 0 6.521 6.521 0 0 0 9.221-9.222 99.244 99.244 0 0 0 -140.354 0 6.52 6.52 0 1 0 9.221 9.22zm87.064 41.37a26.121 26.121 0 0 0 -19.589-25.29v-11.457a6.521 6.521 0 1 0 -13.042 0v11.457a26.073 26.073 0 0 0 -11.94 6.826c-16.41 16.409-4.719 44.569 18.461 44.569a13.067 13.067 0 1 1 -13.066 13.066 6.521 6.521 0 0 0 -13.042 0 26.123 26.123 0 0 0 19.587 25.288v11.456a6.521 6.521 0 1 0 13.042 0v-11.456a26.08 26.08 0 0 0 11.94-6.827c16.409-16.409 4.72-44.569-18.461-44.569a13.067 13.067 0 1 1 13.066-13.066 6.521 6.521 0 0 0 13.042 0zm-13.429-193 10.69-16.609a6.508 6.508 0 1 1 10.953 7.031l-6.165 9.578h35.518l25.421-44.042c5.458-9.455-3.222-19.415-14.313-19.415h-26l-7.974 21.908a6.5 6.5 0 0 1 -12.227-4.432l14.297-39.289c1.224-3.363-4.82-9.376-8.819-8.233l-32.183 9.2a6.478 6.478 0 0 1 -3.663.028l-32.273-9.228c-4-1.143-10.045 4.867-8.82 8.233l14.3 39.286a6.5 6.5 0 0 1 -12.227 4.432l-7.975-21.912h-26c-11.091 0-19.772 9.958-14.314 19.415l25.421 44.042h35.518l-6.165-9.578a6.508 6.508 0 0 1 10.954-7.031l10.69 16.609h4.522l.41-.636-5.741-8.92a6.507 6.507 0 1 1 10.953-7.03l2.526 3.923 2.541-3.948a6.508 6.508 0 1 1 10.953 7.031l-5.755 8.942.41.636zm-7.707 13.042a6.377 6.377 0 0 1 -.7 0h-70.7c-12.314 0-18.87 15.047-9.927 23.991a14.018 14.018 0 0 0 9.927 4.136h132.86c12.314 0 18.87-15.047 9.927-23.991a14.018 14.018 0 0 0 -9.927-4.136z" fill-rule="evenodd"/></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
      @endcan
      @can('show bills')
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="mb-1 me-2">{{ $total_bills }}</h5>
                <p class="mb-0 text-capitalize">{{ __('Total Bills') }}</p>
              </div>
              <div class="card-icon text-info">
                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 682.667 682.667" width="54px" height="54px" fill="currentColor"><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z"/></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="M0 0c16.57 0 30 13.43 30 30v299.5L-62.51 422h-175.53c-16.57 0-30-13.43-30-30" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(405.02 82.5)"/><path d="M0 0h60l-92.51 92.5v-60" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(375.02 412)"/><path d="M0 0v112.594l-92.51 92.5h-175.53c-16.57 0-30-13.43-30-30" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(405.02 269.406)"/><path d="M0 0c16.57 0 30 13.43 30 30v151.906" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(375.02 52.5)"/><path d="M0 0h60l-92.51 92.5v-60" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(345.02 382)"/><path d="M0 0v-206.034c0-16.568 13.431-30 30-30h238.036c16.569 0 30 13.432 30 30v314.497l-92.503 92.503H30c-16.569 0-30-13.431-30-30V35" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(76.982 243.534)"/><path d="M0 0v-62.503c0-16.569 13.432-30 30-30h62.503z" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(282.515 444.5)"/><path d="M0 0v-148.12" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(188.5 390.06)"/><path d="M0 0c0-11.946 8.715-21.631 19.466-21.631h25.955c10.751 0 19.467 9.685 19.467 21.631v14.422c0 11.947-8.716 21.631-19.467 21.631l-25.954-.001C8.716 36.052 0 45.737 0 57.684v14.42c0 11.947 8.715 21.631 19.466 21.631h25.955c10.751 0 19.467-9.684 19.467-21.631" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(156.056 279.948)"/><path d="M0 0h208.036" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(121.982 196.94)"/><path d="M0 0h208.036" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(121.982 151.94)"/><path d="M0 0h208.036" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(121.982 106.94)"/><path d="M0 0h133.036" style="fill:none;stroke:currentColor;stroke-width:15;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(121.982 61.94)"/></g></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
        <div class="col">
          <div class="card h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
              <div class="card-title mb-0">
                <h5 class="mb-1 me-2">{{ $total_paid_bills }}</h5>
                <p class="mb-0 text-capitalize">{{ __('Total Paid Bills') }}</p>
              </div>
              <div class="card-icon text-info">
                <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 682.667 682.667" width="54px" height="54px" fill="currentColor"><defs><clipPath id="a" clipPathUnits="userSpaceOnUse"><path d="M0 512h512V0H0Z"/></clipPath></defs><g clip-path="url(#a)" transform="matrix(1.33333 0 0 -1.33333 0 682.667)"><path d="m0 0-214.733-63.563c-10.473-3.097-16.447-14.101-13.35-24.575l27.623-93.31c2.465-8.339 9.932-13.831 18.15-14.162h-130.684v440.291H0V91.353" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(387.866 205.623)"/><path d="m0 0 175.885 52.067V-.802H-4.741A20.2 20.2 0 0 1 0 0" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(211.981 10.815)"/><path d="M0 0v-51.696h-261.638v-388.595h-51.356V0Z" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(336.51 502)"/><path d="m0 0-262.329-77.648c-10.474-3.101-21.479 2.877-24.579 13.351l-27.62 93.315c-3.101 10.473 2.877 21.478 13.351 24.579l262.329 77.648c10.475 3.1 21.479-2.878 24.579-13.351l27.621-93.315C16.452 14.105 10.475 3.101 0 0" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(474.313 88.467)"/><path d="m0 0-17.205 58.125L5.581 64.87c8.821 2.611 18.089-2.424 20.701-11.245 2.611-8.821-2.424-18.09-11.245-20.701L-7.749 26.18" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(221.695 53.638)"/><path d="m0 0 5.22 64.763 40.269-51.298" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(282.19 71.544)"/><path d="m0 0 17.204-58.126" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(347.956 154.228)"/><path d="m0 0-23.254-6.883-17.205 58.125 23.254 6.884C-8.642 60.66.354 55.773 2.89 47.21l8.025-27.115C13.45 11.531 8.563 2.535 0 0" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(426.987 114.403)"/><path d="m0 0 37.107 10.983" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(283.152 83.478)"/><path d="M0 0" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(387.866 251.022)"/><path d="M0 0h-27.799c-7.745 0-14.023-6.278-14.023-14.022v-6.995c0-7.745 6.278-14.023 14.023-14.023h13.776C-6.279-35.04 0-41.318 0-49.063v-7.559c0-7.744-6.279-14.023-14.023-14.023h-27.799" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(180.677 395.678)"/><path d="M0 0v-12.052" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(160.01 407.73)"/><path d="M0 0v-9.653" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(159.766 325.033)"/><path d="M0 0h92.265" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(236.917 398.717)"/><path d="M0 0h92.265" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(236.917 360.356)"/><path d="M0 0h92.265" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(236.917 321.994)"/><path d="M329.182 231.922H138.855v41.785h190.327z" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1"/><path d="M0 0h51.414" style="fill:none;stroke:currentColor;stroke-width:20;stroke-linecap:round;stroke-linejoin:round;stroke-miterlimit:10;stroke-dasharray:none;stroke-opacity:1" transform="translate(140.968 187.62)"/></g></svg>
              </div><!-- card-icon -->
            </div><!-- card-body -->
          </div><!-- card -->
        </div><!-- col -->
      @endcan
    </div><!-- row -->
  @endcanany

  @can('show bills')
    <div class="row row-cols-1 row-cols-md-1 row-cols-lg-2 g-6">
      <div class="col">
        <bills-paid-amount :user="{{$user}}"></bills-paid-amount>
      </div><!-- col -->
      <div class="col">
        <bills-paid-count :user="{{$user}}"></bills-paid-count>
      </div><!-- col -->
      <div class="col">
      <bills-count :user="{{$user}}"></bills-count>
      </div><!-- col -->
      <div class="col">
        <div class="card h-100">
          <div class="card-header d-flex justify-content-between">
            <div class="card-title mb-0">
              <h5 class="mb-0">{{__('Latest Bills') }}</h5>
            </div>
            @if($latest->count() > 0)
              <a href="/bills?dont_update_statuses=true" title="{{__('View all') }}" class="btn btn-sm rounded-2 btn-primary waves-effect waves-light flex-shrink-0 m-0">{{__('View all') }}</a>
            @endif
          </div>
          <div class="card-body">
            @if($latest->count() > 0)
              <ul class="list-unstyled mb-0">
                @foreach($latest as $bill)
                  @include('latest_bill_item')
                @endforeach
              </ul>
            @else
              <div class="no_bills_available text-capitalize">{{ __('No Bill Matched The Given Criteria.') }}</div>
            @endif
          </div>
        </div>
      </div><!-- col -->
    </div><!-- row -->
  @endcan




  @can('create bills')
      @if((auth()->user()->mainStoreUser == null && count(auth()->user()->channels) == 0) || (auth()->user()->mainStoreUser && count(auth()->user()->mainStoreUser->channels) == 0))
        <a href="{{ route('bills.create')}}" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ __('Create a bill')}}" class="addNewBillBtn position-fixed rounded-circle d-block shadow"></a>
      @endif
    @endcan



@endsection

@can('show bills')
@push('footer-scripts')

<script src="{{ asset('new/js/chartjs/Chart.min.js') }}?v={{ config('app.asset_version') }}" defer></script>
    <script type="text/javascript">
      Echo.channel('home').listen('NewMessage', (e) => {
          console.log(e.message);
      });
    </script>
@endpush
@endcan
