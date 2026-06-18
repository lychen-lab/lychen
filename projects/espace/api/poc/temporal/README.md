# POC — Temporal workflows for the espace API

This is a **proof of concept** that re-implements the espace `AreaProposal`
(_landProposal_) publishing lifecycle with [Temporal](https://temporal.io/)
instead of the Symfony Workflow component, so the two can be compared.

> See [`ANALYSIS.md`](./ANALYSIS.md) for the full Symfony Workflow vs Temporal
> comparison and the recommendation.

It is deliberately **isolated** from the api project: its own `composer.json`,
its own `compose.yml`, no autowiring into `src/`. Nothing here is built or
linted by the espace-api Moon tasks (phpstan/rector only scan `src/` & `tests/`).

## What it models

The exact same state machine as
`src/Workflow/AreaProposal/AreaProposalWorkflow.php`:

```
draft ──submit──▶ verification ──publish──▶ published ──archive──▶ archived
                  verification ──reject───▶ draft
                  verification ──archive──▶ archived
```

Two things go beyond the Symfony version on purpose, to show what Temporal buys
you:

1. **Activities with automatic retries** — every side effect (DB write,
   moderator/author notifications, SEO (de)indexing) is a Temporal Activity,
   retried on failure with backoff.
2. **A durable timer** — a `published` proposal is auto-archived after a TTL
   (`P180D`) without any cron or Messenger scheduler.

## Layout

| File | Role |
| --- | --- |
| `src/AreaProposalWorkflowInterface.php` | Workflow contract (signals + queries) |
| `src/AreaProposalWorkflow.php` | Durable orchestration of the lifecycle |
| `src/AreaProposalActivitiesInterface.php` | Side-effect contract |
| `src/AreaProposalActivities.php` | POC implementation (logs only) |
| `worker.php` | RoadRunner worker host |
| `bin/start_workflow.php` | Client: start an execution |
| `bin/signal_workflow.php` | Client: drive it (submit/publish/…) + read state |
| `.rr.yml` | RoadRunner config |
| `compose.yml` / `Dockerfile` | Temporal server + UI + worker |

## Running it

> Requires Docker. The PHP Temporal SDK needs the `grpc` + `protobuf`
> extensions and RoadRunner; the provided `Dockerfile` bundles all of it.

```bash
# 1. Temporal server + web UI (http://localhost:8233)
docker compose up -d temporal temporal-ui

# 2. The worker (PHP + RoadRunner)
docker compose up worker         # or, locally: composer install && composer worker

# 3. Drive a proposal through its lifecycle
php bin/start_workflow.php demo-1
php bin/signal_workflow.php demo-1 submit
php bin/signal_workflow.php demo-1 publish
php bin/signal_workflow.php demo-1 archive   # -> reaches the terminal place
```

Each command prints the current `place` and the transition history (also
visible, with full event history, in the Temporal UI).

## Wiring it to the real API

The activity bodies in `AreaProposalActivities.php` only log. To make this
production-real, inject the espace services and replace the bodies, e.g.:

```php
public function persistPlace(string $uuid, string $place): void
{
    $proposal = $this->repository->findOneByUuid($uuid);
    $proposal->setPlace($place);
    $this->em->flush();
}
```

The workflow code itself would not change.
