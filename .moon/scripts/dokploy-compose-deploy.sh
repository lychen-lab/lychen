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

echo '> Deploy through Dokploy API'

curl -sS -X POST "${DOKPLOY_API_URL}/compose.deploy" \
  -H "accept: application/json" \
  -H "Content-Type: application/json" \
  -H "x-api-key: ${DOKPLOY_API_TOKEN}" \
  -d "{\"composeId\": \"${DOKPLOY_COMPOSE_ID}\"}" || exit 1

echo '> Deployed'
