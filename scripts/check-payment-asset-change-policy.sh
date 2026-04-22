#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ASSET_FILE="${ROOT_DIR}/security/payment-page-assets.json"
MANIFEST_PATH="security/payment-page-manifest.sha256"

if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required to parse ${ASSET_FILE}" >&2
  exit 1
fi

BASE_REF="${1:-origin/main}"
HEAD_REF="${2:-HEAD}"

mapfile -t tracked_assets < <(jq -r '.localFiles[]' "${ASSET_FILE}")
changed_files="$(git diff --name-only "${BASE_REF}" "${HEAD_REF}")"

if [[ -z "${changed_files}" ]]; then
  echo "No changed files found between ${BASE_REF} and ${HEAD_REF}."
  exit 0
fi

asset_changed=0
manifest_changed=0

while IFS= read -r changed; do
  [[ "${changed}" == "${MANIFEST_PATH}" ]] && manifest_changed=1

  for asset in "${tracked_assets[@]}"; do
    if [[ "${changed}" == "${asset}" ]]; then
      asset_changed=1
      break
    fi
  done
done <<< "${changed_files}"

if [[ "${asset_changed}" -eq 1 && "${manifest_changed}" -eq 0 ]]; then
  echo "A payment page asset changed but ${MANIFEST_PATH} was not updated." >&2
  exit 1
fi

echo "Payment asset change policy check passed."
