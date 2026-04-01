#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
TARGET_FILE="${ROOT_DIR}/app/Payment/Drivers/MasterCardHostedSession/pay.js"
CHECK_SCRIPT="${ROOT_DIR}/scripts/check-payment-integrity.sh"
BACKUP_FILE="$(mktemp)"

cp "${TARGET_FILE}" "${BACKUP_FILE}"
cleanup() {
  cp "${BACKUP_FILE}" "${TARGET_FILE}"
  rm -f "${BACKUP_FILE}"
}
trap cleanup EXIT

echo "// tamper-test $(date -u +"%Y-%m-%dT%H:%M:%SZ")" >> "${TARGET_FILE}"

if "${CHECK_SCRIPT}"; then
  echo "Tamper simulation failed: checker did not detect modifications." >&2
  exit 1
fi

echo "Tamper simulation passed: checker detected changes."
