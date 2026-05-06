<!--
    Post-payment interstitial.

    Why this exists:
      Mastercard 3DS auto-submits a form back to our `check-payment` endpoint.
      Per CSP Level 3, `form-action` is enforced on EVERY URL in the redirect
      chain that follows that submission, including server-side 302s. After a
      successful payment the controller used to do `redirect($bill->getRedirectUrl())`,
      which targets the merchant-supplied `back_url` — an arbitrary URL we
      cannot enumerate in the payment-page CSP. That tripped:

        Sending form data to '<merchant url>' violates the following CSP
        directive: form-action 'self' https://*.mastercard.com ...

      By returning THIS view (HTTP 200) from `check-payment` instead of an
      HTTP redirect, the form-submission navigation terminates on our own
      host (which is already in form-action). The subsequent navigation is
      driven by `window.location.replace()`, which is a regular navigation,
      not a form submission, so `form-action` no longer applies.

    Notes:
      - `location.replace` (not `assign`) so the user can't accidentally
        re-trigger the POST by hitting Back.
      - Inline <script> is safe here because /api/mastercard/.../check-payment
        does NOT match the payment-page CSP regex (`^/bills/[^/]+/pay`), so
        no script-src is enforced on this response.
      - <noscript> fallback so JS-disabled clients still complete the flow.
      - The URL is emitted via json so any quotes/control chars are escaped.
-->
<!DOCTYPE html>
<html lang="{{ App::getLocale() }}" dir="{{ App::isLocale('ar') ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="referrer" content="no-referrer">
    <title>{{ __('Redirecting...') }}</title>
    <style>
        html, body { margin: 0; padding: 0; height: 100%; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            display: flex; align-items: center; justify-content: center;
            background: #fafafa; color: #333;
        }
        .box { text-align: center; padding: 24px; }
        .spinner {
            width: 36px; height: 36px;
            border: 3px solid #e6e6e6; border-top-color: #00d595;
            border-radius: 50%;
            animation: spin .8s linear infinite;
            margin: 0 auto 12px;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        a { color: #00d595; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <div class="box" role="status" aria-live="polite">
        <div class="spinner" aria-hidden="true"></div>
        <p>{{ __('Redirecting...') }}</p>
        <noscript>
            <p><a href="{{ $redirectUrl }}">{{ __('Continue') }}</a></p>
        </noscript>
    </div>
    <script>
        (function () {
            var url = @json($redirectUrl);
            try {
                window.location.replace(url);
            } catch (e) {
                window.location.href = url;
            }
        })();
    </script>
</body>
</html>
