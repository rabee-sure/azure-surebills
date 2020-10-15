<footer class="page-footer">
  <div class="footer-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-sm-6">
          <p class="mb-0 text-muted">SURE PAY © All rights reserved</p>
        </div>
      </div>
    </div>
  </div>
</footer>

    @if(request()->route()->getName() == 'mobile_verify' || request()->route()->getName() =='integration')
      <script src="/js/app.js?v={{ config('app.asset_version') }}" defer></script>
      <script src="/js/jbootstrap.js?v={{ config('app.asset_version') }}"></script>
      <script src="/js/all.js?v={{ config('app.asset_version') }}" ></script> 
      <script src="/js/dore.script.js?v={{ config('app.asset_version') }}"></script>
    @else
      <script src="/js/app.js?v={{ config('app.asset_version') }}" ></script>
      <script src="/js/jbootstrap.js?v={{ config('app.asset_version') }}"></script>
      <script src="/js/all.js?v={{ config('app.asset_version') }}" defer></script> 
      <script src="/js/dore.script.js?v={{ config('app.asset_version') }}"></script>
    @endif

<script>
    window._locale = '{{ app()->getLocale() }}';
    window._translations = {!! cache('translations') !!};
</script>
<script type="text/javascript" src="/vendor/jsvalidation/js/jsvalidation.js?v={{ config('app.asset_version')}}"></script>
