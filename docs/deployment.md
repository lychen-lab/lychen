# Deployment & Dokploy provisioning

CI/CD builds the Docker images, then **provisions and deploys** each affected
application to [Dokploy](https://dokploy.com/). Provisioning is automatic and
idempotent: the Dokploy project, environment, compose service, public domain and
the Cloudflare DNS record are created on first run and reconciled on every run —
nothing has to be clicked in the Dokploy UI and no compose ids are stored.

## Model

Everything is resolved **by name**, so the only "matching key" between the repo
and Dokploy is the naming convention below:

```
Dokploy project   = $DOKPLOY_PROJECT      domain group: tera, espace, flora, website
  └─ environment   = staging | production  one per stage (Dokploy "environments")
      └─ compose    = <moon project id>     e.g. tera-api, espace-app, website
          └─ domain  = $STAGING_DOMAIN / $PRODUCTION_DOMAIN   (optional, per env)
```

A Dokploy project groups every service of one product (api / app / website) and
splits them across a `staging` and a `production` environment. When a project is
created Dokploy automatically provides the default `production` environment; the
`staging` environment is created on demand.

## Pipeline

The deploy workflow ([.github/workflows/deploy.yml](../.github/workflows/deploy.yml))
runs `moon ci <project>:dokploy-compose-deploy` per affected `dokploy`-tagged
project, once in the `staging` GitHub environment and once in `production`. The
moon task graph chains:

```
dokploy-provision  ─▶  dokploy-compose-update  ─▶  dokploy-compose-deploy
(ensure project/env/      (upload compose.yml as       (compose.deploy)
 compose + domain + DNS,    sourceType=raw)
 write .dokploy-compose-id)
```

`dokploy-provision` ([.moon/scripts/dokploy-provision.sh](../.moon/scripts/dokploy-provision.sh)):

1. **Project** — `project.all`, create via `project.create` if missing.
2. **Environment** — `environment.byProjectId`, create `staging` via `environment.create` if missing (`production` exists by default).
3. **Compose** — `project.one`, create via `compose.create` (`composeType: docker-compose`) if missing; writes the resolved `composeId` to `.dokploy-compose-id` for the update/deploy tasks.
4. **Domain** (only if `STAGING_DOMAIN`/`PRODUCTION_DOMAIN` is set) — `domain.byComposeId`, attach via `domain.create` (`domainType: compose`, `certificateType: letsencrypt`, `https: true`) if not already present. Dokploy does **not** enforce host uniqueness, so we de-dupe before creating.
5. **DNS** (only if `CLOUDFLARE_API_TOKEN` is set) — upsert a **DNS-only** `A` record for the host → `DOKPLOY_SERVER_IPV4` in the host's Cloudflare zone.

Steps 4 and 5 are skipped gracefully when their inputs are absent, so a service
without a domain yet still gets its structure created.

## Required GitHub configuration

Set on the `staging` and `production` **GitHub Environments** (so each stage can
point at the right Dokploy server / token):

| Name | Kind | Purpose |
| --- | --- | --- |
| `DOKPLOY_API_URL` | secret | Base URL of the Dokploy API (e.g. `https://panel.example.com/api`). |
| `DOKPLOY_API_TOKEN` | secret | Dokploy **admin** API key (`x-api-key`) — must be able to create projects/environments/composes/domains, not just deploy. |
| `CLOUDFLARE_API_TOKEN` | secret | Cloudflare token with **Zone → DNS → Edit** and **Zone → Read** on the `lychen.org` zone. Omit to skip DNS management. |
| `DOKPLOY_SERVER_IPV4` | variable | Public IPv4 of the Dokploy server; the A-record target. |

Optional overrides (env vars on the provision step, rarely needed):

- `CLOUDFLARE_ZONE_NAME` — force the Cloudflare zone instead of deriving the last two labels of the host.
- `CLOUDFLARE_PROXIED` — `true` to create proxied (orange-cloud) records. Default `false`; proxied records break Let's Encrypt HTTP-01, so only use with Cloudflare origin certs.

## Per-project metadata (`moon.yml`)

Each `dokploy`-tagged project declares, in its `env:` block:

- `DOKPLOY_PROJECT` — the Dokploy project / domain group (e.g. `tera`).
- `DOMAIN_SERVICE` — the compose service the domain routes to (`<id>` for APIs, `app` for the nginx frontends).
- `DOMAIN_PORT` — container port, optional (defaults to `80`).
- `STAGING_DOMAIN` / `PRODUCTION_DOMAIN` — the public host per environment, optional.

Current deploy set:

| moon project | Dokploy project | compose | service | domains |
| --- | --- | --- | --- | --- |
| `tera-api` | `tera` | `tera-api` | `tera-api` | ✅ staging + production |
| `espace-api` | `espace` | `espace-api` | `espace-api` | ✅ staging + production |
| `flora-api` | `flora` | `flora-api` | `flora-api` | ✅ staging + production |
| `espace-app` | `espace` | `espace-app` | `app` | ⚠️ none yet |
| `espace-website` | `espace` | `espace-website` | `app` | ⚠️ none yet |
| `website` | `website` | `website` | `app` | ⚠️ none yet |

> ⚠️ The three frontends are provisioned (project + environment + compose) but
> **no domain or DNS record is created** until `STAGING_DOMAIN` / `PRODUCTION_DOMAIN`
> are added to their `moon.yml`. Add them to enable automatic domain + DNS.

## Bootstrapping / running provisioning by hand

To create the Dokploy structure for one project without deploying:

```bash
DOKPLOY_API_URL=https://panel.example.com/api \
DOKPLOY_API_TOKEN=*** \
DOKPLOY_ENVIRONMENT=staging \
CLOUDFLARE_API_TOKEN=*** \
DOKPLOY_SERVER_IPV4=203.0.113.10 \
moon run tera-api:dokploy-provision
```

It is safe to re-run: existing resources are reused, the DNS record is reconciled.

## Migrating existing services

Because resolution is by name, any service that already exists in Dokploy must
match the convention (project = domain group, environment = `staging`/`production`,
compose name = moon project id) — otherwise provisioning will create a **new**
empty compose alongside it. Before the first run, either rename the existing
Dokploy project/compose to match, or let the automation create fresh resources
and retire the old ones.
