<footer class="page-footer"> --}}
  <div class="footer-content">
    <div class="container-fluid">
      <div class="row">
        <div class="col-12 col-sm-6">
          <p class="mb-0 text-muted">Go Pay © All rights reserved </p>
        </div>
      </div>
    </div>
  </div>
</footer>

<script src="{{ asset('js/jbootstrap.js') }}"></script>
<script src="{{ mix('/js/all.js') }}" defer></script> 
<script src="{{ asset('js/dore.script.js') }}"></script>
<script src="{{ asset('js/app.js') }}" defer></script>
<script>
    window._locale = '{{ app()->getLocale() }}';
    window._translations = {!! cache('translations') !!};
</script>
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
