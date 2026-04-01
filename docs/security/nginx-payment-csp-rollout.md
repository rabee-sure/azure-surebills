# Nginx CSP Rollout Guide for Payment Pages

## Route Scope
Apply CSP headers on payment routes only (or payment subdomain):
- `/bills/{id}/pay`
- `/bills/{id}/pay/{lang}`

## Step 1: Report-Only Mode
1. Copy snippet to server:
   - `deploy/nginx/payment-csp-report-only.conf` -> `/etc/nginx/snippets/payment-csp-report-only.conf`
2. Include in payment location/server block.
3. Validate and reload:
   - `nginx -t`
   - `systemctl reload nginx`
4. Monitor:
   - Laravel log channel `csp_violations` (`storage/logs/csp_violations-*.log`)

## Step 2: Enforce Mode
1. Replace include with:
   - `/etc/nginx/snippets/payment-csp-enforce.conf`
2. Validate and reload Nginx.
3. Perform checkout smoke tests:
   - Card flow + 3DS redirect callback
   - Apple Pay validation/check-payment

## Notes
- Current policy keeps `'unsafe-inline'` for compatibility with existing Blade inline scripts.
- Planned hardening should migrate inline JS to static files and then remove `'unsafe-inline'`.
