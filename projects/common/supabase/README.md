# Supabase — self-hosted PostgreSQL provider

This stack runs **Supabase Postgres** as the database engine for the Lychen APIs
(`tera`, `espace`, `flora`). It is the first half of the Supabase integration.

**Scope of this integration (intentionally narrow):**

| Concern | Decision |
| --- | --- |
| Database engine | **Supabase Postgres** (this stack) — local optional, staging/prod on the VPS |
| Authentication | **Unchanged — stays on Zitadel.** GoTrue/PostgREST/Realtime are not run |
| File storage / buckets | **Deferred to a follow-up PR** (Supabase Storage / S3) |
| App ↔ DB access | Symfony **Doctrine** over a privileged role — not PostgREST, not the anon key |

Because the apps talk to Postgres through Doctrine exactly as before, **no
application code changes** are required — only the `DATABASE_URL` each API
receives per environment.

## What runs here

| Service | Image | Default? | Purpose |
| --- | --- | --- | --- |
| `supabase-db` | `supabase/postgres:15.8.1.085` | **yes** | The Postgres engine (Postgres 15) |
| `supabase-meta` | `supabase/postgres-meta:v0.96.6` | no (`studio` profile) | Backs the Studio table/SQL editors |
| `supabase-studio` | `supabase/studio:2026.06.03-sha-0bca601` | no (`studio` profile) | Optional admin dashboard, **loopback-only** |

`init/01-app-databases.sh` runs once on first boot and creates one database +
owner role per app: `tera`/`tera_app`, `espace`/`espace_app`, `flora`/`flora_app`.
One Postgres instance, three isolated databases — mirroring local, where each API
has its own database today.

## Environment topology

| Environment | Where Postgres lives | Notes |
| --- | --- | --- |
| **Local / CI** | The existing per-API `*-database` containers (`postgres:16-alpine`) | **Unchanged.** This stack is optional locally (parity + Studio) |
| **Staging** | This stack on the VPS (Dokploy) | One instance, databases `tera`/`espace`/`flora` |
| **Production** | A separate instance of this stack on the VPS (Dokploy) | Isolated from staging; own volume, own secrets |

> CI deliberately keeps its throwaway Postgres container: the test suite relies on
> per-worker database suffixing (`dbname_suffix: _test%env(TEST_TOKEN)%`), which a
> shared remote database cannot provide. Do not point CI at Supabase.

## Local use (optional)

The committed defaults are local-safe, so no `.env` is needed:

```bash
moon common-supabase:up                       # just the database
docker compose --profile studio up -d         # database + Studio on http://127.0.0.1:54323
moon common-supabase:down
```

The database is reachable from host DB clients at `127.0.0.1:54322` and from other
containers on `lychen-network` as `supabase-db`. To run an API against it instead
of its own container, override `DATABASE_URL` in that project's `.env.local`:

```dotenv
DATABASE_URL=postgresql://tera_app:PasswordForLocal!@supabase-db:5432/tera?serverVersion=15&charset=utf8
```

## Staging / production on the VPS (Dokploy)

1. **Create a shared network** once on the VPS so the API containers can reach the
   database by name:

   ```bash
   docker network create lychen-data
   ```

2. **Deploy this folder as a Dokploy “Compose” service.** In its environment, set
   strong, unique values for every secret in [`.env.example`](./.env.example) and:

   ```dotenv
   SUPABASE_NETWORK=lychen-data
   ```

   Leave the `studio` profile **off** in production (see Security).

3. **Attach each API service to `lychen-data`** in Dokploy (Advanced → Networks).
   No change to the APIs' `compose.prod.yml` is required.

4. **Point each API at its database** by setting `DATABASE_URL` in that API's
   Dokploy environment (see matrix below), then redeploy.

5. **Run migrations** against the new database, once, from the deployed API
   container:

   ```bash
   php bin/console doctrine:migrations:migrate --no-interaction
   ```

### `DATABASE_URL` matrix

`serverVersion=15` matches `supabase/postgres` (Postgres 15) — set it correctly so
Doctrine selects the right platform.

| API | Local (unchanged) | Staging / production (Supabase) |
| --- | --- | --- |
| tera | `postgresql://userlocal:PasswordForLocal!@tera-database:5432/app?serverVersion=16&charset=utf8` | `postgresql://tera_app:<TERA_DB_PASSWORD>@supabase-db:5432/tera?serverVersion=15&charset=utf8` |
| espace | `…@espace-database:5432/app?serverVersion=16…` | `postgresql://espace_app:<ESPACE_DB_PASSWORD>@supabase-db:5432/espace?serverVersion=15&charset=utf8` |
| flora | `…@flora-database:5432/app?serverVersion=16…` | `postgresql://flora_app:<FLORA_DB_PASSWORD>@supabase-db:5432/flora?serverVersion=15&charset=utf8` |

Host `supabase-db` resolves over the shared `lychen-data` network. If you connect
across hosts instead, expose the port and append `&sslmode=require`.

## Security

- **Database**: bound to `127.0.0.1` on the host and reachable only over the
  internal Docker network. Never publish it to a public interface; rely on the
  shared network + the VPS firewall.
- **Studio**: the trimmed stack has **no auth gateway (Kong)**, so Studio is
  unauthenticated. It is bound to loopback and the `studio` profile is **off by
  default**. For VPS admin, reach it through an SSH tunnel
  (`ssh -L 54323:127.0.0.1:54323 vps`) rather than exposing it.
- **Secrets**: every password and `JWT_SECRET` in `.env.example` is a local
  placeholder. Generate fresh values per environment (`openssl rand -base64 48`)
  and store them only in Dokploy. Never commit a real `.env`.
- **Least privilege**: each API gets a role that owns only its own database, so a
  leaked credential cannot reach another app's data.

## Backups

[`scripts/backup.sh`](./scripts/backup.sh) dumps all three databases to
timestamped, gzipped files. Run it from the stack directory on the VPS and wire it
to cron:

```cron
0 3 * * * cd /path/to/supabase && BACKUP_DIR=/var/backups/supabase ./scripts/backup.sh
```

Also snapshot the `supabase_db_data` volume (or the VPS disk) for point-in-time
recovery, and copy dumps off-box.

## Notes & future work

- **Connection limits**: each FrankenPHP worker holds a connection. With three
  APIs sharing one instance, watch `max_connections`. If you approach the limit,
  add the Supabase **Supavisor** pooler (transaction mode on `6543`) and disable
  server-side prepared statements in the pooled DSN; keep migrations on the direct
  `5432` connection.
- **Version parity**: local is Postgres 16, this stack is Postgres 15. The current
  schema uses no version-specific features, so this is safe; for strict parity,
  pin the local containers to `supabase/postgres` too.
- **Not included** (kept off on purpose): GoTrue auth, PostgREST, Realtime,
  Storage, Edge Functions, Kong. Auth remains Zitadel; Storage arrives in the
  follow-up PR.
