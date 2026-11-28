# CLAUDE.md · myko

This file complements the monorepo root `CLAUDE.md`. It applies to all work on Myko: `projects/myko/`
(`api`, `app`) and the Myko libraries listed below.

Myko is Lychen's mushroom cultivation product. Version 0 tracks mushroom logs (bûches): their
inoculation, their journal and their weighed harvests. Read the functional spec,
[`docs/myko/spec.fr.md`](../../docs/myko/spec.fr.md) (French), before implementing anything: it is
the source of truth for what the app does. This file is the source of truth for how it is built.

## Projects and libraries

| What | Path | Moon id | Package |
| --- | --- | --- | --- |
| API (Symfony + API Platform) | `projects/myko/api` | `myko-api` | |
| App (Vue 3 PWA) | `projects/myko/app` | `myko-app` | `@lychen/myko-app` |
| API SDK (generated) | `libs/typescript/myko-api-sdk` | `typescript-myko-api-sdk` | `@lychen/typescript-myko-api-sdk` |
| API front lib (endpoint composables) | `libs/vue/myko-api` | `vue-myko-api` | `@lychen/vue-myko-api` |
| Front concepts | `libs/typescript/front-model-business/mushroom/` | `typescript-front-model-business` | |
| Domain components | `libs/vue/myko` (exists) | `vue-myko` | `@lychen/vue-myko` |
| Constants | `libs/typescript/myko` (exists) | `typescript-myko` | `@lychen/typescript-myko` |
| Brand assets | `libs/assets/myko` (exists) | `assets-myko` | |

Local ports: API HTTP `8082`, API PostgreSQL `23432`, app dev server `5820` (Flora, Tera and Espace
already use 8080, 8081, 8089, 21432, 22432, 19432, 5800 and 5810).

## What to copy

- **API**: `projects/flora/api` is the reference. Plain Doctrine entities extending
  `AbstractIdOrmAndUuidApiIdentified` with the created/updated traits; the API representation in
  `src/Api/Resource/<Name>/` with its input DTOs (`Dto/<Name>Post`, `Dto/<Name>Patch`) and the
  object mapper; uuids in URLs, the int id never leaves the database; Foundry factories and stories;
  functional tests on `Lychen\Test\AbstractApiTestCase`; workflows in `src/Workflow/<Name>/` and
  `config/packages/workflow/`. Scaffold with `moon generate php-api` (name `myko`), then align on
  Flora where the template lags behind.
- **Current user**: a `Person` entity extending `AbstractZitadelUser`, as
  `projects/espace/api/src/Entity/Person.php`.
- **Ownership**: as Tera's `Land`. A Doctrine listener sets the owner on creation
  (`LandLinkOwnerListener`), query extensions filter collections and items by owner
  (`LandRequestExtension`), voters guard writes.
- **App**: `projects/espace/app` (`views/<name>/View.vue` with its `i18n/` folder, `router/`,
  `layouts/`). Endpoint composables: `libs/vue/espace-api`. Concepts:
  `libs/typescript/front-model-business` (see the root CLAUDE.md, "API front libs").

## Glossary

Code is in English, the UI in French first. Never name a mushroom log `Log` alone: it clashes with
logging everywhere.

| French (UI) | Code | API resource |
| --- | --- | --- |
| Zone | `Zone` | `/api/zones` |
| Bûche | `MushroomLog` | `/api/mushroom_logs` |
| Essence de bois | `WoodSpecies` | `/api/wood_species` (read-only reference) |
| Espèce de champignon | `FungusSpecies` | `/api/fungus_species` (read-only reference) |
| Souche | `Strain` | `/api/strains` |
| Inoculation | `Inoculation` | `/api/inoculations` |
| Entrée de journal | `JournalEntry` | `/api/journal_entries` |
| Récolte | `Harvest` | `/api/harvests` |
| Statistiques de récolte | `HarvestStat` | `/api/harvest_stats` (read-only, computed) |

## Data model v0

- `Person`: the Zitadel user.
- `Zone`: `name` (required, max 255), `description` (nullable text), `owner` (Person, set by the
  listener, never part of an input). Deleting a zone that holds logs is rejected (409 or 422).
- `WoodSpecies`, `FungusSpecies`: reference data, seeded by a story, read-only through the API.
  `code` (unique slug, e.g. `oak`, `shiitake`) and `scientificName` (e.g. `Quercus`,
  `Lentinula edodes`). No translated names in the database: the app translates `code` with i18n.
  The seeded codes and scientific names are listed once, in `libs/typescript/myko/constants/`
  (`FungusSpecies.ts`, `WoodSpecies.ts`): the API's story seeds exactly those, and
  `useSpeciesLabels` (`libs/vue/myko`) translates them to fr-FR and en-US. A species the story does
  not know has no translation: the app falls back to its scientific name, then to its code.
  Adding a species means adding it to the constants, to the story and to both locale files.
- `Strain`: `fungusSpecies` (required), `name` (required, e.g. `M3782`), `supplier` (nullable),
  `notes` (nullable), `owner`.
- `MushroomLog`: `zone` (required, owned by the same person), `label` (required, unique per owner,
  e.g. `B-012`), `woodSpecies` (nullable), `cutAt` (date, nullable), `diameterCm` and `lengthCm`
  (nullable positive ints), `notes` (nullable), `state` (workflow marking). A log belongs to the
  owner of its zone: no separate owner column, filter through the zone.
- `Inoculation`: `mushroomLog` (unique: one per log in v0), `strain` (required, same owner),
  `inoculatedAt` (date, required, not in the future), `method` (`dowels` | `sawdust` | `other`),
  `quantity` (nullable positive int), `notes` (nullable).
- `JournalEntry`: `mushroomLog`, `occurredAt` (datetime, defaults to now), `kind` (`note` |
  `observation` | `soaking` | `watering` | `moving` | `treatment` | `other` | `state_change`),
  `text` (required for `note` and `observation`, optional otherwise). `state_change` entries are
  written by the API only, on every workflow transition, and are read-only.
- `Harvest`: `mushroomLog`, `harvestedAt` (date, required, not in the future), `weightGrams` (int,
  greater than 0), `notes` (nullable).

## Log lifecycle

Symfony Workflow `mushroom_log_lifecycle`, a state machine on `MushroomLog::$state`:

| Transition | From | To | Triggered by |
| --- | --- | --- | --- |
| `inoculate` | `prepared` | `incubating` | the API, when an Inoculation is created |
| `cancel_inoculation` | `incubating` | `prepared` | the API, when the Inoculation is deleted |
| `start_fruiting` | `incubating` | `fruiting` | an exposed endpoint, or the API on the first Harvest |
| `retire` | `prepared`, `incubating`, `fruiting` | `retired` | an exposed endpoint |

A Harvest on a `prepared` or `retired` log is rejected (422). Deleting the Inoculation of a
`fruiting` or `retired` log is rejected. Every applied transition records a `state_change`
JournalEntry (`from → to` in its text).

## Security

- Every endpoint requires an authenticated user, except `/api/docs`.
- A person only sees and changes their own data. Another person's item answers 404, never 403, so
  that its existence does not leak. Relations are checked too: a log cannot be put in someone
  else's zone, an inoculation cannot use someone else's strain.
- Reference data is readable by any authenticated user and never writable through the API.
- Every resource ships with tests in `tests/Security` that prove the isolation between two persons.

## API conventions

- Filters: `mushroom_logs?zone=&state=&woodSpecies=&inoculation.strain.fungusSpecies=&label=`
  (label is partial), `journal_entries?mushroomLog=&kind=`,
  `harvests?mushroomLog=&harvestedAt[after]=&harvestedAt[before]=`.
- Default order: logs by `label` ascending, journal entries by `occurredAt` descending, harvests by
  `harvestedAt` descending.
- `GET /api/harvest_stats?groupBy=mushroomLog|strain|fungusSpecies|woodSpecies|zone|month&from=&to=`
  gives `[{ key, label, totalGrams, harvestCount }]`, from an API Platform provider, scoped to the
  current person.
- A log's timeline is not an endpoint: the app merges its inoculation, journal entries and
  harvests (`useMushroomLogTimeline` in `vue-myko-api`), with the pure
  `buildMushroomLogTimeline` (`front-model-business/mushroom/MushroomLogTimeline.ts`).

## App conventions

- Mobile first: design at 390 px wide, usable with one hand. Bottom navigation: Accueil, Bûches,
  Scanner, Stats, Plus.
- Routes: `/`, `/logs`, `/logs/new`, `/logs/new-batch`, `/logs/:uuid`, `/logs/:uuid/edit`,
  `/l/:uuid` (QR entry point, redirects to `/logs/:uuid`), `/zones`, `/zones/new`,
  `/zones/:uuid/edit`, `/strains`, `/strains/new`, `/strains/:uuid/edit`, `/labels`, `/scan`,
  `/stats`, `/sign-in`.
- Every route needs a signed-in user, except `/sign-in`: the layout route carries
  `auth.protectedRouteMeta` (`src/auth/Auth.ts`, routes built by `createRoutes(auth)`). With Zitadel
  configured the identity provider's guard (`meta.authName`, as in Espace) sends an anonymous visitor
  to Zitadel; with the fake login, or Zitadel not configured, `installSignInScreenGuard` sends them to
  `/sign-in?redirect=<where they were going>`. A new route goes under the layout route, so it is
  protected without doing anything.
- Every route has its name in `src/router/routeNames.ts` (`ROUTE_NAME`). A screen that is not
  built yet is listed in `src/views/provisional/index.ts`, with its path: the ticket that builds
  it moves its route into its own `views/<name>/` (`ROUTE_<NAME>` in its `index.ts`, as the home
  view does) and out of that list.
- **The QR code content is `<app origin>/l/<log uuid>` and must never change**: labels are printed,
  and will later be engraved on steel. `mushroomLogQrContent` and `mushroomLogUuidFromQrContent`
  (`libs/typescript/myko/qr/`) are the only code that writes and reads it, and their test pins it.
  The code itself is drawn from that content by `mushroomLogQrCode` (`uqr`, error correction M, an SVG
  path, no markup) in `MushroomLogQrCode.vue` (`libs/vue/myko`); the origin it takes is the one the
  labels must open, not whatever the app runs on: the screens get it from `getMushroomLogQrOrigin()`
  (`projects/myko/app/src/config/`), which reads `VITE_MYKO_APP_URL` and falls back to the origin
  the app runs on (`resolveMushroomLogQrOrigin`, `libs/typescript/myko/qr/`).
- A batch's labels (`B-001` to `B-040`) come from `mushroomLogBatchLabels`
  (`front-model-business/mushroom/MushroomLogBatchLabels.ts`), for the form's preview and for the
  creation alike. Its limits (200 logs, 6 digits) are constants next to it.
- A sheet of labels (A4, two sizes) is laid out by `labelSheetLayout` and `paginateLabels`
  (`libs/typescript/myko/labels/`): how many labels fit, the millimetre position of each cell, of
  its QR code and of its text. The print stylesheet of `/labels` takes those values as they are and
  computes nothing: `labelSheetStyle.ts` turns them into the CSS lengths, and `MushroomLogLabelSheets`
  (`libs/vue/myko`) draws the sheets with them (A4 pages, preview with cut lines on screen, nothing
  but the labels in print). A label's text is a single line sized to fit (`labelTextFontSizeMm`), and
  wraps rather than shrinks below legible.
- A bulk entry (the same journal entry or inoculation on every selected log) runs through `runBulk`
  (`libs/typescript/myko/bulk/`): a few requests at a time, a failed item never stops the others, the
  results come back in the selection's order and `onProgress` feeds the progress bar and the recap.
  For an inoculation, `splitLogsForInoculation`
  (`front-model-business/mushroom/MushroomLogBulkInoculation.ts`) first leaves out the logs that are
  not `prepared`, to be reported as skipped.
- The list of logs keeps its filters in the URL, so that a filtered list can be shared, reloaded and
  left with the back button: `parseMushroomLogListQuery`, `toMushroomLogListQuery` and
  `withMushroomLogListFilters` (`front-model-business/mushroom/MushroomLogListFilters.ts`) are the
  only code that reads and writes the parameters (`label`, `zone`, `state`, `fungusSpecies`,
  `woodSpecies`). A blank parameter or a `state` the front does not know is ignored, never an error.
  Its multiple selection is `useItemSelection` (`libs/vue/myko`), over the ids the list shows now: it
  only reports the selected logs the list shows, in the list's order, so that a bulk entry never
  reaches a log a filter hides, and it forgets nothing until `clear()`.
- What the scanner reads is turned into an action by `resolveScannedContent`
  (`libs/typescript/myko/scan/`): a Myko label (any origin) opens its log, anything else is `NotMyko`
  and shown as text, never as a link. A camera that cannot be used is explained from
  `classifyCameraError` (what `getUserMedia` rejected with) and `cameraProblemBeforeAsking` (an
  insecure page or a browser without the API), as a `CameraProblem` the screen translates, with the
  manual search by label as the way out. The camera loop is `useQrScanner` (`libs/vue/myko`): it asks
  for the rear camera, reads a frame every 250 ms through a `QrDetector` made by
  `createQrDetector` (`libs/typescript/myko/scan/`): the browser's own `BarcodeDetector` where there
  is one (`createBarcodeQrDetector`, Chrome on Android), otherwise jsQR (`createJsQrDetector`, for
  Safari on iOS and Firefox: loaded on first use as a chunk of its own, frames drawn at 640 px at
  most, a light code on a dark ground read too); another detector goes in its `createDetector`
  option. It reports each code once per 3 s. It makes the detector before it asks
  for the camera, and `stop()` (or the end of the scope) releases the camera.
- A form checks what it gives with `validateZoneInput`, `validateMushroomLogInput`,
  `validateHarvestInput`, `validateInoculationInput` and `validateJournalEntryInput`
  (`front-model-business/mushroom/MushroomInputValidation.ts`): the API's rules, as codes the form
  translates. The API still has the last word.
- Every screen titles the page: `useDocumentTitle(() => t('page_title'))` (`libs/vue/myko`) gives
  `<screen> · Myko`, reactive to the language and to data (`() => log.value?.label`). A single-page
  app has to do it on every route (WCAG 2.4.2), and the title is what a screen reader announces.
- Every string in `fr-FR` (default) and `en-US`, in the view's `i18n/` folder, as in Espace. A test
  checks that both files of every `i18n/` folder of `libs/vue/myko` and of the app have the same
  keys and `{placeholders}` and no empty message (`compareLocales`, `libs/typescript/myko/i18n/`).
- Reuse `components-core`, `components-business` and `components-app` before writing a component.
  Myko-specific components go in `libs/vue/myko`.
- Quick entries (journal, harvest, inoculation, transitions) open in a bottom sheet: three taps at
  most for a common entry.

## Scope rules for agents

Myko is built from the GitHub issues labelled `myko` (milestone "Myko v0", one issue per ticket
M01, M02…), mostly by a night agent that merges its own PRs. To keep that safe:

- Change only `projects/myko/**`, the Myko libraries above, `libs/typescript/front-model-business/mushroom/**`,
  `docs/myko/**` and Myko's own workflow files (`.github/workflows/*myko*`).
- Pure additions to shared registration files are fine: a reference in the root `tsconfig.json`,
  `yarn.lock` changes coming only from new Myko workspaces or new dependencies, a Myko line in
  `projects/common/rabbitmq/subscriptions.conf` or in the root `CLAUDE.md`.
- Any other change to existing shared code (components-core, api-client, PHP libraries, other
  products, CI) is left as an open PR for Nathan's review.
- Myko is not deployed: never add the `dokploy` tag or Dokploy ids, a dedicated ticket does it.
- PR titles are Conventional Commits, scoped by project: `feat(myko-api): …`,
  `feat(myko-app): …`, `feat(vue-myko-api): …`, `docs(myko): …`. A PR body says `Closes #<issue>`,
  so that merging it closes its issue.

## Never blocked

Waiting on Nathan never stops the work. Whatever he has to provide (Zitadel apps and client ids,
Dokploy composes and domains, secrets, a product decision), build everything around it, so that
his answer only fills in a value:

- **Configuration, not constants**: every external value (Zitadel issuer, client id and project
  resource id, API URL, domains) comes from environment variables, as in Espace (`.env` with
  `${…}` placeholders, read at runtime). List each one, with what it is for, in
  `projects/myko/README.md`. A missing value never breaks the build or the tests.
- **Authentication without a Zitadel**: the API's functional and security tests log in with
  `actingAs()` (`Lychen\Test`), they never need a Zitadel. For the app's local runs, screenshots
  and e2e, a fake login is allowed when an explicit variable turns it on
  (`VITE_MYKO_FAKE_AUTH=true`) and the code that reads it sits behind `import.meta.env.DEV` (or the
  test mode), so that a production build can never contain it. Never weaken the API's security to
  get around a missing Zitadel.
- **Deployment**: prepare everything (compose files as Tera's, the list of environment variables,
  the Zitadel app's redirect URIs, a checklist) without the `dokploy` tag or Dokploy ids. Only
  plugging those in waits for Nathan.
- **Product questions**: pick the most reasonable option, keep it easy to change, and ship it.
  The PR says so in a `## Questions for Nathan` section (the choice made, the alternatives).
  Only a choice that is hard to undo, or touches security or secrets, waits for his answer.
- **Shared code**: prefer a Myko-local solution, in Myko's libraries, to a change in shared code.
  If a shared change is truly needed, open it as its own PR for Nathan's review and keep Myko
  moving on the local solution meanwhile.
- **Nothing ready?** There is always something within Myko's scope: missing tests, i18n gaps,
  accessibility, the demo data, docs, small UX fixes found while reading the app, groundwork for
  the next issues that only waits on a dependency. One coherent PR at a time, explained like any
  other.

## Working from a cloud session

Since 27/11/2026, Nathan allows every domain for Claude's cloud sessions, so a new session should
reach ghcr.io, Docker Hub, Packagist, repo.yarnpkg.com, nodejs.org and moonrepo.dev. Check it first:
`curl -s -o /dev/null -w '%{http_code}' https://ghcr.io/v2/` answers `401` when ghcr.io is
reachable, `000` when it is blocked.

**Full network**: work as on a laptop. Install proto
(`curl -fsSL https://moonrepo.dev/install/proto.sh | bash -s -- --yes`, then put `~/.proto/bin` and
`~/.proto/shims` on `PATH`), run `proto install`, then `yarn install`, and use the moon tasks:
`moon myko-api:lint`, `moon myko-api:format`, `moon myko-api:test`, `moon myko-app:lint`,
`moon myko-app:test`… The session has a Docker daemon: the APIs' dev containers and the PHP tools'
images run there. Push only what passes locally. GitHub's GraphQL API stays closed (last point
below).

**Restricted network** (the check answers `000`): only GitHub and the npm registry. In practice:

- Yarn: `export COREPACK_NPM_REGISTRY=https://registry.npmjs.org`, then `corepack enable` and
  `yarn install` (about 30 s). Node is 22 there instead of 24: fine for installs, lint and tests.
- Moon: `npx -p @moonrepo/cli@2.5.4 moon …` installs, but its toolchain plugins come from ghcr.io,
  so tasks usually fail. Run the tools directly instead (ESLint, Prettier, vue-tsc, Vitest, as the
  task commands in `.moon/tasks/*.yml` call them), from the project's folder.
- PHP: no Packagist and no Docker images, so nothing PHP runs locally. Write the code with care, then
  let CI judge: the QG workflow takes 3 to 7 minutes. Read a failed job's log through the REST API
  (`gh api repos/lychen-lab/lychen/actions/runs/<run>/jobs`, then
  `gh api repos/lychen-lab/lychen/actions/jobs/<job>/logs`).
- GitHub's GraphQL API is closed to these sessions: `gh issue …` and `gh pr create` fail. Use
  `gh api repos/lychen-lab/lychen/...` (REST) for issues, comments, labels, PRs and merges.
