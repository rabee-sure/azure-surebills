# Nginx CSP Rollout Guide for Payment Pages

## Route Scope

Apply CSP headers on payment routes only:

- `/bills/{id}/pay`
- `/bills/{id}/pay/{lang}`

## Why we use `map` instead of a payment `location` block

Laravel's payment route falls through `try_files $uri $uri/ /index.php?$query_string`,
which triggers an **internal redirect** to `/index.php`. Nginx re-evaluates locations
for the rewritten URI and applies response headers from the FINAL matching location
(here, `location ~ \.php$`). Any `add_header` placed only on the payment regex location
is therefore dropped before the response reaches the browser.

`$request_uri` preserves the **original** request URI after internal rewrites, so we
gate the CSP headers at the `server {}` level via `map` variables keyed on
`$request_uri`. Empty `map` values cause Nginx (>= 1.7.5) to skip emitting the header,
so non-payment URLs are unaffected.

## Files in this repo

| File | Purpose |
| --- | --- |
| `deploy/nginx/payment-csp-map.conf` | `map` definitions of CSP strings keyed on `$request_uri` (installs at `http {}` scope). |
| `deploy/nginx/payment-csp-report-only.conf` | `server {}`-level `add_header` directives using the report-only map variable. |
| `deploy/nginx/payment-csp-enforce.conf` | `server {}`-level `add_header` directives using the enforce map variable. |

## Step 1: Install the `map` file (once per Nginx instance)

1. Copy `deploy/nginx/payment-csp-map.conf` to the server:

   ```bash
   sudo cp deploy/nginx/payment-csp-map.conf /etc/nginx/conf.d/payment-csp-map.conf
   ```

2. Verify it is included by the `http {}` block. Most distributions auto-include
   everything under `/etc/nginx/conf.d/*.conf`. If your `nginx.conf` does not,
   add:

   ```nginx
   http {
       include /etc/nginx/conf.d/payment-csp-map.conf;
       ...
   }
   ```

## Step 2: Report-Only mode (observe)

1. Copy the report-only snippet:

   ```bash
   sudo cp deploy/nginx/payment-csp-report-only.conf /etc/nginx/snippets/payment-csp-report-only.conf
   ```

2. In the payment site's `server {}` block (NOT inside a `location`), include it:

   ```nginx
   server {
       listen 80;
       server_name dashboard-bills.example.sa;

       add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
       add_header X-Frame-Options            "SAMEORIGIN" always;
       add_header X-XSS-Protection           "1; mode=block" always;

       include /etc/nginx/snippets/payment-csp-report-only.conf;

       # ... locations (no payment-specific location needed anymore) ...
   }
   ```

3. Remove any previous `location ~ ^/bills/.*/pay` block that included the old snippet.

4. Validate and reload:

   ```bash
   sudo nginx -t
   sudo systemctl reload nginx
   ```

5. Verify the header in the browser DevTools (Network tab) on `/bills/{id}/pay`:

   ```
   content-security-policy-report-only: default-src 'self'; ...
   referrer-policy: strict-origin-when-cross-origin
   permissions-policy: camera=(), microphone=(), geolocation=()
   ```

   And confirm non-payment URLs (e.g. `/dashboard`) do NOT receive these headers.

6. Monitor violations:

   - Laravel log channel `csp_violations` -> `storage/logs/csp_violations-*.log`
   - Endpoint: `POST /api/csp/report` -> `App\Http\Controllers\Security\CspReportController`

## Step 3: Tune the policy

Fix violations by adjusting the application (inline script refactor, SRI, asset origin
changes), **not** by widening the CSP. See `strict_csp_rollout_plan` for the long-term
strict-CSP migration plan.

## Step 4: Enforce mode

1. Copy the enforce snippet:

   ```bash
   sudo cp deploy/nginx/payment-csp-enforce.conf /etc/nginx/snippets/payment-csp-enforce.conf
   ```

2. Swap the include in the `server {}` block:

   ```nginx
   # was: include /etc/nginx/snippets/payment-csp-report-only.conf;
   include /etc/nginx/snippets/payment-csp-enforce.conf;
   ```

3. Validate and reload:

   ```bash
   sudo nginx -t
   sudo systemctl reload nginx
   ```

4. Perform payment-flow regression:
   - Card flow + 3DS redirect callback
   - Apple Pay validation / `/api/mastercard/check-payment`
   - Error and cancel paths

## Rollback

Swap the include back to the report-only snippet and reload Nginx. No app-side changes
are required.

## Multi-project note (sure-bills + sure-easy)

When sure-easy calls `POST /api/bills/payment_form` on sure-bills and injects the
returned HTML into its own page, the payment scripts run under **sure-easy's origin**.
CSP headers set by sure-bills do NOT apply to that document. sure-easy must deploy the
same `payment-csp-map.conf` + snippet pair on the routes that render the injected HTML,
with its own `connect-src` allowing the sure-bills API domain.

## Notes

- Current policy keeps `'unsafe-inline'` for compatibility with existing Blade inline
  scripts. Remove it after the inline-script refactor tracked in
  `strict_csp_rollout_plan`.
- `report-uri` is relative so both report-only and enforce modes report to the same
  origin. For cross-origin reporting, switch to an absolute URL and allow the origin
  in `config/cors.php`.
