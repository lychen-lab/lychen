#!/usr/bin/env bash
#
# Deploy a Dokploy compose, optionally pinned to a specific image tag.
#
# When IMAGE_TAG is set, fetch the compose's CURRENT env from Dokploy, replace
# only the IMAGE_TAG entry (keeping IMAGES_PREFIX and everything else), write the
# full env back via compose.update, then deploy. This is deliberately defensive:
# it never relies on whether compose.update merges or replaces the env (it always
# sends the complete env), and it aborts if compose.one returns an unexpected
# shape rather than risk clobbering the env.
#
# When IMAGE_TAG is unset, just deploy (compose files default the tag to :latest).
#
# Required env: DOKPLOY_API_URL, DOKPLOY_API_TOKEN, DOKPLOY_COMPOSE_ID
# Optional env: IMAGE_TAG
set -euo pipefail

: "${DOKPLOY_API_URL:?DOKPLOY_API_URL is required}"
: "${DOKPLOY_API_TOKEN:?DOKPLOY_API_TOKEN is required}"
: "${DOKPLOY_COMPOSE_ID:?DOKPLOY_COMPOSE_ID is required}"

dokploy() {
  # dokploy <METHOD> <path> [json-body]
  local method="$1" path="$2" body="${3:-}"
  if [ -n "$body" ]; then
    curl -fsS -X "$method" "${DOKPLOY_API_URL}/${path}" \
      -H 'accept: application/json' \
      -H 'Content-Type: application/json' \
      -H "x-api-key: ${DOKPLOY_API_TOKEN}" \
      -d "$body"
  else
    curl -fsS -X "$method" "${DOKPLOY_API_URL}/${path}" \
      -H 'accept: application/json' \
      -H "x-api-key: ${DOKPLOY_API_TOKEN}"
  fi
}

if [ -n "${IMAGE_TAG:-}" ]; then
  echo "> Pinning compose ${DOKPLOY_COMPOSE_ID} to IMAGE_TAG=${IMAGE_TAG}"

  echo "  - fetching current compose (compose.one)"
  compose="$(dokploy GET "compose.one?composeId=${DOKPLOY_COMPOSE_ID}")"

  # Safety: make sure we actually got the compose object before trusting and
  # rewriting its env, so an unexpected payload can never wipe IMAGES_PREFIX & co.
  if ! printf '%s' "$compose" | jq -e 'has("composeId") or has("appName") or has("name")' >/dev/null 2>&1; then
    echo "  ! unexpected compose.one response; aborting to avoid clobbering env" >&2
    printf '%s' "$compose" | head -c 400 >&2
    echo >&2
    exit 1
  fi

  # Keep every existing env line except a prior IMAGE_TAG, then append the new
  # IMAGE_TAG. IMAGES_PREFIX, comments and all other vars are preserved.
  new_env="$(printf '%s' "$compose" | jq -r --arg tag "$IMAGE_TAG" '
    (.env // "")
    | split("\n")
    | map(select(length > 0 and (startswith("IMAGE_TAG=") | not)))
      + ["IMAGE_TAG=\($tag)"]
    | join("\n")
  ')"

  echo "  - writing env back (compose.update)"
  dokploy POST 'compose.update' \
    "$(jq -nc --arg id "$DOKPLOY_COMPOSE_ID" --arg env "$new_env" '{composeId: $id, env: $env}')" \
    >/dev/null
else
  echo "> IMAGE_TAG not set; deploying with compose defaults (:latest)"
fi

echo "> Deploying (compose.deploy)"
dokploy POST 'compose.deploy' \
  "$(jq -nc --arg id "$DOKPLOY_COMPOSE_ID" '{composeId: $id}')" \
  >/dev/null
echo "> Deployed ${DOKPLOY_COMPOSE_ID}${IMAGE_TAG:+ @ ${IMAGE_TAG}}"
