# Deployments & rollback

Applications deploy via **Dokploy**. On every push to `main`, the affected apps
are built into Docker images, pushed to GHCR, and redeployed.

## How image tags work

- **Build and push images** builds each affected app and pushes it to GHCR
  tagged with both the **commit SHA** and `latest`:
  - Frontends (`tag-docker`): `ghcr.io/lychen-lab/lychen/<project>:<sha>`
  - APIs (`tag-symfony`): `ghcr.io/lychen-lab/<project>:<sha>`
- **Deploy** then pins each Dokploy compose to that exact SHA by setting the
  `IMAGE_TAG` variable on the compose (see
  [`.moon/scripts/dokploy-compose-deploy.sh`](../.moon/scripts/dokploy-compose-deploy.sh)),
  so `compose.yml`'s `image: …:${IMAGE_TAG:-latest}` resolves to an **immutable**
  image.

A deploy is therefore always a specific, reproducible build — never a moving
`:latest`.

Projects in the automated pipeline (tagged `dokploy`): `website`, `espace-app`,
`espace-website`, `tera-api`, `espace-api`, `flora-api`.

## Rollback

To roll back a project, redeploy it pinned to an earlier commit SHA. That image
is already in GHCR, so there is **no rebuild** — it's an instant re-pull.

### 1. Pick the SHA to roll back to

The image tag **is** the commit SHA. Choose a known-good commit on `main`:

```bash
git log --oneline --first-parent origin/main
```

The image lives under the project's GHCR package (`ghcr.io/lychen-lab/<project>`
for APIs, `…/lychen/<project>` for frontends), tag = the full 40-char SHA.

### 2a. Via the Dokploy UI (recommended)

1. Open the project's **Compose** service in Dokploy.
2. Under **Environment**, set `IMAGE_TAG=<previous-full-sha>` (leave
   `IMAGES_PREFIX` and the other variables untouched).
3. Click **Deploy**.

### 2b. Via the Dokploy API

Run the same script the pipeline uses, with a chosen SHA:

```bash
export DOKPLOY_API_URL=...        # e.g. https://dokploy.example.com/api
export DOKPLOY_API_TOKEN=...
export DOKPLOY_COMPOSE_ID=...     # the project's compose id
export IMAGE_TAG=<previous-full-sha>

bash .moon/scripts/dokploy-compose-deploy.sh
```

The script reads the compose's current env, replaces **only** `IMAGE_TAG`
(preserving `IMAGES_PREFIX` and everything else), writes it back, and redeploys.

## Prefer a forward fix

A rollback is a stopgap. Once the incident is contained, revert the bad change on
`main` (`git revert <sha>`); the normal pipeline rebuilds and redeploys a fresh
SHA. That keeps `main` and what's deployed in sync.
