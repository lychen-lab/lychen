# Zitadel

Self-hosted [Zitadel](https://zitadel.com/) identity provider, deployed through
the **same Dokploy pipeline** as every other project — no more hand-managed
compose in the Dokploy UI.

## Topology

| Environment | Instance | Notes |
|---|---|---|
| **dev** | one **shared, already-deployed** instance | Local dev machines **and** CI point at it — one source of truth, no per-developer config drift. Kept as-is. |
| **staging** | its own instance | Deployed by the pipeline from [`compose.yml`](compose.yml). |
| **production** | its own instance | Deployed by the pipeline from [`compose.yml`](compose.yml). |

> **Local development does not run Zitadel.** Apps/APIs point at the shared **dev**
> instance (`https://dev.account.lychen.fr`) via their `.env.local`.

## Files

| File | Role |
|------|------|
| [`compose.yml`](compose.yml) | Deployable stack: `zitadel` + bundled `db`. Dokploy runs this file. |
| [`.env`](.env) | Reference list of every variable (empty — real values live in Dokploy). |

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
> sandbox you already deployed.

## Notes

- **`ZITADEL_VERSION`** — pin a real stable tag (the compose default is `latest`).
- **Clients** (the front/back OIDC + API apps) are created **manually in the
  Zitadel console** for now — automated provisioning was intentionally left out
  of this PR.
- **Production restart** — because no `zitadel` image lives in our registry, the
  prod no-op detector falls through and restarts Zitadel on each release. Harmless
  (sessions are JWTs) but worth knowing.
