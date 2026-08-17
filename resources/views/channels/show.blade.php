@extends('layouts.app')

@section('title', __('Channel') . ' ' . $channel->name)

@section('content')

  <channel-applications :channel_id="{{$channel->id}}" :channel_name="'{{$channel->name}}'"> </channel-applications>
@endsection

@push('footer-scripts')
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      document.addEventListener('click', function(e) {
        const btn = e.target.closest('button');
        if (!btn || btn.disabled) return;
        const modal = btn.closest('.modal');
        if (!modal) return;
        const modalId = modal.id;
        if (!['modal-create-application', 'modal-edit-application', 'modal-delete-application'].includes(modalId)) return;
        if (btn.classList.contains('btn-label-secondary')) return;

        const isDelete = modalId === 'modal-delete-application';
        const loadingText = isDelete ? '{{ __("Deleting...") }}' : '{{ __("Saving...") }}';

        btn.disabled = true;
        btn.setAttribute('data-original-html', btn.innerHTML);
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>' + loadingText;

        const resetBtn = function() {
          if (btn.disabled && btn.getAttribute('data-original-html')) {
            btn.innerHTML = btn.getAttribute('data-original-html');
            btn.removeAttribute('data-original-html');
            btn.disabled = false;
          }
        };

        $(modal).one('hidden.bs.modal', resetBtn);
        setTimeout(resetBtn, 10000);

        var observer = new MutationObserver(function(mutations) {
          if (!btn.getAttribute('data-original-html')) return;
          var hasErrors = modal.querySelector('.form-control.is-invalid') || modal.querySelector('.invalid-feedback.d-block');
          if (hasErrors) {
            observer.disconnect();
            resetBtn();
          }
        });
        var modalBody = modal.querySelector('.modal-body');
        if (modalBody) {
          observer.observe(modalBody, { childList: true, subtree: true, attributes: true, attributeFilter: ['class'] });
          setTimeout(function() { observer.disconnect(); }, 8000);
        }
      }, true);
    });
  </script>
@endpush
