#!/usr/bin/env bash
# `docker buildx build` of a project's image, with the project's Dockerfile: the
# one at the root of its build context when it has one, else the one its stack
# shares (SHARED_DOCKERFILE, e.g. libs/docker/symfony/Dockerfile for the Symfony
# APIs). A project overrides the shared Dockerfile only by having its own.
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

echo "Dockerfile: $dockerfile" >&2
exec docker buildx build --file "$dockerfile" "$@"
