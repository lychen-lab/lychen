#!/bin/sh
# pgBackRest sidecar of an instance: creates the stanza, then takes an
# incremental backup every MCO_BACKUP_INTERVAL seconds (pgBackRest turns the
# first one into a full) and runs restore-test.sh after the first backup, then
# every MCO_RESTORE_TEST_EVERY backups.
set -eu

until pg_isready -h "${PGBACKREST_PG1_SOCKET_PATH:-/var/run/postgresql}" -q; do sleep 2; done
pgbackrest stanza-create
pgbackrest check

# On a fresh volume the application creates its schema after the database is up
# (the API's entrypoint runs the Doctrine migrations): a first backup taken
# before would fail the restore test's business check. Wait for
# MCO_FIRST_BACKUP_AFTER_SQL to return true, MCO_FIRST_BACKUP_WAIT seconds at
# most, then back up anyway.
if [ -n "${MCO_FIRST_BACKUP_AFTER_SQL:-}" ]; then
  waited=0
  until [ "$(psql -X -At -h "${PGBACKREST_PG1_SOCKET_PATH:-/var/run/postgresql}" \
      -U "${PGBACKREST_PG1_USER:-postgres}" -d "${MCO_RESTORE_CHECK_DB:-postgres}" \
      -c "$MCO_FIRST_BACKUP_AFTER_SQL" 2>/dev/null)" = "t" ]; do
    if [ "$waited" -ge "${MCO_FIRST_BACKUP_WAIT:-600}" ]; then
      echo "MCO : schéma applicatif toujours absent après ${waited} s, premier backup quand même [$PGBACKREST_STANZA]"
      break
    fi
    sleep 5
    waited=$((waited + 5))
  done
fi

n=0
while true; do
  pgbackrest --type=incr backup || echo "MCO BACKUP EN ECHEC [$PGBACKREST_STANZA]"
  if [ $((n % ${MCO_RESTORE_TEST_EVERY:-24})) -eq 0 ]; then
    sh /mco/restore-test.sh || true
  fi
  n=$((n + 1))
  sleep "${MCO_BACKUP_INTERVAL:-3600}"
done
