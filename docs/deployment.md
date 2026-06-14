# Deployment & provisioning

CI/CD builds the Docker images, then **provisions and deploys** each affected
application. Provisioning is automatic and idempotent: the Cloudflare DNS record,
the Dokploy project / environment / compose service, its **GitHub provider** wiring
and its public domain are created on first run and reconciled on every run —
nothing has to be clicked in the Dokploy UI and no compose ids are stored.

Each compose service is **GitHub-sourced**: Dokploy clones this repo and deploys
the per-environment compose file (`compose.staging.yml` / `compose.prod.yml`) from
its `composePath`. CI never uploads a compose file; it just triggers `compose.deploy`.

DNS and Dokploy are provisioned by **two independent scripts** so each side can be
run, skipped or debugged on its own:

| Script | Talks to | Responsibility |
| --- | --- | --- |
| [.moon/scripts/cloudflare-provision.sh](../.moon/scripts/cloudflare-provision.sh) | Cloudflare | Upsert a DNS-only `A` record `host → DOKPLOY_SERVER_IPV4`. |
| [.moon/scripts/dokploy-provision.sh](../.moon/scripts/dokploy-provision.sh) | Dokploy | Ensure project → environment → compose, wire the GitHub provider, attach the compose domain. |

## Model

Everything is resolved **by name**, so the only "matching key" between the repo
and Dokploy is the naming convention below:

```
Dokploy project   = $DOKPLOY_PROJECT      domain group: tera, espace, flora, website
  └─ environment   = staging | production  one per stage (Dokploy "environments")
      └─ compose    = <moon project id>     e.g. tera-api, espace-app, website
          ├─ source  = GitHub provider, composePath ./<project>/compose.<env>.yml
          └─ domain  = $STAGING_DOMAIN / $PRODUCTION_DOMAIN   (optional, per env)
```

A Dokploy project groups every service of one product (api / app / website) and
splits them across a `staging` and a `production` environment. **Each environment
holds its own compose service**, so e.g. `espace-api` becomes two Dokploy compose
services — one in `espace/staging` (composePath `compose.staging.yml` →
`staging.api.espace.lychen.org`) and one in `espace/production` (composePath
`compose.prod.yml` → `api.espace.lychen.org`).

When a project is created Dokploy automatically provides the default `production`
environment; the `staging` environment is created on demand.

## Pipeline

The deploy workflow ([.github/workflows/deploy.yml](../.github/workflows/deploy.yml))
runs `moon ci <project>:dokploy-compose-deploy` per affected `dokploy`-tagged
project, once in the `staging` GitHub environment and once in `production`. The
moon task graph chains:

```
cloudflare-provision ┐
                     ├─▶ dokploy-compose-deploy   (compose.deploy)
dokploy-provision    ┘
```

- `cloudflare-provision` — upserts the DNS record (runs first so the host resolves before Let's Encrypt is requested on deploy).
- `dokploy-provision` — ensures `project` / `environment` / `compose` (resolved by name, created if missing), **wires the GitHub provider** via `compose.update` (`sourceType: github`, `githubId`, `owner`/`repository`/`branch`, `composePath`, `triggerType: tag`, `autoDeploy: true`), attaches the domain (`domainType: compose`, Let's Encrypt) when a host is set, and writes the resolved `composeId` to a gitignored `.dokploy-compose-id`.
- `dokploy-compose-deploy` — calls `compose.deploy`, which clones the repo at `branch`, reads `composePath` and deploys. (Works regardless of `triggerType`; the `tag` trigger only governs Dokploy's own auto-deploys.)

### Per-environment compose file (composePath)

`dokploy-provision` points each compose at the environment-specific file in the repo:

- **staging** → `./<project>/compose.staging.yml`
- **production** → `./<project>/compose.prod.yml`

These files must be **committed** (the APIs ship them; the template-generated
frontends do not yet — their deploy is pending until committed). The service named
by `DOMAIN_SERVICE` must exist in that compose file and listen on `DOMAIN_PORT`.

## What to configure, and where

### GitHub → Settings → Environments → `staging` and `production`

Each GitHub environment gets its own copy, so staging and production can point at
different Dokploy targets / tokens.

| Name | Kind | Value / purpose |
| --- | --- | --- |
| `DOKPLOY_API_URL` | secret | Dokploy API base URL, e.g. `https://panel.example.com/api`. |
| `DOKPLOY_API_TOKEN` | secret | Dokploy **admin** API key (`x-api-key`) — must create projects/environments/composes/domains, not just deploy. |
| `CLOUDFLARE_API_TOKEN` | secret | Cloudflare token with **Zone → DNS → Edit** + **Zone → Read** on the `lychen.org` zone. Omit to skip DNS management. |
| `DOKPLOY_SERVER_IPV4` | variable | Public IPv4 of the Dokploy server (the A-record target). |
| `DOKPLOY_GITHUB_PROVIDER` | variable | The Dokploy GitHub provider **display name** (e.g. `Dokploy-2026-05-01-uybqmc`). Optional — only needed when more than one provider is configured; with a single provider it is auto-selected. |

Optional overrides (env vars, rarely needed):

- `DOKPLOY_GIT_OWNER` / `DOKPLOY_GIT_REPOSITORY` / `DOKPLOY_GIT_BRANCH` — default `lychen-lab` / `lychen` / `main`.
- `CLOUDFLARE_ZONE_NAME` — force the Cloudflare zone instead of deriving the last two labels of the host.
- `CLOUDFLARE_PROXIED` — `true` for proxied (orange-cloud) records. Default `false`; proxied records break Let's Encrypt HTTP-01 unless you use Cloudflare origin certs.

The old per-project `<PROJECT>_DOKPLOY_ID` secrets are **no longer used** and can be deleted.

### Per project — `moon.yml` `env:` block

| Key | Purpose |
| --- | --- |
| `DOKPLOY_PROJECT` | Dokploy project / domain group (e.g. `tera`). |
| `DOKPLOY_COMPOSE_NAME` | Compose display name in Dokploy. Optional; defaults to the moon project id. Set it to reuse a compose you named differently in the UI (e.g. `API`) instead of creating a new one. |
| `DOMAIN_SERVICE` | Compose service the domain routes to (`api` for the APIs, `app` for the nginx frontends). Must match a service in the compose file. |
| `DOMAIN_PORT` | Container port, optional (defaults to `80`). |
| `STAGING_DOMAIN` / `PRODUCTION_DOMAIN` | Public host per environment, optional. |

### Per project — compose files (you own these)

`compose.staging.yml` and `compose.prod.yml` describe the full stack per
environment and are deployed from git. The service named in `DOMAIN_SERVICE` must
exist and listen on `DOMAIN_PORT`.

## Current deploy set

| moon project | Dokploy project | compose | service | domains |
| --- | --- | --- | --- | --- |
| `tera-api` | `tera` | `tera-api` | `api` | ✅ staging + production |
| `espace-api` | `espace` | `espace-api` | `api` | ✅ staging + production |
| `flora-api` | `flora` | `flora-api` | `api` | ✅ staging + production |
| `espace-app` | `espace` | `espace-app` | `app` | ⚠️ none yet |
| `espace-website` | `espace` | `espace-website` | `app` | ⚠️ none yet |
| `website` | `website` | `website` | `app` | ⚠️ none yet |

> ⚠️ The three frontends are provisioned (project + environment + compose + provider)
> but have **no committed `compose.<env>.yml` and no `*_DOMAIN`** yet, so their
> deploy and domain/DNS are pending until both are added.

## Bootstrapping / running by hand

Each side can be run independently for one project + environment:

```bash
# DNS only
DOKPLOY_ENVIRONMENT=staging \
CLOUDFLARE_API_TOKEN=*** \
DOKPLOY_SERVER_IPV4=203.0.113.10 \
moon run espace-api:cloudflare-provision

# Dokploy structure + GitHub provider + domain only
DOKPLOY_API_URL=https://panel.example.com/api \
DOKPLOY_API_TOKEN=*** \
DOKPLOY_ENVIRONMENT=staging \
moon run espace-api:dokploy-provision
```

Both are safe to re-run: existing resources are reused and reconciled.

## Migrating existing services

Because resolution is by name, any service that already exists in Dokploy must
match the convention (project = domain group, environment = `staging`/`production`,
compose name = moon project id) — otherwise provisioning will create a **new**
empty compose alongside it. Before the first run, either rename the existing
Dokploy project/compose to match, or let the automation create fresh resources
and retire the old ones. (Provisioning will overwrite the compose's provider
config — repo / branch / composePath / trigger — to match this convention.)
