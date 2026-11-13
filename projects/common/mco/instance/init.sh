#!/bin/sh
# One-shot init of an instance (idempotent): technical roles and extensions on
# the instance, then its registration in the PoWA repository of common-mco.
# PGHOST/PGUSER/PGPASSWORD/PGDATABASE name the instance, MCO_SERVER_ALIAS its
# name in PoWA. Needs psql >= 15 (\getenv).
set -eu

export MCO_SERVER_HOST="$PGHOST"
# Re-runs on every `up`: no NOTICE for what already exists.
export PGOPTIONS='-c client_min_messages=warning'
psql -X -q -v ON_ERROR_STOP=1 -f /mco/init-db.sql

# common-mco runs in another compose project: depends_on cannot wait for it.
until pg_isready -h mco-powa-repo -q; do sleep 2; done
PGHOST=mco-powa-repo PGUSER=powa PGPASSWORD="$MCO_POWA_REPO_PASSWORD" PGDATABASE=powa \
  psql -X -q -At -v ON_ERROR_STOP=1 -o /dev/null -f /mco/register-powa.sql

echo "MCO init OK pour $MCO_SERVER_ALIAS ($MCO_SERVER_HOST)"
