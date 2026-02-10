<footer class="content-footer footer bg-footer-theme">
  <div class="container-fluid">
    <div class="footer-container d-flex align-items-center justify-content-between py-4 flex-md-row flex-column">
      <div class="text-body">
        صُنع بـ ❤️ بواسطة <a href="https://surepay.sa" target="_blank" class="footer-link">SurePay</a> © {{ date('Y') }}
      </div>
      <div class="d-none d-lg-inline-block">
        <a href="{{ url('/') }}/privacy" class="footer-link me-4" target="_blank" title="{{trans('privacy_policy')}}"
          >{{trans('privacy_policy')}}</a
        >
      </div>
    </div>
  </div>
</footer>
