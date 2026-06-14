#!/bin/bash
# Creates one PostgreSQL database + owner role per Lychen application.
#
# Runs automatically on FIRST cluster initialization (empty data volume) via the
# postgres docker-entrypoint, which executes files in /docker-entrypoint-initdb.d
# against a temporary local server as the `postgres` superuser.
#
# Every step is idempotent, so it is also safe to run by hand later, e.g.:
#   docker compose exec -e TERA_DB_PASSWORD=... supabase-db \
#     bash /docker-entrypoint-initdb.d/01-app-databases.sh
set -euo pipefail

superuser="${POSTGRES_USER:-postgres}"
superdb="${POSTGRES_DB:-postgres}"

create_app_db() {
  local db="$1" role="$2" password="$3"

  if [ -z "${password}" ]; then
    echo "WARN: no password for role '${role}', skipping database '${db}'" >&2
    return 0
  fi

  # Role + database. psql variable substitution (:'x' / :"x") keeps identifiers
  # and the password safely quoted; CREATE ROLE/DATABASE are guarded with \gexec
  # so re-runs are no-ops. The heredoc is quoted so the shell never expands $.
  PGPASSWORD="${POSTGRES_PASSWORD:-}" psql -v ON_ERROR_STOP=1 \
    --username "${superuser}" --dbname "${superdb}" \
    -v db="${db}" -v role="${role}" -v pwd="${password}" <<'EOSQL'
SELECT format('CREATE ROLE %I LOGIN PASSWORD %L', :'role', :'pwd')
WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = :'role')\gexec
SELECT format('CREATE DATABASE %I OWNER %I', :'db', :'role')
WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = :'db')\gexec
EOSQL

  # Schema ownership so Doctrine migrations can run DDL. Since PG15 the `public`
  # schema is not world-writable by default, so the app role must own it.
  PGPASSWORD="${POSTGRES_PASSWORD:-}" psql -v ON_ERROR_STOP=1 \
    --username "${superuser}" --dbname "${db}" \
    -v db="${db}" -v role="${role}" <<'EOSQL'
GRANT ALL PRIVILEGES ON DATABASE :"db" TO :"role";
ALTER SCHEMA public OWNER TO :"role";
GRANT ALL ON SCHEMA public TO :"role";
EOSQL

  echo "OK: database '${db}' owned by role '${role}'"
}

create_app_db "tera"   "${TERA_DB_USER:-tera_app}"     "${TERA_DB_PASSWORD:-}"
create_app_db "espace" "${ESPACE_DB_USER:-espace_app}" "${ESPACE_DB_PASSWORD:-}"
create_app_db "flora"  "${FLORA_DB_USER:-flora_app}"   "${FLORA_DB_PASSWORD:-}"
