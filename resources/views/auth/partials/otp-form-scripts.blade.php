<script>
  document.addEventListener('DOMContentLoaded', function() {
    var form = document.getElementById('twoStepsForm');
    var hiddenOtp = document.getElementById('otp');
    var verifyBtn = document.getElementById('otp-verify-btn');
    var inputs = document.querySelectorAll('.numeral-mask-wrapper .numeral-mask');
    var expectedLength = inputs.length;
    var submitting = false;

    function syncOtpAndToggleVerify() {
      var otpValue = '';
      inputs.forEach(function(inp) {
        otpValue += (inp.value || '').replace(/\D/g, '').slice(0, 1);
      });
      if (hiddenOtp) {
        hiddenOtp.value = otpValue.length === expectedLength ? otpValue : '';
      }
      if (verifyBtn && !submitting) {
        verifyBtn.disabled = otpValue.length !== expectedLength;
      }
    }

    var oldOtp = hiddenOtp && hiddenOtp.value ? String(hiddenOtp.value).replace(/\D/g, '').slice(0, expectedLength) : '';
    if (oldOtp && inputs.length === expectedLength) {
      for (var i = 0; i < oldOtp.length; i++) {
        inputs[i].value = oldOtp[i];
      }
    }
    syncOtpAndToggleVerify();

    inputs.forEach(function(inp) {
      inp.addEventListener('keypress', function(e) {
        if (!/^\d$/.test(e.key) && e.key !== 'Backspace' && e.key !== 'Delete') {
          e.preventDefault();
        }
      });
      inp.addEventListener('input', function() {
        this.value = this.value.replace(/\D/g, '').slice(0, 1);
        syncOtpAndToggleVerify();
      });
      inp.addEventListener('keyup', syncOtpAndToggleVerify);
    });

    var wrapper = document.querySelector('.numeral-mask-wrapper');
    if (wrapper) {
      wrapper.addEventListener('paste', function(e) {
        e.preventDefault();
        var pasted = (e.clipboardData || window.clipboardData).getData('text').replace(/\D/g, '').slice(0, expectedLength);
        for (var j = 0; j < inputs.length; j++) {
          inputs[j].value = pasted[j] || '';
        }
        syncOtpAndToggleVerify();
      });
    }

    if (form) {
      form.addEventListener('submit', function(e) {
        syncOtpAndToggleVerify();
        if (!hiddenOtp || hiddenOtp.value.length !== expectedLength) {
          e.preventDefault();
          if (verifyBtn) verifyBtn.disabled = true;
          return;
        }
        if (submitting) {
          e.preventDefault();
          return;
        }
        submitting = true;
        if (verifyBtn) verifyBtn.disabled = true;
      });
    }

    var resendForm = document.getElementById('resend-otp-form');
    var resendBtn = document.getElementById('resend-otp-btn');
    if (resendBtn) {
      var remaining = parseInt(resendBtn.getAttribute('data-remaining'), 10) || 0;
      var idleLabel = resendBtn.getAttribute('data-idle-label') || '';
      var waitLabel = resendBtn.getAttribute('data-wait-label') || ':time';

      function pad(n) {
        return String(n).padStart(2, '0');
      }

      function formatMmSs(seconds) {
        return pad(Math.floor(seconds / 60)) + ':' + pad(seconds % 60);
      }

      function renderResendCountdown() {
        if (remaining <= 0) {
          resendBtn.disabled = false;
          resendBtn.textContent = idleLabel;
          return;
        }
        resendBtn.disabled = true;
        resendBtn.textContent = waitLabel.replace(':time', formatMmSs(remaining));
      }

      renderResendCountdown();
      if (remaining > 0) {
        var resendTimer = setInterval(function() {
          remaining -= 1;
          renderResendCountdown();
          if (remaining <= 0) {
            clearInterval(resendTimer);
          }
        }, 1000);
      }
    }
    if (resendForm) {
      resendForm.addEventListener('submit', function(e) {
        if (resendBtn && resendBtn.disabled) {
          e.preventDefault();
        }
      });
    }
  });
</script>
