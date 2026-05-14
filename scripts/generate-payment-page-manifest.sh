#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ASSET_FILE="${ROOT_DIR}/security/payment-page-assets.json"
MANIFEST_FILE="${ROOT_DIR}/security/payment-page-manifest.sha256"

if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required to parse ${ASSET_FILE}" >&2
  exit 1
fi

if [[ ! -f "${ASSET_FILE}" ]]; then
  echo "Asset inventory not found: ${ASSET_FILE}" >&2
  exit 1
fi

tmp_manifest="$(mktemp)"
trap 'rm -f "${tmp_manifest}"' EXIT

mapfile -t files < <(jq -r '.localFiles[]' "${ASSET_FILE}")

for rel_file in "${files[@]}"; do
  abs_file="${ROOT_DIR}/${rel_file}"
  if [[ ! -f "${abs_file}" ]]; then
    echo "Missing file in inventory: ${rel_file}" >&2
    exit 1
  fi

  checksum="$(sha256sum "${abs_file}" | awk '{print $1}')"
  echo "${checksum}  ${rel_file}" >> "${tmp_manifest}"
done

sort "${tmp_manifest}" > "${MANIFEST_FILE}"
echo "Generated ${MANIFEST_FILE}"
