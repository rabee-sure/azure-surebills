#!/usr/bin/env bash
set -euo pipefail

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
ASSET_FILE="${ROOT_DIR}/security/payment-page-assets.json"
MANIFEST_FILE="${ROOT_DIR}/security/payment-page-manifest.sha256"

if ! command -v jq >/dev/null 2>&1; then
  echo "jq is required to parse ${ASSET_FILE}" >&2
  exit 1
fi

if [[ ! -f "${MANIFEST_FILE}" ]]; then
  echo "Missing manifest file: ${MANIFEST_FILE}" >&2
  exit 1
fi

tmp_expected="$(mktemp)"
tmp_current="$(mktemp)"
trap 'rm -f "${tmp_expected}" "${tmp_current}"' EXIT

sort "${MANIFEST_FILE}" > "${tmp_expected}"

mapfile -t files < <(jq -r '.localFiles[]' "${ASSET_FILE}")

for rel_file in "${files[@]}"; do
  abs_file="${ROOT_DIR}/${rel_file}"
  if [[ ! -f "${abs_file}" ]]; then
    echo "Missing file in inventory: ${rel_file}" >&2
    exit 1
  fi

  checksum="$(sha256sum "${abs_file}" | awk '{print $1}')"
  echo "${checksum}  ${rel_file}" >> "${tmp_current}"
done

sort "${tmp_current}" -o "${tmp_current}"

if ! diff -u "${tmp_expected}" "${tmp_current}"; then
  echo "Payment page manifest mismatch detected." >&2
  exit 1
fi

echo "Payment page manifest is valid."
