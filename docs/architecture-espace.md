# espace.lychen — Architecture

Complements the root `CLAUDE.md` (monorepo root) for everything specific to the `espace` project (`projects/espace/`). Claude Code will load this context in addition to the root file when working on this sub-project.

## Infrastructure

Deployed via **Dokploy**.

```
Dokploy
├── PostgreSQL
│   ├── DB: zitadel
│   ├── DB: temporal
│   ├── DB: novu
│   └── DB: espace_lychen
├── Temporal Server + UI
├── Novu
├── Zitadel
├── Minio
│   └── Bucket: espace-lychen-area-proposals
└── espace.lychen
    ├── API (Symfony + API Platform)
    ├── Front (Vue.js)
    └── Temporal Worker (same Docker image as the API, entrypoint bin/worker.php)
```

**DEV environment**: Temporal, Novu, Zitadel and Minio run on **shared instances**, not locally. Goal: avoid running these services on every developer's machine. Only the API, the front, and the worker for `espace.lychen` run locally, pointing to these shared instances.

> To be documented separately once available: shared DEV instance URLs, procedure for obtaining credentials/namespaces for Temporal, Zitadel, Novu.

## Business domain — Temporal workflows

4 workflows, intentionally separated based on their differing validation business rules.

### 1. `ValidationAreaRequestWorkflow`

- **Trigger**: `POST` of a `AreaRequest`
- **Role**: checks the seeker profile's completeness and the request's consistency; waits for a human moderation decision if needed (with a timeout)
- **Outcome**: if approved → triggers `MatchingWorkflow`

### 2. `ValidationAreaProposalWorkflow`

- **Trigger**: `POST` of a `AreaProposal`
- **Role**: checks area/property information; same moderation logic as above
- **Outcome**: if approved → signals all open `MatchingWorkflow` instances so they re-evaluate their candidates

### 3. `MatchingWorkflow`

- **Cardinality**: one per approved `AreaRequest`
- **Role**: scans active `AreaProposal` entries, scores each pair, spawns one `MatchLifecycleWorkflow` per relevant candidate
- **Note**: stays alive to process new proposals arriving after it started (via signal from `ValidationAreaProposalWorkflow`)
- **Ends**: when the request is fulfilled or expires

### 4. `MatchLifecycleWorkflow`

- **Cardinality**: one per `AreaRequest` + `AreaProposal` pair
- **Role**: notifies both parties, manages response deadlines, orchestrates mutual acceptance
- **Ends (success)**: both parties agree → creates the agreement + closes the `AreaRequest`
- **Ends (failure)**: timeout or refusal → terminates silently

## Temporal conventions — non-negotiable

- **Payloads = IDs only.** No business data ever travels between workflows/activities — only identifiers (UUIDs of Postgres entities).
- **Source of truth = PostgreSQL.** Business data lives exclusively in the database; Temporal orchestrates, it does not store.
- **Status duplicated in the database.** The status of entities (`AreaRequest`, `AreaProposal`, matches...) is duplicated in Postgres to enable API queries; Temporal activities are responsible for keeping it up to date as the workflow progresses.
- **Notifications = activities only.** All notifications originate exclusively from a Temporal activity, via Novu. Never a direct call to Novu from the API.
- **Images = direct Minio.** `AreaProposal` images are stored in Minio and served directly to the front end (signed or public URLs depending on the bucket) — the API does not proxy the files.

## Implementation implications

When implementing a feature touching this domain:

1. **Activity vs domain logic**: a Temporal PHP activity calls existing (or new) Symfony business services — it should not contain business logic itself, only orchestration + status persistence.
2. **Idempotency**: activities must be idempotent (Temporal retries) — check before writing, don't just write.
3. **Naming**: follow the naming of the 4 workflows above verbatim for any corresponding PHP class (`ValidationAreaRequestWorkflow`, etc.) and their associated activities (`*Activities`).
4. **Signals**: `ValidationAreaProposalWorkflow` → `MatchingWorkflow` communication happens via a signal, not a new workflow execution; document the signal name and its payload (the `AreaProposal` ID) in the code.
5. **Testing**: prefer the Temporal Test Framework (in-memory workflow environment) over relying on the shared instance for unit/PHPUnit tests.

## API exposure (espace-api)

API resources are DTOs, not Doctrine entities: entities (`src/Entity`) only carry persistence, and each resource lives in `src/Api/Resource/<Name>/`.

- **One read resource per entity**, declared with `stateOptions: new Options(entityClass: …)` and `#[Map(source: Entity::class)]`. Keep it the only class mapped _from_ that entity: Symfony registers a single reverse mapping per source class, so property-level `#[Map]` attributes on a second DTO are silently ignored.
- **Writes go through input DTOs** (`Dto/<Name>Post`, `Dto/<Name>Patch`) marked `#[Map(target: Entity::class)]`. API Platform maps them onto the entity, persists it and maps the result back to the resource — no custom processor. Patch DTO properties stay uninitialized so that only the fields sent by the client are applied (merge-patch).
- **Validation lives on the input DTOs**: API Platform validates the deserialized DTO, not the entity, so constraints declared on entities are not enforced by the API.
- **Relations and computed fields use transforms**: `#[Map(source: 'relation', transform: …)]`. Dotted source paths (`relation.field`) resolve to `null` under the reverse mapping. Transforms that need services (e.g. activity codes → `AreaActivity`) implement `TransformCallableInterface`.
- **Visibility is enforced in SQL** by `AreaVisibilityExtension`: reads return the public items (published proposals, active requests) plus the user's own, writes only reach the user's own; anything else answers 404.
- **Filters** are declared with `QueryParameter` and API Platform's filters (`ExactFilter`, `PartialSearchFilter`, `SortFilter`); nested properties such as `activities.code` resolve against the entity.

## Real-time updates

The API pushes data changes to open screens through the monorepo's central Mercure hub,
on the `espace` tenant — see [deployment.md](deployment.md#central-mercure-hub).

- **Published from Doctrine, not from the API write path.** `AreaProposalMercureListener`
  publishes every committed write to an `AreaProposal`, whoever makes it: the status
  changes Temporal activities persist reach the front with no extra code in the activities.
- **Private updates, IRI paths as topics** (`/api/area_proposals/{uuid}`): the hub only
  delivers what the user's token (`GET /api/mercure_subscription`) allows, and a worker,
  which has no request host, publishes to the same topics as the API.
- **Front:** `useEspaceMercure(topics, onUpdate)` in `libs/vue/espace`.
- **Not a notification channel.** Mercure mirrors data to screens that are open; notifying
  a user still happens from activities, through Novu, as above.

---

_Keep this document up to date as implementation progresses — in particular, add the Workflow ↔ actual PHP class mapping once coded, and the exact names of the Temporal queues used._
