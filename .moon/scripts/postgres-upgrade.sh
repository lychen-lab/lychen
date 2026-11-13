#!/usr/bin/env bash
# Upgrades an API's local PostgreSQL volume in place to the major of its
# compose.yml image (moon <api>:db-upgrade): the official PostgreSQL 18 image
# refuses a data directory from an older major. pgautoupgrade, in one-shot
# mode, runs pg_upgrade --link, moves the data to the 18+ layout (18/docker)
# and reindexes, then exits; on an empty or an already upgraded volume it does
# nothing. One way: dump the database first if its data matters.
set -euo pipefail

project="${1:?usage: postgres-upgrade.sh <compose project, e.g. espace-api>}"
volume="${project}_database_data"
image="pgautoupgrade/pgautoupgrade:${PGAUTO_TAG:-18-alpine}"

if ! docker volume inspect "$volume" >/dev/null 2>&1; then
  echo "No volume ${volume}: nothing to upgrade."
  exit 0
fi

# pg_upgrade needs the old server shut down cleanly. No-op if it is not running.
docker compose -p "$project" stop database

# The same defaults as compose.yml's database service. POSTGRES_DB matters:
# pgautoupgrade opens the old cluster on it (it defaults to the user's name).
docker run --rm \
  -e PGAUTO_ONESHOT=yes \
  -e POSTGRES_DB="${POSTGRES_DB:-app}" \
  -e POSTGRES_USER="${POSTGRES_USER:-userlocal}" \
  -e POSTGRES_PASSWORD="${POSTGRES_PASSWORD:-PasswordForLocal!}" \
  -v "${volume}:/var/lib/postgresql" \
  "$image"

echo "${volume} is ready for ${image#*:}: moon ${project}:up"
