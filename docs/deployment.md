# Deployments & rollback

Applications deploy via **Dokploy**, using **build-once / promote-many**: an
image is built once per commit and the *same* image is promoted across
environments — never rebuilt.

- **Staging** deploys **continuously**: every push to `main` builds the affected
  apps and redeploys them to staging, pinned to that commit's SHA.
- **Production** deploys per **release, when you decide**: cutting a release
  re-tags the images with the version, and a manual workflow pins production to
  that version.

## How image tags work

- **Build and push images** builds each affected app and pushes it to GHCR
  tagged with both the **commit SHA** and `latest`. Frontends (`tag-docker`) and
  APIs (`tag-symfony`) now share a **single namespace**:
  `ghcr.io/lychen-lab/lychen/<project>:<sha>`. Because the path is uniform across
  layers, the release-versioning task `image-promote` is defined once on a
  dedicated `version` tag
  ([`.moon/tasks/tag-version.yml`](../.moon/tasks/tag-version.yml)) — independent
  of the `dokploy` deploy logic — instead of being duplicated per layer.
- A deploy pins each Dokploy compose to a specific tag by setting the `IMAGE_TAG`
  variable on the compose (see
  [`.moon/scripts/dokploy-compose-deploy.sh`](../.moon/scripts/dokploy-compose-deploy.sh)),
  so `compose.yml`'s `image: …:${IMAGE_TAG:-latest}` resolves to an **immutable**
  image — staging pins a SHA, production pins a release version.

A deploy is therefore always a specific, reproducible build — never a moving
`:latest`.

Projects in the automated pipeline (tagged `dokploy`): `website`, `espace-app`,
`espace-website`, `tera-api`, `espace-api`, `flora-api`.

## Staging (continuous)

Every push to `main` triggers **Deploy | Build & push images** →
[**Deploy | Staging**](../.github/workflows/deploy.yml), which deploys the
affected projects to the `staging` environment pinned to the commit SHA. Nothing
to do — staging always reflects `main`.

## Releases & production (on demand)

Production is a **two-step**, release-gated flow.

### 1. Cut the release

Merge the rolling **release-please** PR. This bumps the version, tags `vX.Y.Z`,
creates the GitHub Release, and runs the `promote-images` job in
[`release.yml`](../.github/workflows/release.yml): it re-tags **every** dokploy
project's current `:latest` image as `:vX.Y.Z` (and `:stable`), copying the
multi-arch manifest registry-side — **no rebuild, no layer transfer**.

All six projects get the version tag, even those unchanged since the last
release: their `:vX.Y.Z` simply points at the same image (same digest) as the
previous version. That keeps the version namespace coherent — `vX.Y.Z` is
deployable for the whole set.

### 2. Deploy to production, when you decide

Run the [**Deploy | Production**](../.github/workflows/deploy-prod.yml) workflow
(`workflow_dispatch`):

- Leave `version` **blank** to deploy the latest release tag, or
- set it to a specific `vX.Y.Z`.

It pins every production compose to that version and redeploys. Cut a release,
let it bake on staging, and ship it to production whenever you choose.

**No-op deploys are skipped.** A release re-tags *every* dokploy project as
`:vX.Y.Z`, so a project unchanged since the previous release gets a `:vX.Y.Z`
that points at the **same digest** as what is already running — redeploying it
would just restart the container (a brief blip) for an identical image. Before
deploying each project, the workflow compares the target tag's multi-arch index
digest against the tag the project's Dokploy compose is **currently pinned to**
(read via `compose.one` — the live, deployed state, so it stays correct even if
production skipped a release) and **skips** the deploy when they match. Every
decision is logged with the digests compared — no silent skips. The check is
fail-safe: a first deploy (no current tag), an unreachable registry, or any
unresolvable digest falls through to a normal deploy. The promote step is
untouched — all six projects still get the `:vX.Y.Z` tag — the optimisation
lives only in the deploy. See
[`.moon/scripts/dokploy-prod-image-changed.sh`](../.moon/scripts/dokploy-prod-image-changed.sh).

> **Pin production to an explicit `vX.Y.Z`, never to the moving `:stable`/`:latest`
> alias.** That's what keeps production immutable and reproducible. `:stable` is
> only a convenience pointer to the most recent release.

The `production` GitHub Environment scopes the prod Dokploy secrets
(`<PROJECT>_DOKPLOY_ID`). Add a required reviewer there if you want a second
approval on top of who can run the workflow.

## Rollback

Roll production back by **redeploying an earlier version**. The image is already
in GHCR, so there is **no rebuild**.

- **Recommended:** run the **Deploy | Production** workflow with `version` set
  to a previous `vX.Y.Z`.
- **Via the Dokploy UI:** open the project's **Compose** service →
  **Environment**, set `IMAGE_TAG=<previous-version>` (leave `IMAGES_PREFIX` and
  the rest untouched) → **Deploy**.
- **Via the Dokploy API** (the same script the pipeline uses):

  ```bash
  export DOKPLOY_API_URL=...            # e.g. https://dokploy.example.com/api
  export DOKPLOY_API_TOKEN=...
  export DOKPLOY_COMPOSE_ID=...         # the project's compose id
  export IMAGE_TAG=<previous-version>   # a release version, or a commit SHA

  bash .moon/scripts/dokploy-compose-deploy.sh
  ```

  The script reads the compose's current env, replaces **only** `IMAGE_TAG`
  (preserving `IMAGES_PREFIX` and everything else), writes it back, and redeploys.

> `IMAGE_TAG` accepts any tag that exists in GHCR — a release version (`v0.2.0`)
> or a raw commit SHA both work. Releases cut before this pipeline change have no
> `:vX.Y.Z` image; pin those by commit SHA instead.

### API images built before the namespace unification

API images now publish under `ghcr.io/lychen-lab/lychen/<project>` like the
frontends. **API** images built *before* this change still live under the old
`ghcr.io/lychen-lab/<project>` path (without the `/lychen` segment) and are **not**
re-tagged retroactively. To roll an API back to a pre-migration build, point its
Dokploy compose `IMAGES_PREFIX` at the old namespace (`ghcr.io/lychen-lab/`) for
that one deploy, or rebuild from the target commit on `main` so a new image is
published under the unified path. Keep the old GHCR packages readable until no
production deploy references a pre-migration SHA.

> **Runtime config (Dokploy, outside this repo):** the API composes resolve their
> image as `${IMAGES_PREFIX:-}<project>:${IMAGE_TAG:-latest}`. After this change,
> the staging/prod `IMAGES_PREFIX` for the three APIs must be set to
> `ghcr.io/lychen-lab/lychen/` (the frontends already hardcode the full path). The
> same variable also drives prefixed names in dev/CI, so verify local stacks still
> resolve before rolling it out.

## Prefer a forward fix

A rollback is a stopgap. Once the incident is contained, revert the bad change on
`main` (`git revert <sha>`); the normal pipeline rebuilds and redeploys staging,
and the fix ships to production in the next release. That keeps `main` and what's
deployed in sync.
