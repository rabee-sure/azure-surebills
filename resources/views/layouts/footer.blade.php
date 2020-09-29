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
      <script src="{{ asset('js/app.js') }}" defer></script>
      <script src="{{ asset('js/jbootstrap.js') }}"></script>
      <script src="{{ mix('/js/all.js') }}" ></script> 
      <script src="{{ asset('js/dore.script.js') }}"></script>
    @else
      <script src="{{ asset('js/app.js') }}" ></script>
      <script src="{{ asset('js/jbootstrap.js') }}"></script>
      <script src="{{ mix('/js/all.js') }}" defer></script> 
      <script src="{{ asset('js/dore.script.js') }}"></script>
    @endif

<script>
    window._locale = '{{ app()->getLocale() }}';
    window._translations = {!! cache('translations') !!};
</script>
<script type="text/javascript" src="{{ asset('vendor/jsvalidation/js/jsvalidation.js')}}"></script>
