# Zitadel

Self-hosted [Zitadel](https://zitadel.com/) identity provider (v4), deployed
through the **same Dokploy pipeline** as every other project — no more
hand-managed compose in the Dokploy UI.

## v4 = two containers

Since Zitadel **v4**, the login UI is a **separate container** ([Login V2](https://zitadel.com/docs/self-hosting/manage/login-client)),
required by default. The stack therefore runs:

- **`zitadel`** (core, port `8080`) — API + console. On first init it creates a
  `login-client` machine user (`IAM_LOGIN_CLIENT`) and writes its **PAT** to the
  shared `zitadel_bootstrap` volume.
- **`login`** (`ghcr.io/zitadel/zitadel-login`, port `3000`) — the login UI,
  served under **`/ui/v2/login`**. Reads that PAT to talk to the core.
- **`db`** — bundled Postgres.

Both are served under **one external domain**; the reverse proxy routes
`/ui/v2/login` to `login:3000` and everything else to `zitadel:8080`.

## Topology

| Environment | Instance | Notes |
|---|---|---|
| **dev** | one **shared, already-deployed** instance | Local dev machines **and** CI point at it. Kept as-is. |
| **staging** | its own instance | Deployed by the pipeline from [`compose.yml`](compose.yml). |
| **production** | its own instance | Deployed by the pipeline from [`compose.yml`](compose.yml). |

> **Local development does not run Zitadel.** Apps/APIs point at the shared **dev**
> instance via their `.env.local`.

## Files

| File | Role |
|------|------|
| [`compose.yml`](compose.yml) | Deployable stack: `zitadel` core + `login` (Login V2) + bundled `db`. |
| [`.env`](.env) | Reference list of every variable (empty — real values live in Dokploy). |

## Deploy (staging / production)

Rides the existing `dokploy` pipeline (see [`docs/deployment.md`](../../docs/deployment.md)):
staging redeploys when `projects/zitadel/**` changes, production ships with the
release set. Images are upstream, so nothing is built — only the deploy stage
applies. Pin the version with `ZITADEL_VERSION` (used by **both** the core and
login images, keeping them in sync).

### One-time setup (per environment)

1. **Dokploy** → create a Compose service (Git provider, branch `main`, compose
   path `projects/zitadel/compose.yml`). Fill its **Environment** from [`.env`](.env).
2. Add **two Domains** on the **same host** (this is the v4-specific bit):

   | Host | Path | Service | Port | Priority |
   |---|---|---|---|---|
   | `<your-domain>` | `/ui/v2/login` | `login` | `3000` | **higher** |
   | `<your-domain>` | `/` | `zitadel` | `8080` | lower |

   The `/ui/v2/login` route must win over `/` (longer path-prefix → higher
   priority in Traefik; set it explicitly if Dokploy exposes the field).
3. Copy the compose id into the GitHub **`ZITADEL_DOKPLOY_ID`** secret, in the
   matching GitHub Environment (`staging`, then `production`).

> **dev** is intentionally left out of the automated pipeline — it's the shared
> sandbox you already deployed.

## Notes

- **`ZITADEL_VERSION`** — pin a real v4 tag (e.g. `v4.15.3`); the default is `latest`.
- **First boot** — the `login` container may restart a few times until the core
  has written the `login-client.pat` to the shared volume. That's expected; it
  settles once the core finishes its init.
- **Fresh database** — the Postgres volume is named `zitadel_pgdata` (renamed from
  `zitadel_data`) so a redeploy starts on a clean data dir. Postgres only applies
  `POSTGRES_USER`/`PASSWORD` on first init, so if you ever change DB credentials
  you must reset this volume (or `ALTER USER` inside Postgres). Delete any old
  `*_zitadel_data` volume once the new stack is up.
- **Clients** (front/back OIDC + API apps) are created **manually in the Zitadel
  console** for now — automated provisioning was intentionally left out.
