#!/usr/bin/env bash
# `docker buildx build` of a project's image, with the project's Dockerfile: the
# one at the root of its build context when it has one, else the one its stack
# shares (SHARED_DOCKERFILE, e.g. libs/docker/symfony/Dockerfile for the Symfony
# APIs). A project overrides the shared Dockerfile only by having its own.
#
# The Caddy configuration its stack shares (SHARED_CADDY_CONFIG, e.g.
# libs/caddy/symfony) is passed as the `caddy-config` build context, which the
# shared Dockerfiles copy from (COPY --from=caddy-config). A project adds to it or
# replaces it from its own build context: see libs/caddy/README.md.
#
# Takes the arguments of `docker buildx build`, the build context first.
set -euo pipefail

context="${1:?usage: docker-buildx.sh <build context> [buildx build options...]}"

if [[ -f "$context/Dockerfile" ]]; then
  dockerfile="$context/Dockerfile"
elif [[ -n "${SHARED_DOCKERFILE:-}" ]]; then
  dockerfile="$SHARED_DOCKERFILE"
else
  echo "docker-buildx.sh: no Dockerfile in $context, and no SHARED_DOCKERFILE for its stack" >&2
  exit 1
fi

options=(--file "$dockerfile")
if [[ -n "${SHARED_CADDY_CONFIG:-}" ]]; then
  options+=(--build-context "caddy-config=$SHARED_CADDY_CONFIG")
fi

echo "Dockerfile: $dockerfile${SHARED_CADDY_CONFIG:+, Caddy config: $SHARED_CADDY_CONFIG}" >&2
exec docker buildx build "${options[@]}" "$@"
