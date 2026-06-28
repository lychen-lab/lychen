# Zitadel

Self-hosted [Zitadel](https://zitadel.com/) identity provider, deployed through
the **same Dokploy pipeline** as every other project — no more hand-managed
compose in the Dokploy UI.

## Topology

| Environment | Instance | Notes |
|---|---|---|
| **dev** | one **shared, already-deployed** instance | Local dev machines **and** CI point at it — one source of truth, no per-developer config drift. Kept as-is; **not** re-provisioned here (it already holds the clients the apps' `.env.local` reference). |
| **staging** | its own instance | Deployed by the pipeline from [`compose.yml`](compose.yml). |
| **production** | its own instance | Deployed by the pipeline from [`compose.yml`](compose.yml). |

> **Local development does not run Zitadel.** Apps/APIs point at the shared **dev**
> instance (`https://dev.account.lychen.fr`) via their `.env.local`.

## Files

| File | Role |
|------|------|
| [`compose.yml`](compose.yml) | Deployable stack: `zitadel` + bundled `db` (+ a profile-gated `provisioner`). Dokploy runs this file. |
| [`.env`](.env) | Reference list of every variable (empty — real values live in Dokploy or `.env.local`). |
| [`provisioning/`](provisioning) | One-shot, idempotent client provisioner (no npm deps, stock `node:22-alpine`). |

## Deploy (staging / production)

Rides the existing `dokploy` pipeline (see [`docs/deployment.md`](../../docs/deployment.md)):

- **staging** — redeploys automatically when `projects/zitadel/**` changes (`deploy.yml`).
- **production** — ships with the release set via `Deploy | Production` (`deploy-prod.yml`).

The image is upstream (`ghcr.io/zitadel/zitadel`), so there is **nothing to build** —
only the deploy stage applies. `IMAGE_TAG` injected by the pipeline is ignored;
the version is pinned with `ZITADEL_VERSION` in the Dokploy env.

### One-time setup (per environment)

1. **Dokploy** → create a Compose service (Git provider, branch `main`, compose
   path `projects/zitadel/compose.yml`). Fill its **Environment** from
   [`.env`](.env) and add a **Domain** → service `zitadel`, port `8080`.
2. Copy that compose's id into the GitHub **`ZITADEL_DOKPLOY_ID`** secret, in the
   matching GitHub Environment (`staging`, then `production`).

> **dev** is intentionally left out of the automated pipeline — it's the shared
> sandbox you already deployed. Want it pipeline-managed too? Add a `dev` GitHub
> Environment + secret and a deploy trigger; ask and we'll wire it.

## Creating the default clients (staging / production only)

Zitadel **generates** client IDs — you cannot pre-set them. `FirstInstance` only
seeds the org, the human admin and a **provisioner** machine user whose **PAT** is
written to a shared volume on first init. The apps themselves are created after
startup by the provisioner, driven by [`provisioning/apps.json`](provisioning/apps.json):

- an **OIDC app** (User-Agent / PKCE) per frontend — `tera-app`, `espace-app`
- an **API app** (Basic auth, for token introspection) per backend — `tera-api`,
  `espace-api`, `flora-api`

It is **idempotent** (search-before-create) and prints the values to wire back
into each consumer's environment:

| Consumer | Variables |
|----------|-----------|
| frontends | `VITE_ZITADEL_CLIENT_ID`, `VITE_ZITADEL_PROJECT_RESOURCE_ID`, `VITE_ZITADEL_ISSUER` |
| backends  | `ZITADEL_CLIENT_ID`, `ZITADEL_CLIENT_SECRET`, `ZITADEL_PROJECT_ID`, `ZITADEL_DOMAIN` |

The project id is shared, which keeps token **audiences** aligned between SPAs and APIs.

> ⚠️ **Run it only against a fresh staging/production instance — never dev.**
> Re-provisioning dev would create new apps with new client IDs and diverge from
> the IDs the committed `.env.local` files already use.

Two ways to run it:

- **On the Dokploy host** (uses the volume PAT + internal URL):
  ```bash
  docker compose --profile provision run --rm provisioner
  ```
- **From anywhere** (CI / local) against the public URL:
  ```bash
  ZITADEL_API_URL=https://staging.account.lychen.org \
  ZITADEL_PAT=<provisioner-pat> \
  TERA_APP_BASE_URL=https://app.staging.lychen.org \
  ESPACE_APP_BASE_URL=https://espace.staging.lychen.org \
  moon zitadel:provision
  ```

### Things to verify / adjust

- **`ZITADEL_VERSION`** — pin a real stable tag (current compose default is `latest`).
- **Redirect URIs** — `provision.mjs` builds the standard `vue-oidc-client` URIs
  with `authName: "zitadel"`. Confirm that slug matches what `@lychen/typescript-zitadel`
  generates and adjust `apps.json` if needed.
- **Feeding IDs back** — for now the IDs are printed and copied into each consumer's
  Dokploy env by hand. Automating that write-back via the Dokploy API is a follow-up.
- **Production restart** — because no `zitadel` image lives in our registry, the
  prod no-op detector falls through and restarts Zitadel on each release. Harmless
  (sessions are JWTs) but worth knowing.
