# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Lychen is an environmental and agricultural symbiosis platform connecting food producers, consumers, researchers, and local communities. It is a **monorepo managed by [Moonrepo](https://moonrepo.dev/)** with multiple frontend apps and backend APIs organized by domain.

## Toolchain Setup

Toolchain versions are enforced via [proto](https://moonrepo.dev/proto) (`.prototools`):

- Node 24.21.0, Yarn 4.18.0, Moon 2.5.4
- `.prototools` is the single source of truth: `.moon/toolchains.yml` leaves `node` and `yarn`
  unpinned so moon inherits them, and `package.json#packageManager` must match the Yarn version.

```bash
proto install    # Install all required toolchain versions
```

`yarn.lock` must be committed in sync with the `package.json` files: CI installs with Yarn's
default immutable mode and fails on any lockfile drift. After changing a dependency, run
`yarn install` (moon also runs `yarn dedupe`) and commit the resulting `yarn.lock`.

## Key Commands

All tasks run through Moon. Use `moon <project-id>:<task>` or `moon :<task>` (colon prefix) for workspace-wide tasks.

### Common task interface

Every project implements the same task names, whatever its stack, so CI never needs to know
which stack a project uses. A project only defines the tasks that apply to it (a Symfony API has
no `build`, a local compose service has no `lint`).

The `compose-local` tag marks projects that also run locally as a compose stack (`up`, `down`,
`dev`): the APIs and the common services. The `dokploy` tag is the deployment one. A frontend's
`compose.yml` only serves Dokploy, so frontends never get `compose-local`.

| Task | Vue / TypeScript | Symfony API | Local compose service |
| --- | --- | --- | --- |
| `dev` | `yarn dev` | `up` + mailpit, Mercure, RabbitMQ | `up` |
| `lint` / `lint-fix` | ESLint, after `typecheck` (vue-tsc) | PHPStan, after `doctrine:schema:validate` | — |
| `format` / `format-fix` | Prettier | php-cs-fixer (root config, Docker image) | — |
| `audit` | — | Known vulnerabilities in `composer.lock` | — |
| `build` | `yarn build` | — | — |
| `docker-build` | Image for the runner's platform | Production image for the runner's platform | — |
| `docker-push` | Multi-platform image, pushed to ghcr | Multi-platform image, pushed to ghcr | — |
| `test` | Vitest | Whole PHPUnit suite (local only) | — |
| `test-functional` | — | PHPUnit `tests/Functional` | — |
| `test-security` | — | PHPUnit `tests/Security` | — |

The same tasks run locally and in CI. A Symfony API's tasks (`tag-symfony.yml`) run as they are
in its dev container locally and know nothing about CI; `symfony-ci.yml` adds the test image
and the test database under them in CI (`ci-*` helpers, `runInCI: only`).

A tasks file reaches the projects that match all of its `inheritedBy` conditions (tag, stack,
layer, language…; moon has no condition on a single task). Stack-specific additions to a shared
task go in their own file under the same task id, e.g. `tag-docker.yml` builds any image and
`frontend-docker.yml` (`tag: docker` + `stack: frontend`) adds the frontends' `build` and
shared Dockerfile. Files merge in `inheritedBy.order` (default 500 for a tag condition, +100 for
a stack, then by file name): set `order` when the merge order matters.

```bash
moon tera-app:dev        # Start dev server for a project
moon storybook:dev       # Component workshop
moon :build              # Build all projects
moon :lint               # Check all projects
moon :format-fix         # Auto-fix formatting
moon tera-api:lint       # PHPStan (and schema validation) for one API
```

Pre-commit hooks automatically run `format-fix` and `lint-fix` on affected files, except in PHP
projects (their fixer needs Docker): run `moon <project>:format-fix` by hand.

PHP has one code style for the whole monorepo, as ESLint and Prettier do: `/.php-cs-fixer.php`.
Every PHP project (APIs and `libs/php/*`) inherits `format` / `format-fix` from
`.moon/tasks/php.yml`, which runs `.moon/scripts/php-cs-fixer.sh`: php-cs-fixer from its official
Docker image (version pinned there), so it needs neither PHP on the host nor the API's container.
VSCode formats PHP on save with the same script and config (`junstyle.php-cs-fixer`). Never set
rules or paths on the command line, only in the root config.

PHPStan debt is frozen in each API's `phpstan-baseline.neon`: new code must pass
clean, fixing an error means deleting its baseline entry, and CI rejects a PR whose
baseline grows. A PR needs the `QG | Gate` check green: the QG workflow runs the static
checks (`lint`, `format`, `audit`, `build` + `docker-build`), then the tests (`test`,
`test-functional`, `test-security`), each with `moon ci`.

### Testing

**E2E (Playwright):**

```bash
moon tera-tests-e2e:run                               # Run all E2E tests
moon tera-tests-e2e:open                              # Open Playwright UI
moon tera-tests-e2e:run -- tests/some.spec.ts         # Run a single test file
```

**PHP (PHPUnit) — run from the API project directory:**

```bash
make phpunit                                          # Run all PHP tests
make phpunit c="tests/SomeTest.php"                  # Run a single test file
make phpunit c="--filter testMethodName"             # Run a single test method
make phpunit c="--group e2e"                         # Run by group
```

## Repository Structure

```
/projects          # Applications (the things that run)
  /tera            # Main agricultural platform
    /api           # Symfony REST API (PHP, ~port 3000)
    /app           # Vue 3 SPA (PWA, ~port 5800)
    /website       # Static marketing site (SSG)
    /tests-e2e     # Playwright E2E tests
  /espace          # Another domain product (api/app/website, same structure as tera)
    /CLAUDE.md     # Project-specific context: Temporal workflows, shared DEV infra (Temporal, Novu, Zitadel, Minio). Read this before working in this project — see /docs/architecture-espace.md and architecture-espace.fr.md for details.
  /flora           # Flora domain (API only)
  /robust          # Design system project
  /storybook       # Component workshop (Storybook 10)
  /website         # Main Lychen website (SSG)
  /zitadel         # Self-hosted Zitadel identity provider (Dokploy-deployed)
  /common          # Shared infra: central RabbitMQ, central Mercure hub, mailpit, lychen-network

/libs              # Reusable libraries
  /css/core        # Tailwind design tokens
  /typescript/     # 20 TypeScript libraries, incl. the API SDKs (<api>-api-sdk), their client (api-client), the front's business model (front-model-business) and api-mapping
  /vue/            # 30 Vue component/composable libraries, incl. each API's front lib (<api>-api) and api-query
  /php/            # 8 PHP/Symfony bundle libraries
  /i18n/           # Translation files (per domain, e.g. tera)
  /assets/         # Shared brand/content assets per domain (fonts, logos, icons)
  /drawio/         # Architecture diagrams (.drawio) per domain
  /vite/           # Shared Vite plugins/config (e.g. SSG scaffold)
  /caddy/          # Shared Caddy server configs (pwa, ssg)
  /docker/         # Shared Dockerfiles (e.g. frontend image)

/templates         # `moon generate` templates (api-sdk, typescript-lib, vue-lib, php-api)

/.moon             # Moonrepo configuration
  workspace.yml    # Workspace constraints, VCS hooks
  toolchains.yml   # Node, Yarn, TypeScript settings
  hooks/           # Pre-commit hook scripts
  tasks/           # Shared task definitions (all.yml, frontend.yml, frontend-docker.yml, php.yml, php-library.yml, symfony-ci.yml, symfony-docker.yml, tag-*.yml)
```

## Architecture

### Frontend (Vue 3 + Vite + Tailwind CSS)

- **Composition API** with `<script setup lang="ts">` — no Options API
- **Component layers** (import order matters):
  - `libs/vue/components-core` → Base UI (buttons, inputs, dialogs, tables)
  - `libs/vue/components-business` → Domain-agnostic business components
  - `libs/vue/components-app` → Application-level composed components
  - `libs/vue/[domain]-*` → Domain-specific components (tera, espace, etc.)
- **Headless components** via Reka UI, styled with Tailwind CSS v4
- **Server state** managed by Tanstack Query; table logic via Tanstack Table
- **Routing** via Vue Router 5; **auth** via Zitadel OIDC
- **Websites** use Vite-SSG for static generation

### Backend (Symfony 8.1 + API Platform 4.3 + PHP 8.5)

- API Platform auto-generates REST APIs from Doctrine entities
- Each API's OpenAPI spec generates its TypeScript SDK (`libs/typescript/<api>-api-sdk`, see
  API SDKs below)
- State management via Symfony Workflow; messaging via RabbitMQ — one **central**
  broker (`projects/common/rabbitmq`) shared by every API: a single durable topic
  exchange (`lychen.events`), one queue per service, routing keys shaped
  `<domain>.<aggregate>.<action>.v<n>`
- Events between APIs go through `libs/php/event-bus` (lychen/event-bus): recorded in an
  outbox in the transaction of the change, published by `lychen:event-bus:relay` under their
  type as routing key, handled once per service behind an inbox (`worker` and
  `outbox-relay` compose services). A service's queue receives its own `<domain>.internal.#`
  work and the event types listed for it in `projects/common/rabbitmq/subscriptions.conf`
- Real-time updates via Mercure (SSE) — one **central** hub
  (`projects/common/mercure`) shared by every API, each on its own path prefix
  with its own JWT keys
- FrankenPHP as PHP runtime in Docker; PostgreSQL 18 for storage (a local volume from an older
  major: `moon <api>:db-upgrade`, see `docs/deployment.md`); Redis for cache
- Each API project has its own `compose.yml` (Postgres + Redis) for local
  development; RabbitMQ, Mercure and mailpit are shared via the `lychen-network`

### API SDKs

Every API's TypeScript SDK follows one norm, so that they all expose the same client:

- `libs/typescript/<api>-api-sdk`, project `typescript-<api>-api-sdk`, package
  `@lychen/typescript-<api>-api-sdk`, tag `api-sdk` (tasks in `.moon/tasks/tag-api-sdk.yml`).
- `@hey-api/openapi-ts` generates it in `generated/<api>-api/` (never edit it): the types, one
  `<Api>Api` class holding every operation, and the bundled fetch client. Hand-written additions
  sit next to `generated/` (e.g. tera's `enums.ts`).
- Its `openapi-ts.config.ts` only calls `defineApiSdkConfig({ api, input })` from
  `libs/typescript/api-sdk-config`: the one configuration of every SDK (plugins, spec patches) and
  the only place `@hey-api/openapi-ts` is pinned. Change the generator there, never in one SDK.
- Its front lib's `use<Api>Api()` (`libs/vue/<api>-api`, see API front libs below) sets up its
  client with `createApiClient(createClient, { baseUrl, bearerToken? })` (`libs/typescript/api-client`).
  Every API's client therefore rejects on non-2xx responses and runs the same default
  interceptors (`defaultInterceptors()`): the JSON-LD / Merge Patch `Content-Type` of a write
  without one, and an `Error` naming the URL and status.
- The bearer token is opt-in and knows no identity provider: `bearerToken` takes any function
  that gives the token, or undefined to send none, and adds `Authorization: Bearer <token>`
  (`bearerToken.ts`). The apps pass `getAccessToken` from `libs/vue/zitadel`.
- An API changes its own interceptors with
  `interceptors: (defaults) => ({ request: [...defaults.request, mine] })` (complete, filter or
  replace a list) and passes any other client option in `config`. Change a default in
  `libs/typescript/api-client`, for every API, never in one `use<Api>Api()`.

```bash
moon run typescript-tera-api-sdk:generate-api                        # From the monorepo API (export-openapi first)
moon run typescript-tera-api-sdk:generate-api-from-url -- <spec URL> # From a running API, e.g. https://<host>/api/docs.jsonopenapi
moon generate api-sdk -- --api flora                                 # New SDK of the monorepo project flora-api
moon generate api-sdk -- --api <name> --url <spec URL>               # New SDK of an API outside the monorepo
```

After `moon generate api-sdk`, `moon run typescript-<api>-api-sdk:generate-api` installs the new
workspace and generates the SDK. Add a `.github/workflows/openapi-sdk-sync-<api>.yml` caller for a
monorepo API. Generation stays manual (`docs/decouple-apis-from-frontends.md`): commit the
regenerated SDK when the frontends should adopt the new contract.

### API front libs

The frontends never call an SDK, so that an API's contract stops at the endpoint composables
and the components know no API at all:

- `libs/typescript/front-model-business` (`typescript-front-model-business`): the front's
  business model, one for every API, framework-free: its concepts (`area/AreaProposal`,
  `land/Land`, `person/PersonName`…), their states and the inputs of their forms. A concept is
  the front's own and follows no API representation: it may come from several endpoints, of
  several APIs. Every field of a concept is optional, as no endpoint gives them all, and it is
  identified by `id`, whatever the API calls it (uuid, ulid). An input types its fields from its
  concept (`title: NonNullable<AreaProposal['title']>`, `name?: Land['name']`). A state is an
  object of constants, `AREA_PROPOSAL_STATE = { Draft: 'draft', … } as const`, its type
  `ObjectValues<typeof AREA_PROPOSAL_STATE>` (`typescript-utils/transformers/ObjectValues`).
- Components type against these concepts only, and say which fields they need:
  `defineProps<{ proposal: WithRequired<AreaProposal, 'id' | 'title'> }>()`
  (`front-model-business/WithRequired`).
- `libs/vue/<api>-api` (`vue-<api>-api`), one per API: the client (`client/use<Api>Api.ts`) and
  one composable per endpoint, whatever it does (reads, pages of a list, writes, workflow
  transitions, custom actions), in `endpoint/<resource>/<endpoint>/`, next to the resource's
  `queryKeys.ts`. Apps and components import a composable from its folder (`index.ts`), never
  the SDK or `use<Api>Api()`.

An endpoint's folder holds its composable and its own mapping, one file each:

```
endpoint/area-activity/get-area-activities/   endpoint/area-activity/create-area-activity/
  useGetAreaActivities.ts                       useCreateAreaActivity.ts
  mapToModel.ts                                 mapToModel.ts
  index.ts                                      mapToRequest.ts
                                                index.ts
```

- `mapToModel` takes the SDK's response type (`ApiAreaActivitiesGetCollectionResponse`) and gives
  a concept, its fields listed one by one, never a spread. Its type, next to it, says which fields
  the endpoint always gives: `export type Model = WithRequired<AreaActivity, 'id'>` (a list's
  `Member`). Where a page passes an endpoint's data to a component, TypeScript checks that
  the one gives what the other needs. `mapToRequest` takes the concept's input and gives the
  SDK's request type (`Omit<ApiAreaActivitiesPostData, 'url'>`); an endpoint whose request is
  only a path builds it in the composable. A page's filters are the SDK's own
  (`ApiAreaActivitiesGetCollectionData['query']`).
- The composable calls `useQuery` or `useMutation` itself. It owns the URL (the SDK operation) and
  the mappers, in `queryFn` / `mutationFn`; its caller may change everything else. Its options are
  typed `ApiQueryOptions<Model>` / `ApiMutationOptions<Model, Variables>` (`libs/vue/api-query`):
  every Tanstack option but `queryFn` / `mutationFn`. The composable's `queryKey` and callbacks are
  defaults the options replace; its `enabled` is combined with what it waits for (an id). A
  mutation's cache upkeep is its default `onSuccess`, which refetches what the change makes stale
  (`detail(id)`, `lists()`, other resources a transition touches); a caller that only reacts
  to a success passes its callback to `mutate(variables, { onSuccess })` and keeps the upkeep.
- Keys: `createResourceQueryKeys<Filters>(api, resource)` gives `all`, `lists()`, `list(filters)`,
  `details()` and `detail(id)`: every resource identifies its items by one string id. A read of
  an item's own goes under its key (`[...detail(id), 'workflow']`).
- Mappers build on `libs/typescript/api-mapping`: `optional`, `required`, `toDate`,
  `oneOf(AREA_PROPOSAL_STATE, data.place)` (one of the constants, undefined for any other),
  `withoutUndefined`, `toArray` (a list, each item mapped, empty when the API sends none) and
  `toCollection` (API Platform's Hydra collection as given, `member` mapped). Live data:
  `useMercure(subscribe, matchers, onUpdate)` (`libs/vue/api-query/mercure`), which an API's lib
  wraps with the request for its subscriber token, as `useEspaceMercure` does.

### Monorepo Wiring

- Workspace dependencies use `@lychen/<lib-name>` with `workspace:*` protocol
- Each project/lib has a `moon.yml` declaring `dependsOn` and `tasks`
- Global task definitions in `.moon/tasks/` are inherited by projects based on tags/layer
- Frontend and backend code are strictly separated — libs do not cross layers

## Code Conventions

- **TypeScript strict mode** everywhere; ESLint flat config with Vue + TS rules
- **No `console.log`** in production code (ESLint error)
- **YAML files** must use `.yml` extension (enforced)
- **Prettier**: 100-char line width, single quotes, trailing commas
- **Vue SFC order**: `<template>` → `<script>` → `<style>`
- **Props/emits**: `defineProps<T>()` and `defineEmits<E>()` with full TypeScript types
- **Library naming**: domain-driven (`tera`, `espace`, `flora`, `humu`, `kiro`, `kolo`, `meli`, `myko`, `novi`, `vara`, `robust`)
