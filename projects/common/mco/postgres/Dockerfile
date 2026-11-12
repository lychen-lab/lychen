# PostgreSQL image of the MCO stack: the official Debian image plus the PGDG
# packages of pgBackRest, pgaudit, PoWA and its statistics extensions.
#
# The APIs' own databases run on Alpine; this one is Debian (glibc), so it must
# never reuse their volumes (musl and glibc sort text differently, which
# corrupts text indexes): every overlay mounts a fresh `database_data_mco_pg18`.
#
# Same major as the APIs (PostgreSQL 18). A new major means new data volumes
# (an 18 server cannot open a 16 data directory, and pg_upgrade would need the
# old extensions' libraries too) and `stanza-upgrade` for pgBackRest, which
# instance/pgbackrest-loop.sh runs by itself.
#
# Built by every compose file that uses it (the three API overlays and the PoWA
# repository of common-mco) under one tag, `lychen-local/mco-postgres:<version>`.
ARG POSTGRES_VERSION=18.6-trixie
FROM postgres:${POSTGRES_VERSION}

# PG_MAJOR comes from the base image.
RUN apt-get update \
 && apt-get install -y --no-install-recommends \
      pgbackrest \
      postgresql-${PG_MAJOR}-hypopg \
      postgresql-${PG_MAJOR}-pg-qualstats \
      postgresql-${PG_MAJOR}-pg-stat-kcache \
      postgresql-${PG_MAJOR}-pg-track-settings \
      postgresql-${PG_MAJOR}-pg-wait-sampling \
      postgresql-${PG_MAJOR}-pgaudit \
      postgresql-${PG_MAJOR}-powa \
 && rm -rf /var/lib/apt/lists/* \
 && mkdir -p /var/lib/pgbackrest /var/log/pgbackrest /var/spool/pgbackrest /etc/pgbackrest \
 && chown -R postgres:postgres /var/lib/pgbackrest /var/log/pgbackrest /var/spool/pgbackrest
