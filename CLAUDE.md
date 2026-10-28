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
| `format` / `format-fix` | Prettier | php-cs-fixer | — |
| `audit` | — | Known vulnerabilities in `composer.lock` | — |
| `build` | `yarn build` | — | — |
| `docker-build` | Image for the runner's platform | Production image for the runner's platform | — |
| `docker-push` | Multi-platform image, pushed to ghcr | Multi-platform image, pushed to ghcr | — |
| `test` | Vitest | Whole PHPUnit suite (local only) | — |
| `test-functional` | — | PHPUnit `tests/Functional` | — |
| `test-security` | — | PHPUnit `tests/Security` | — |

The same tasks run locally and in CI. A Symfony API's tasks run in its dev container locally;
in CI they first build the test image and the test database (`ci-*` helpers, `runInCI: only`).

```bash
moon tera-app:dev        # Start dev server for a project
moon storybook:dev       # Component workshop
moon :build              # Build all projects
moon :lint               # Check all projects
moon :format-fix         # Auto-fix formatting
moon tera-api:lint       # PHPStan (and schema validation) for one API
```

Pre-commit hooks automatically run `format-fix` and `lint-fix` on affected files, except in PHP
projects (their fixers need the API's container): run `moon <api>:format-fix` by hand.

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
  /typescript/     # 16 TypeScript libraries
  /vue/            # 28 Vue component/composable libraries
  /php/            # 6 PHP/Symfony bundle libraries
  /i18n/           # Translation files (per domain, e.g. tera)
  /assets/         # Shared brand/content assets per domain (fonts, logos, icons)
  /drawio/         # Architecture diagrams (.drawio) per domain
  /vite/           # Shared Vite plugins/config (e.g. SSG scaffold)
  /caddy/          # Shared Caddy server configs (pwa, ssg)
  /docker/         # Shared Dockerfiles (e.g. frontend image)

/.moon             # Moonrepo configuration
  workspace.yml    # Workspace constraints, VCS hooks
  toolchains.yml   # Node, Yarn, TypeScript settings
  hooks/           # Pre-commit hook scripts
  tasks/           # Shared task definitions (all.yml, frontend.yml, php-library.yml, symfony-ci.yml, tag-*.yml)
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
- OpenAPI spec is used to generate the TypeScript SDK at `libs/typescript/tera/api-sdk`
- State management via Symfony Workflow; messaging via RabbitMQ — one **central**
  broker (`projects/common/rabbitmq`) shared by every API: a single durable topic
  exchange (`lychen.events`), one queue per service, routing keys shaped
  `<domain>.<aggregate>.<action>.v<n>`
- Real-time updates via Mercure (SSE) — one **central** hub
  (`projects/common/mercure`) shared by every API, each on its own path prefix
  with its own JWT keys
- FrankenPHP as PHP runtime in Docker; PostgreSQL for storage; Redis for cache
- Each API project has its own `compose.yml` (Postgres + Redis) for local
  development; RabbitMQ, Mercure and mailpit are shared via the `lychen-network`

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
