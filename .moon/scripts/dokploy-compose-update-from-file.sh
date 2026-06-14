#!/bin/bash

set -euo pipefail

# The compose id is provided either directly (DOKPLOY_COMPOSE_ID) or, when the
# service was resolved/created by dokploy-provision, via COMPOSE_ID_FILE.
DOKPLOY_COMPOSE_ID="${DOKPLOY_COMPOSE_ID:-}"
if [ -z "$DOKPLOY_COMPOSE_ID" ] && [ -n "${COMPOSE_ID_FILE:-}" ] && [ -f "$COMPOSE_ID_FILE" ]; then
  DOKPLOY_COMPOSE_ID="$(cat "$COMPOSE_ID_FILE")"
fi
if [ -z "$DOKPLOY_COMPOSE_ID" ]; then
  echo "Error: DOKPLOY_COMPOSE_ID is empty and no id found at ${COMPOSE_ID_FILE:-<unset>}" >&2
  exit 1
fi

# Prefer the environment-specific compose file. The APIs ship compose.staging.yml
# and compose.prod.yml (compose.production.yml is also accepted); anything else
# falls back to COMPOSE_FILE (compose.yml), e.g. the template-generated frontends.
PROJECT_ROOT="${MOON_PROJECT_ROOT:-$PWD}"
case "${DOKPLOY_ENVIRONMENT:-}" in
  production) envCandidates=("compose.prod.yml" "compose.production.yml") ;;
  staging) envCandidates=("compose.staging.yml") ;;
  *) envCandidates=() ;;
esac
if [ "${#envCandidates[@]}" -gt 0 ]; then
  for candidate in "${envCandidates[@]}"; do
    if [ -f "${PROJECT_ROOT}/${candidate}" ]; then
      COMPOSE_FILE="${PROJECT_ROOT}/${candidate}"
      break
    fi
  done
fi

echo "> Get content from $COMPOSE_FILE"
if [ ! -f "$COMPOSE_FILE" ]; then
  echo "Error: Compose file not found: $COMPOSE_FILE" >&2
  exit 1
fi

composeContent=$(<"$COMPOSE_FILE")
if [ -z "$composeContent" ]; then
  echo "Error: Compose file is empty: $COMPOSE_FILE" >&2
  exit 1
fi

# sourceType "raw" tells Dokploy to deploy from this inline file. A compose
# created through the API defaults to sourceType "github", so we must set it
# explicitly (it is a no-op for composes that are already raw).
payload=$(jq -nc \
  --arg composeId "$DOKPLOY_COMPOSE_ID" \
  --arg composeFile "$composeContent" \
  '{composeId: $composeId, sourceType: "raw", composeFile: $composeFile}')

echo "> Update through Dokploy API"
response=$(curl -sS -X POST "${DOKPLOY_API_URL}/compose.update" \
  -H "Content-Type: application/json" \
  -H "x-api-key: ${DOKPLOY_API_TOKEN}" \
  -d "$payload")

# Check for an error in the response body
if echo "$response" | jq -e '.error' >/dev/null 2>&1; then
  echo "Error: Dokploy API returned an error" >&2
  echo "Response: $response" >&2
  exit 1
fi

echo "> Compose updated successfully"
