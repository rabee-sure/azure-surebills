# PCI DSS 11.6 Operating Procedure (Mastercard Payment Page)

## Purpose
This document defines the operating controls for detecting unauthorized changes to Mastercard payment page content and responding to alerts.

## Control Components
- Asset inventory: `security/payment-page-assets.json`
- Approved baseline: `security/payment-page-manifest.sha256`
- Build/update script: `scripts/generate-payment-page-manifest.sh`
- Verification script: `scripts/verify-payment-page-manifest.sh`
- Host checker: `scripts/check-payment-integrity.sh`
- CSP reporting endpoint: `POST /api/csp/report`
- Nginx snippets:
  - `deploy/nginx/payment-csp-report-only.conf`
  - `deploy/nginx/payment-csp-enforce.conf`

## Deployment Checklist
1. Update payment assets and regenerate manifest:
   - `scripts/generate-payment-page-manifest.sh`
2. Validate manifest before merge:
   - `scripts/verify-payment-page-manifest.sh`
3. Install host checker:
   - Copy systemd files from `deploy/systemd/`
   - Enable timer: `systemctl enable --now payment-integrity-check.timer`
4. Enable CSP report-only include in Nginx payment location.
5. Validate Nginx:
   - `nginx -t`
   - `systemctl reload nginx`
6. Review CSP reports for at least 1-2 weeks.
7. Switch to enforcement snippet.

## Tamper Alert Response Runbook
1. **Triage**
   - Inspect `/var/log/surebills/payment_integrity.log`
   - Identify changed file, expected hash, actual hash.
2. **Containment**
   - Remove affected host from load balancer if mismatch is unexplained.
   - Block new deployments until root cause is identified.
3. **Eradication**
   - Redeploy last approved release artifact.
   - Re-run `scripts/check-payment-integrity.sh` on host.
4. **Recovery**
   - Re-enable host in rotation after clean validation.
5. **Post-Incident**
   - Create incident ticket with timeline and root cause.
   - Attach integrity logs, alert payloads, and remediation evidence.

## Evidence Retention
- Keep integrity logs and CSP violation logs for at least 12 months.
- Store CI job results and release manifests per build artifact.
- Keep monthly control test results and quarterly tabletop records.

## Control Testing Cadence
- Monthly: execute tamper simulation test.
- Quarterly: incident tabletop exercise.
- After each major payment-page release: verify CSP and integrity checks.
