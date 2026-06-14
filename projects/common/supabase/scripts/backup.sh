#!/bin/bash
# Dump every Lychen application database to a timestamped, gzipped file.
#
# Intended to run from this stack's directory on the VPS (cron-friendly):
#   0 3 * * * cd /path/to/supabase && BACKUP_DIR=/var/backups/supabase ./scripts/backup.sh
#
# Env:
#   BACKUP_DIR   destination directory (default: ./backups)
#   DB_SERVICE   compose service name of the database (default: supabase-db)
#   APP_DBS      space-separated database list (default: "tera espace flora")
#   RETENTION    delete dumps older than N days (default: 14; 0 disables)
set -euo pipefail

backup_dir="${BACKUP_DIR:-./backups}"
db_service="${DB_SERVICE:-supabase-db}"
app_dbs="${APP_DBS:-tera espace flora}"
retention="${RETENTION:-14}"
stamp="$(date -u +%Y%m%dT%H%M%SZ)"

mkdir -p "${backup_dir}"

for db in ${app_dbs}; do
  out="${backup_dir}/${db}-${stamp}.sql.gz"
  echo "Dumping '${db}' -> ${out}"
  # pg_dump as the postgres superuser inside the db container; PGPASSWORD comes
  # from the container's own POSTGRES_PASSWORD env.
  docker compose exec -T "${db_service}" \
    sh -c 'PGPASSWORD="$POSTGRES_PASSWORD" pg_dump -U postgres -d '"${db}"' --no-owner' \
    | gzip > "${out}"
done

if [ "${retention}" -gt 0 ]; then
  echo "Pruning dumps older than ${retention} day(s)"
  find "${backup_dir}" -name '*.sql.gz' -type f -mtime "+${retention}" -delete
fi

echo "Done."
