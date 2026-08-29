# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

Lychen is an environmental and agricultural symbiosis platform connecting food producers, consumers, researchers, and local communities. It is a **monorepo managed by [Moonrepo](https://moonrepo.dev/)** with multiple frontend apps and backend APIs organized by domain.

## Toolchain Setup

Toolchain versions are enforced via [proto](https://moonrepo.dev/proto) (`.prototools`):

- Node 24.16.0, Yarn 4.12.0, Moon 2.3.2

```bash
proto install    # Install all required toolchain versions
```

## Key Commands

All tasks run through Moon. Use `moon <project-id>:<task>` or `moon :<task>` (colon prefix) for workspace-wide tasks.

### Development

```bash
moon <project>:dev       # Start dev server for a project (e.g., moon tera-app:dev)
moon storybook:dev       # Component workshop
```

### Building

```bash
moon <project>:build     # Build a specific project
moon :build              # Build all projects
```

### Linting & Formatting

```bash
moon :lint               # Check all projects
moon :lint-fix           # Auto-fix lint issues
moon :format             # Check formatting
moon :format-fix         # Auto-fix formatting
```

Pre-commit hooks automatically run `format-fix` and `lint-fix` on affected files.

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
  /common          # Shared local-dev infra (Docker network, Mailpit)
  /zitadel         # Self-hosted Zitadel identity provider (Dokploy-deployed)

/libs              # Reusable libraries
  /css/core        # Tailwind design tokens
  /typescript/     # 16 TypeScript libraries
  /vue/            # 28 Vue component/composable libraries
  /php/            # 5 PHP/Symfony bundle libraries
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
  tasks/           # Shared task definitions (all.yml, frontend.yml, php-library.yml, tag-*.yml)
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
- State management via Symfony Workflow; messaging via RabbitMQ
- FrankenPHP as PHP runtime in Docker; PostgreSQL for storage; Redis for cache
- Each API project has its own `compose.yml` for local development

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
