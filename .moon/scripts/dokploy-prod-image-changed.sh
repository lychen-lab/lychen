#!/usr/bin/env bash
#
# Decide whether deploying <PROJECT>@<VERSION> to production would actually change
# the running image, or merely restart the container for an identical one.
#
# build-once-promote-many: a release re-tags EVERY dokploy project's image as
# `:vX.Y.Z`, even projects unchanged since the previous release — their `:vX.Y.Z`
# just points at the same digest as before. Redeploying such a project is a no-op
# that still restarts the container (a brief production blip) for an identical
# image. This compares the multi-arch index digest of the target tag against the
# digest of the tag the Dokploy compose is CURRENTLY pinned to, so the prod deploy
# can skip those no-ops.
#
# Source of truth = what is really running (the compose's current IMAGE_TAG, read
# via compose.one — the same endpoint dokploy-compose-deploy.sh uses), so the
# decision stays correct even if production skipped a release.
#
# Output: prints `changed=true` or `changed=false` and, when $GITHUB_OUTPUT is
# set, appends the same line so a workflow step can gate the deploy on it. Every
# decision (and the digests compared) is logged — no silent skips.
#
# Fail-safe: anything that cannot be resolved (no current tag, unreachable
# registry, missing compose) yields `changed=true` so we deploy rather than risk
# wrongly skipping. This also covers the first prod deploy and rollbacks to an
# older version (whose digest differs → deploy).
#
# Required env:
#   DOKPLOY_API_URL, DOKPLOY_API_TOKEN, DOKPLOY_COMPOSE_ID, PROJECT, VERSION
# Optional env:
#   DEFAULT_IMAGES_PREFIX  registry prefix to fall back on when the compose has
#                          no IMAGES_PREFIX (default: ghcr.io/lychen-lab/lychen/)
set -euo pipefail

: "${DOKPLOY_API_URL:?DOKPLOY_API_URL is required}"
: "${DOKPLOY_API_TOKEN:?DOKPLOY_API_TOKEN is required}"
: "${DOKPLOY_COMPOSE_ID:?DOKPLOY_COMPOSE_ID is required}"
: "${PROJECT:?PROJECT is required}"
: "${VERSION:?VERSION is required}"

DEFAULT_IMAGES_PREFIX="${DEFAULT_IMAGES_PREFIX:-ghcr.io/lychen-lab/lychen/}"

# emit <true|false> <reason> — log the decision, expose it to the workflow, exit.
emit() {
  echo "→ ${PROJECT}: changed=$1 ($2)"
  if [ -n "${GITHUB_OUTPUT:-}" ]; then
    echo "changed=$1" >>"$GITHUB_OUTPUT"
  fi
  exit 0
}

# env_value <KEY> <env-blob> — first KEY=value out of a Dokploy compose env blob
# (newline-separated KEY=value lines). Empty when absent.
env_value() {
  printf '%s\n' "$2" | grep -m1 "^$1=" | sed "s/^$1=//" || true
}

# image_digest <ref> — sha256 of the raw multi-arch index manifest, or empty when
# the ref cannot be resolved. Hashing the bytes `imagetools inspect --raw` returns
# is equivalent to the index digest, and is consistent for the target-vs-current
# equality check regardless of registry-side canonicalisation. Always returns 0
# (empty output on failure) so an unreachable registry can't abort the script —
# the caller treats an empty digest as "cannot resolve" → fail-safe deploy.
image_digest() {
  local raw
  raw="$(docker buildx imagetools inspect --raw "$1" 2>/dev/null)" || return 0
  [ -n "$raw" ] || return 0
  printf '%s' "$raw" | sha256sum | awk '{print "sha256:" $1}'
}

compose="$(curl -fsS -X GET "${DOKPLOY_API_URL}/compose.one?composeId=${DOKPLOY_COMPOSE_ID}" \
  -H 'accept: application/json' \
  -H "x-api-key: ${DOKPLOY_API_TOKEN}" || true)"

env_blob="$(printf '%s' "$compose" | jq -r '.env // ""' 2>/dev/null || true)"
current_tag="$(env_value IMAGE_TAG "$env_blob")"
images_prefix="$(env_value IMAGES_PREFIX "$env_blob")"
[ -n "$images_prefix" ] || images_prefix="$DEFAULT_IMAGES_PREFIX"

[ -n "$current_tag" ] || emit true "no current IMAGE_TAG on the compose (first deploy?)"

image_base="${images_prefix}${PROJECT}"
target_ref="${image_base}:${VERSION}"
current_ref="${image_base}:${current_tag}"

target_digest="$(image_digest "$target_ref")"
current_digest="$(image_digest "$current_ref")"
echo "  target  ${target_ref} → ${target_digest:-<unresolved>}"
echo "  current ${current_ref} → ${current_digest:-<unresolved>}"

{ [ -n "$target_digest" ] && [ -n "$current_digest" ]; } \
  || emit true "could not resolve a digest"

if [ "$target_digest" = "$current_digest" ]; then
  emit false "target digest matches the running image"
else
  emit true "target digest differs from the running image"
fi
