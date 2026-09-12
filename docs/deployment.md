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
`espace-website`, `tera-api`, `espace-api`, `flora-api`, `common-rabbitmq`,
`common-mercure`.

`common-rabbitmq` and `common-mercure` are the odd ones out: they run upstream
images rather than ones we build, so they are absent from **Build & push images**
(that matrix selects on the `docker-buildx` task) and from the release
`image-promote` step (tag `version`). They are deployed like the rest — the
`IMAGE_TAG` a deploy pins is simply ignored by their composes, which pin
`RABBITMQ_VERSION` and `MERCURE_VERSION` instead. See
[Central RabbitMQ](#central-rabbitmq) and [Central Mercure hub](#central-mercure-hub)
below.

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

## Central RabbitMQ

All three APIs share **one** broker ([`projects/common/rabbitmq`](../projects/common/rabbitmq/)),
the way they already share `common/mailpit`. Each API gets its **own vhost and
own credentials**, so the identically-named `async` / `failed` / `sync` queues
the three Symfony Messenger stacks declare cannot collide, and no API can read
another's queues.

Users, vhosts and permissions cannot be expressed with `RABBITMQ_DEFAULT_USER`
and friends (those seed a single vhost, and the node skips them entirely once it
has definitions to import). They are instead rendered into a definitions file at
boot by [`entrypoint.sh`](../projects/common/rabbitmq/entrypoint.sh), from these
variables:

| Variable | Default | Purpose |
| --- | --- | --- |
| `RABBITMQ_TENANTS` | `tera espace flora` | Space-separated vhosts to provision. One per API. |
| `RABBITMQ_<TENANT>_PASSWORD` | the tenant name | Password for that tenant's user, e.g. `RABBITMQ_TERA_PASSWORD`. |
| `RABBITMQ_ADMIN_USER` / `RABBITMQ_ADMIN_PASSWORD` | `admin` / `admin` | Management-UI account, with access to every vhost. |
| `RABBITMQ_VERSION` | `4.0.4` | Image tag (`rabbitmq:<version>-management-alpine`). |

The defaults are **local-dev credentials**. Staging and production must override
every password in the Dokploy compose env. Definitions are re-imported on each
boot and are idempotent — changing a password there and redeploying updates the
user in place, leaving queues and messages untouched.

**To deploy it, first (outside this repo):**

1. Create the Compose service in Dokploy from `projects/common/rabbitmq`, and set
   the passwords above in its env.
2. Add its compose id as the `COMMON_RABBITMQ_DOKPLOY_ID` secret in the `staging`
   and `production` GitHub Environments — the deploy workflows derive the secret
   name from the project id. Without it the deploy job fails.
3. Attach the API composes to the same Docker network as the broker, and set
   `MESSENGER_TRANSPORT_DSN` on each of them (see below).

> **Until those three steps are done, deploys of `common-rabbitmq` fail and the
> APIs have no broker.** Nothing dispatches messages today (no `MessageBusInterface`
> consumer exists yet) and Messenger connects lazily, so an API still boots and
> serves traffic without a reachable broker — but the first message dispatched
> would fail.

> **Runtime config (Dokploy, outside this repo):** each API compose resolves its
> transport as `${MESSENGER_TRANSPORT_DSN:-amqp://<project>:<project>@rabbitmq:5672/<project>}`.
> That default is the dev topology (service name `rabbitmq` on `lychen-network`);
> staging and production must set `MESSENGER_TRANSPORT_DSN` explicitly to the
> deployed broker's host and the environment's real password.

> **Runtime config (Dokploy, outside this repo):** the API composes resolve their
> image as `${IMAGES_PREFIX:-}<project>:${IMAGE_TAG:-latest}`. After this change,
> the staging/prod `IMAGES_PREFIX` for the three APIs must be set to
> `ghcr.io/lychen-lab/lychen/` (the frontends already hardcode the full path). The
> same variable also drives prefixed names in dev/CI, so verify local stacks still
> resolve before rolling it out.

## Central Mercure hub

All three APIs share **one** [Mercure](https://mercure.rocks) hub
([`projects/common/mercure`](../projects/common/mercure/)), the way they already
share `common/rabbitmq` and `common/mailpit`. Mercure pushes real-time updates to
browsers over SSE; the hub is the broker sitting between the APIs (publishers)
and the apps (subscribers).

Each API gets its **own hub**, under its own path prefix, with its **own JWT
keys** and its **own Bolt transport**:

```
http://mercure/tera/.well-known/mercure
http://mercure/espace/.well-known/mercure
http://mercure/flora/.well-known/mercure
```

That is the same isolation the broker gets from per-vhost credentials. It matters
because Mercure authorises purely by JWT signature: with a single shared key, any
API could mint a token granting itself `subscribe` on another domain's topics.
With a key per tenant, a token signed by `espace` is rejected (401) by the `tera`
hub, and the separate transports mean two APIs publishing the *same* topic URI
never see each other's updates.

A Caddyfile cannot loop over a list, so the per-tenant blocks are rendered at boot
by [`entrypoint.sh`](../projects/common/mercure/entrypoint.sh) from these
variables:

| Variable | Default | Purpose |
| --- | --- | --- |
| `MERCURE_TENANTS` | `tera espace flora` | Space-separated hubs to provision. One per API, `[a-z0-9-]`. |
| `MERCURE_<TENANT>_PUBLISHER_JWT_KEY` | a dev key | HMAC key validating that tenant's *publisher* tokens, e.g. `MERCURE_TERA_PUBLISHER_JWT_KEY`. |
| `MERCURE_<TENANT>_SUBSCRIBER_JWT_KEY` | a dev key | Same, for *subscriber* tokens. |
| `MERCURE_EXTRA_DIRECTIVES` | — | Extra `mercure` directives applied to every hub. |
| `MERCURE_<TENANT>_EXTRA_DIRECTIVES` | — | Extra directives for that hub alone — this is where production `cors_origins` goes. |
| `MERCURE_JWT_ALG` | `HS256` | Signing algorithm for every key. |
| `MERCURE_VERSION` | `v0.24.2` | Image tag (`dunglas/mercure:<version>`). |

The rendered Caddyfile holds **no secrets** — it references keys as
`{env.MERCURE_TERA_PUBLISHER_JWT_KEY}`, which Caddy resolves at runtime. When a
key is left empty the hub falls back to an in-repo development key and says so
loudly at boot:

```
lychen: WARNING using built-in development JWT keys for: tera/publisher …
```

**Treat that warning as a deploy failure anywhere but local dev** — those keys are
public, and anyone holding one can publish to, and subscribe to, everything on
that hub.

Adding a tenant takes two edits: append it to `MERCURE_TENANTS`, and declare its
two key variables in
[`compose.yml`](../projects/common/mercure/compose.yml) — Compose only forwards
the variables it is told about.

### Dev

`moon <api>:dev` starts the hub alongside mailpit and the broker. It is permissive
there and only there (`anonymous`, `demo`, `subscriptions`, `cors_origins *`), set
from [`compose.override.yml`](../projects/common/mercure/compose.override.yml).
The Mercure debugger is at
<http://localhost:8800/tera/.well-known/mercure/ui/> (`MERCURE_PORT` to move it).

### To deploy it, first (outside this repo)

1. Create the Compose service in Dokploy from `projects/common/mercure`, and set
   a **fresh random key** for every `MERCURE_<TENANT>_{PUBLISHER,SUBSCRIBER}_JWT_KEY`
   in its env, plus a `MERCURE_<TENANT>_EXTRA_DIRECTIVES` naming that app's origin,
   e.g. `cors_origins https://app.tera.lychen.org`.
2. Add its compose id as the `COMMON_MERCURE_DOKPLOY_ID` secret in the `staging`
   and `production` GitHub Environments — the deploy workflows derive the secret
   name from the project id. Without it the deploy job fails.
3. Attach the API composes to the same Docker network as the hub, and route a
   domain to the hub container's port `80` so browsers can reach it.

> **Until those steps are done, deploys of `common-mercure` fail.** Nothing
> publishes to the hub today — no API has `symfony/mercure-bundle` installed — so
> an unreachable hub breaks nothing else.

### Opting an API in

The hub is deployed and reachable but unused until an API installs the bundle:

```bash
composer require symfony/mercure-bundle
```

then points it at that API's own hub — the internal URL for publishing, the
public one for the `Link` header browsers follow:

```yaml
# config/packages/mercure.yaml
mercure:
    hubs:
        default:
            url: '%env(MERCURE_URL)%'
            public_url: '%env(MERCURE_PUBLIC_URL)%'
            jwt:
                secret: '%env(MERCURE_JWT_SECRET)%'
                publish: ['*']
```

```yaml
# compose.yml, for the tera API
MERCURE_URL: ${MERCURE_URL:-http://mercure/tera/.well-known/mercure}
MERCURE_PUBLIC_URL: ${MERCURE_PUBLIC_URL:-http://localhost:8800/tera/.well-known/mercure}
MERCURE_JWT_SECRET: ${MERCURE_JWT_SECRET:-!ChangeThisDevKey-tera-publisher!}
```

`MERCURE_JWT_SECRET` must equal that tenant's **publisher** key. Tokens the API
mints for browsers must instead be signed with the **subscriber** key, which is
what keeps a subscriber from publishing.

## Prefer a forward fix

A rollback is a stopgap. Once the incident is contained, revert the bad change on
`main` (`git revert <sha>`); the normal pipeline rebuilds and redeploys staging,
and the fix ships to production in the next release. That keeps `main` and what's
deployed in sync.
