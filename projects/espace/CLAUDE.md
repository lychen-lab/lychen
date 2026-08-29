# CLAUDE.md — espace.lychen

This file complements the monorepo root `CLAUDE.md`. It applies to all work in `projects/espace/` (`api`, `app`, `worker`).

## Required reading before implementation

Before implementing anything in this project, read in full:

- [`ARCHITECTURE.md`](../../docs/ARCHITECTURE.md) — infrastructure, Temporal workflows, conventions

This document describes:

- the shared infrastructure (Temporal, Novu, Zitadel, Minio) and the DEV environment
- the 4 business workflows (`ValidationAreaRequestWorkflow`, `ValidationAreaProposalWorkflow`, `MatchingWorkflow`, `MatchLifecycleWorkflow`)
- the non-negotiable Temporal conventions (IDs only between workflows/activities, status duplicated in the database, notifications via activities only, images served directly from Minio)

**Do not deviate from these conventions without explicit validation** — they underpin the consistency of the entire `espace` domain.

## Project structure

```
projects/espace/
├── api/          # Symfony + API Platform
├── app/          # Vue 3 SPA
├── worker/       # Temporal worker entrypoint (bin/worker.php) — same Docker image as api/
└── CLAUDE.md     # this file
```
