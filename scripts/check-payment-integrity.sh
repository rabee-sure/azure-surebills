#!/usr/bin/env bash
set -euo pipefail

SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
APP_ROOT="${APP_ROOT:-$(cd "${SCRIPT_DIR}/.." && pwd)}"
ASSET_FILE="${ASSET_FILE:-${APP_ROOT}/security/payment-page-assets.json}"
MANIFEST_FILE="${MANIFEST_FILE:-${APP_ROOT}/security/payment-page-manifest.sha256}"
LOG_FILE="${LOG_FILE:-${APP_ROOT}/storage/logs/payment_integrity.log}"
HOSTNAME_VAL="$(hostname -f 2>/dev/null || hostname)"
ALERT_WEBHOOK_URL="${ALERT_WEBHOOK_URL:-}"

mkdir -p "$(dirname "${LOG_FILE}")"

if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required." >&2
  exit 1
fi

if [[ ! -f "${ASSET_FILE}" || ! -f "${MANIFEST_FILE}" ]]; then
  echo "Required integrity files are missing." >&2
  exit 1
fi

now_iso="$(date -u +"%Y-%m-%dT%H:%M:%SZ")"
status="ok"
mismatches=()

declare -A expected
while read -r checksum rel_file; do
  expected["${rel_file}"]="${checksum}"
done < "${MANIFEST_FILE}"

mapfile -t files < <(jq -r '.localFiles[]' "${ASSET_FILE}")

for rel_file in "${files[@]}"; do
  abs_file="${APP_ROOT}/${rel_file}"
  if [[ ! -f "${abs_file}" ]]; then
    status="tamper_detected"
    mismatches+=("{\"file\":\"${rel_file}\",\"error\":\"missing_file\"}")
    continue
  fi

  actual="$(sha256sum "${abs_file}" | awk '{print $1}')"
  expected_sum="${expected[${rel_file}]:-}"
  if [[ -z "${expected_sum}" || "${actual}" != "${expected_sum}" ]]; then
    status="tamper_detected"
    mismatches+=("{\"file\":\"${rel_file}\",\"expected\":\"${expected_sum}\",\"actual\":\"${actual}\"}")
  fi
done

mismatches_json="[]"
if [[ "${#mismatches[@]}" -gt 0 ]]; then
  mismatches_json="[$(IFS=,; echo "${mismatches[*]}")]"
fi

event="{\"timestamp\":\"${now_iso}\",\"host\":\"${HOSTNAME_VAL}\",\"status\":\"${status}\",\"mismatches\":${mismatches_json}}"
if [[ "${status}" != "ok" ]]; then
  echo "${event}" >> "${LOG_FILE}"
fi

if [[ "${status}" != "ok" && -n "${ALERT_WEBHOOK_URL}" ]]; then
  curl -sS -X POST -H "Content-Type: application/json" --data "${event}" "${ALERT_WEBHOOK_URL}" >/dev/null || true
fi

if [[ "${status}" != "ok" ]]; then
  echo "Payment integrity mismatch detected." >&2
  exit 2
fi

echo "Payment integrity check passed."
