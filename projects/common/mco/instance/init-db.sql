-- Technical roles and extensions of an instance, run by init.sh as the
-- instance's superuser. Idempotent.
\getenv pgwatch_pw MCO_PGWATCH_PASSWORD
\getenv databasus_pw MCO_DATABASUS_PASSWORD
\getenv powa_pw MCO_POWA_PASSWORD

-- pgwatch: reads the statistics views
SELECT 'CREATE ROLE pgwatch' WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'pgwatch')\gexec
ALTER ROLE pgwatch WITH LOGIN PASSWORD :'pgwatch_pw';
GRANT pg_monitor TO pgwatch;

-- Databasus: read-only logical backups
SELECT 'CREATE ROLE databasus' WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'databasus')\gexec
ALTER ROLE databasus WITH LOGIN PASSWORD :'databasus_pw';
GRANT pg_read_all_data TO databasus;

-- PoWA: remote collector, no superuser. pg_read_all_data is for powa-web, which
-- connects with this role to EXPLAIN the queries under HypoPG's hypothetical
-- indexes: without SELECT on their tables, no index suggestion.
SELECT 'CREATE ROLE powa' WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'powa')\gexec
ALTER ROLE powa WITH LOGIN PASSWORD :'powa_pw';
GRANT pg_read_all_stats TO powa;
GRANT pg_read_all_data TO powa;

-- pgaudit: every write on a table granted to mco_auditor is logged
SELECT 'CREATE ROLE mco_auditor NOLOGIN' WHERE NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'mco_auditor')\gexec

-- extensions in the application database
CREATE EXTENSION IF NOT EXISTS pgaudit;
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
CREATE EXTENSION IF NOT EXISTS pg_qualstats;
CREATE EXTENSION IF NOT EXISTS hypopg;

-- local "powa" database, queried by the collector
SELECT 'CREATE DATABASE powa' WHERE NOT EXISTS (SELECT FROM pg_database WHERE datname = 'powa')\gexec
\c powa
CREATE EXTENSION IF NOT EXISTS btree_gist;
CREATE EXTENSION IF NOT EXISTS pg_stat_statements;
CREATE EXTENSION IF NOT EXISTS pg_qualstats;
CREATE EXTENSION IF NOT EXISTS pg_stat_kcache;
CREATE EXTENSION IF NOT EXISTS pg_wait_sampling;
CREATE EXTENSION IF NOT EXISTS pg_track_settings;
CREATE EXTENSION IF NOT EXISTS hypopg;
CREATE EXTENSION IF NOT EXISTS powa;
GRANT EXECUTE ON ALL FUNCTIONS IN SCHEMA public TO powa;
GRANT SELECT ON ALL TABLES IN SCHEMA public TO powa;
