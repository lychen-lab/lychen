#!/bin/sh
# Restore test: the latest backup plus every archived WAL, into a throwaway
# instance, then a business check and an integrity check (pg_amcheck on tables
# and indexes). Reports how fresh the restored data is and how long the
# recovery took. Runs in the pgBackRest sidecar of the instance.
set -eu

S="${PGBACKREST_STANZA:?PGBACKREST_STANZA manquant}"
U="${PGBACKREST_PG1_USER:-postgres}"
P="${MCO_RESTORE_PORT:-5499}"
D=$(mktemp -d /tmp/mco-restore-test.XXXXXX)
# Restore into the throwaway directory, not the instance's data directory.
export PGBACKREST_PG1_PATH="$D"

cleanup() {
  pg_ctl -D "$D" -m immediate stop >/dev/null 2>&1 || true
  case "$D" in /tmp/mco-restore-test.*) rm -rf "$D" ;; esac
}
trap cleanup EXIT
fail() { echo "MCO RESTORE TEST [$S] : ECHEC ($1)"; exit 1; }

t0=$(date +%s)
echo "== MCO RESTORE TEST [$S] $(date '+%F %T %z')"
pgbackrest --log-level-console=warn restore || fail "pgbackrest restore"
t1=$(date +%s)

# Only pgaudit is preloaded: the restored databases hold its extension, whose
# event triggers reject any DDL without the library (pg_amcheck's CREATE
# EXTENSION amcheck among them). It logs nothing here.
pg_ctl -D "$D" -l "$D/restore-test.log" -w -t 600 start \
  -o "-c port=$P -c listen_addresses= -c unix_socket_directories=$D -c archive_mode=off -c shared_preload_libraries=pgaudit -c pgaudit.log=none" \
  >/dev/null || fail "démarrage de l'instance restaurée"
q() { psql -X -q -At -h "$D" -p "$P" -U "$U" -d postgres -c "$1"; }
i=0
until [ "$(q 'select pg_is_in_recovery()' 2>/dev/null)" = "f" ]; do
  i=$((i + 1)); [ "$i" -gt 600 ] && fail "rejeu des WAL trop long"; sleep 1
done
t2=$(date +%s)

echo "-- backup restauré : $(pgbackrest info | grep -E '(full|diff|incr) backup:' | tail -1 | sed 's/^ *//')"
echo "-- données restaurées jusqu'à : $(q "select coalesce(pg_last_xact_replay_timestamp()::text, 'fin du backup')")"
echo "-- bases :"
q "select '   ' || datname || ' ' || pg_size_pretty(pg_database_size(datname)) from pg_database where not datistemplate order by 1"
CDB="${MCO_RESTORE_CHECK_DB:-postgres}"
CSQL="${MCO_RESTORE_CHECK_SQL:-select count(*) || ' table(s) dans le schéma public' from information_schema.tables where table_schema = 'public'}"
r=$(psql -X -q -At -h "$D" -p "$P" -U "$U" -d "$CDB" -c "$CSQL") || fail "contrôle métier"
echo "-- contrôle métier ($CDB) : $r"
pg_amcheck -h "$D" -p "$P" -U "$U" --all --install-missing --heapallindexed >"$D/amcheck.log" 2>&1 \
  || { cat "$D/amcheck.log"; fail "pg_amcheck a détecté une anomalie"; }
t3=$(date +%s)
echo "-- durées : restauration $((t1 - t0)) s, rejeu WAL $((t2 - t1)) s, contrôle $((t3 - t2)) s, total $((t3 - t0)) s"
echo "MCO RESTORE TEST [$S] : OK"
