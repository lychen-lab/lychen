# Zitadel

Self-hosted [Zitadel](https://zitadel.com/) identity provider, deployed through
the **same Dokploy pipeline** as every other project — no more hand-managed
compose in the Dokploy UI.

## What's here

| File | Role |
|------|------|
| [`compose.yml`](compose.yml) | Deployable stack: `zitadel` + bundled `db` (+ a profile-gated `provisioner`). Dokploy runs this file. |
| [`compose.override.yml`](compose.override.yml) | Local-only: host ports + `lychen-network`. Auto-merged by `docker compose` locally, **ignored by Dokploy** (it deploys `compose.yml` explicitly). |
| [`.env`](.env) | Reference list of every variable (empty — real values live in `.env.local` or in Dokploy). |
| [`provisioning/`](provisioning) | One-shot, idempotent client provisioner (no npm deps, stock `node:22-alpine`). |

## How it deploys (per environment)

It rides the existing `dokploy` pipeline (see [`docs/deployment.md`](../../docs/deployment.md)):

- **staging** — redeploys automatically when `projects/zitadel/**` changes (`deploy.yml`).
- **production** — ships with the release set via `Deploy | Production` (`deploy-prod.yml`).

The image is upstream (`ghcr.io/zitadel/zitadel`), so there is **nothing to build** —
only the deploy stage applies. `IMAGE_TAG` injected by the pipeline is ignored;
the version is pinned with `ZITADEL_VERSION` in the Dokploy env.

### One-time setup (per environment)

1. **Dokploy** → create a Compose service (Git provider, branch `main`,
   compose path `projects/zitadel/compose.yml`). Fill its **Environment** from
   [`.env`](.env): domain, masterkey, DB credentials, first-instance admin, and
   `ZITADEL_VERSION` (pin a real stable tag). Add a **Domain** → service
   `zitadel`, port `8080` (Dokploy wires Traefik + TLS and attaches it to
   `dokploy-network`).
2. Copy that compose's id into the GitHub **`ZITADEL_DOKPLOY_ID`** secret, in the
   matching GitHub Environment (`staging`, then `production`).

That's the whole "stop managing it by hand" step — identical to the other services.

## Local

```bash
moon zitadel:up        # start zitadel + db (http://localhost:8080)
moon zitadel:down
```

## Creating the default clients (front + back)

Zitadel **generates** client IDs — you cannot pre-set them. `FirstInstance` only
seeds the org, the human admin and a **provisioner** machine user whose **PAT** is
written to a shared volume on first init. The apps themselves are created after
startup by the provisioner:

```bash
moon zitadel:provision
```

It is **idempotent** (search-before-create) and, driven by
[`provisioning/apps.json`](provisioning/apps.json), creates inside a single
`Lychen` project:

- an **OIDC app** (User-Agent / PKCE) per frontend — `tera-app`, `espace-app`
- an **API app** (Basic auth, for token introspection) per backend — `tera-api`,
  `espace-api`, `flora-api`

It then prints (and writes to the `zitadel_clients` volume) the values to wire
back into each consumer's environment:

| Consumer | Variables |
|----------|-----------|
| frontends | `VITE_ZITADEL_CLIENT_ID`, `VITE_ZITADEL_PROJECT_RESOURCE_ID`, `VITE_ZITADEL_ISSUER` |
| backends  | `ZITADEL_CLIENT_ID`, `ZITADEL_CLIENT_SECRET`, `ZITADEL_PROJECT_ID`, `ZITADEL_DOMAIN` |

The project id is shared, which keeps token **audiences** aligned between the
SPAs and the APIs.

> Running against a deployed env: execute the provisioner where the instance
> runs (the PAT lives in its volume) — e.g. a one-off `docker compose --profile
> provision run --rm provisioner` on the server.

### Things to verify / adjust

- **`ZITADEL_VERSION`** — pin a real stable tag for staging/prod (local may stay on `latest`).
- **Redirect URIs** — `provision.mjs` builds the standard `vue-oidc-client` URIs
  with `authName: "zitadel"`. Confirm that slug matches what `@lychen/typescript-zitadel`
  generates and adjust `apps.json` if needed.
- **Feeding IDs back** — for now the IDs are emitted to logs/volume and copied
  into each consumer's Dokploy env by hand. Automating that write-back via the
  Dokploy API is a natural follow-up.
- **Production restart** — because no `zitadel` image lives in our registry, the
  prod no-op detector falls through and restarts Zitadel on each release. Harmless
  (sessions are JWTs) but worth knowing.
