#!/bin/bash
# Replaces the pgwatch-demo image's /pgwatch/bootstrap/init_test_monitored_db.sh,
# which its init_supervisor.sh runs once pgwatch is up: registers one pgwatch
# source per MCO instance (MCO_PGWATCH_SOURCES), reached on lychen-mco under its
# `<domain>-database` alias. Idempotent, so it runs on every start; sources added
# or edited in the UI are left alone.
set -eu

for domain in ${MCO_PGWATCH_SOURCES:-espace flora tera}; do
  psql -v ON_ERROR_STOP=1 -q --username postgres --dbname pgwatch \
    -v name="$domain" \
    -v preset="${MCO_PGWATCH_PRESET:-exhaustive_no_python}" \
    -v connstr="postgresql://pgwatch:${MCO_PGWATCH_PASSWORD:-pgwatch}@${domain}-database:5432/app" <<-'EOSQL'
	INSERT INTO pgwatch.source (name, preset_config, config, connstr)
	SELECT :'name', :'preset', null, :'connstr'
	WHERE NOT EXISTS (SELECT FROM pgwatch.source WHERE name = :'name');
EOSQL
  echo "pgwatch source ${domain} OK"
done
