# Changelog

## [0.2.0](https://github.com/lychen-lab/lychen/compare/v0.1.0...v0.2.0) (2026-11-13)


### ⚠ BREAKING CHANGES

* **api:** run the API databases on PostgreSQL 18 ([#269](https://github.com/lychen-lab/lychen/issues/269))
* **api:** turn the central RabbitMQ into a topic-exchange event bus ([#221](https://github.com/lychen-lab/lychen/issues/221))
* **api:** replace the per-API RabbitMQ with one central broker ([#215](https://github.com/lychen-lab/lychen/issues/215))

### Features

* add a central Mercure hub and push espace area proposals live ([#218](https://github.com/lychen-lab/lychen/issues/218)) ([a4be8a5](https://github.com/lychen-lab/lychen/commit/a4be8a5c7b09e7fe4e1de6fc20401bcfb64c70b2))
* **api-front:** one Tanstack composable per endpoint, mapped to front models (espace first) ([#260](https://github.com/lychen-lab/lychen/issues/260)) ([e1a6b87](https://github.com/lychen-lab/lychen/commit/e1a6b87cfea6760482b058c545ee0592c093cd07))
* **api-sdk:** one norm for every API SDK and its client, generation from a URL ([#258](https://github.com/lychen-lab/lychen/issues/258)) ([16b1113](https://github.com/lychen-lab/lychen/commit/16b11131e654d9e40755713c702625bf9c8dd274))
* **api:** replace the per-API RabbitMQ with one central broker ([#215](https://github.com/lychen-lab/lychen/issues/215)) ([783d34a](https://github.com/lychen-lab/lychen/commit/783d34abf9c76de5bd0cd6704ce9c6a7edb2042a))
* **api:** run the API databases on PostgreSQL 18 ([#269](https://github.com/lychen-lab/lychen/issues/269)) ([c207d3f](https://github.com/lychen-lab/lychen/commit/c207d3f94151533117736583fc91833f1815c496))
* **api:** share the APIs' Symfony config through lychen/config ([#225](https://github.com/lychen-lab/lychen/issues/225)) ([5bb6a8b](https://github.com/lychen-lab/lychen/commit/5bb6a8bfcb25500c890b42f773beef2e47c26fa0))
* **api:** turn the central RabbitMQ into a topic-exchange event bus ([#221](https://github.com/lychen-lab/lychen/issues/221)) ([8a03143](https://github.com/lychen-lab/lychen/commit/8a031437e507a1449d06991637141c7174b35b0f))
* **espace-api:** expose each workflow resource's Mermaid diagram ([#237](https://github.com/lychen-lab/lychen/issues/237)) ([9dc57ad](https://github.com/lychen-lab/lychen/commit/9dc57ad05a83fd07c473cd78d8e78bc7232b337c))
* **espace-api:** let listings be submitted, moderated and archived ([#255](https://github.com/lychen-lab/lychen/issues/255)) ([0def555](https://github.com/lychen-lab/lychen/commit/0def555d085af205a6d2b58a079fa72c554c08cc))
* **espace-api:** link AreaProposal to its proposer ([#214](https://github.com/lychen-lab/lychen/issues/214)) ([6a29bc1](https://github.com/lychen-lab/lychen/commit/6a29bc153023daf8472e77d6bc8370d3b74e1bfd))
* **espace-website:** complete Terms of Use (CGU) page ([#191](https://github.com/lychen-lab/lychen/issues/191)) ([0237b78](https://github.com/lychen-lab/lychen/commit/0237b786d9bdfd446a6e636fc692eee9a20731fd))
* **event-bus:** events between APIs with a transactional outbox and an idempotent inbox ([#253](https://github.com/lychen-lab/lychen/issues/253)) ([3e93a4e](https://github.com/lychen-lab/lychen/commit/3e93a4ea4ec1354c5c378fe6984ff743d47737c9))
* **flora-api:** add a seed library example with input DTOs and uuid relations ([#251](https://github.com/lychen-lab/lychen/issues/251)) ([72e233f](https://github.com/lychen-lab/lychen/commit/72e233f502cb5b939071551652433525b38d3ec9))
* **flora:** flora's SDK and front libs, with seed libraries as the first resource ([#263](https://github.com/lychen-lab/lychen/issues/263)) ([3bb45a7](https://github.com/lychen-lab/lychen/commit/3bb45a76e26c022be4543657dbb235330e9a002c))
* **flora:** load-test stack, N API instances behind a load balancer ([#270](https://github.com/lychen-lab/lychen/issues/270)) ([a26315e](https://github.com/lychen-lab/lychen/commit/a26315e3df8864b3f6fd84fa0b2a7b1975c3634b))
* initialize AreaRequest structure ([#210](https://github.com/lychen-lab/lychen/issues/210)) ([a2e06c5](https://github.com/lychen-lab/lychen/commit/a2e06c5fe608bafce9a4d3cb440fef98f548c026))
* **mco:** opt-in PostgreSQL MCO stack for the three APIs (POC) ([#265](https://github.com/lychen-lab/lychen/issues/265)) ([44d98e6](https://github.com/lychen-lab/lychen/commit/44d98e647d2fb4c206414f6f292c434a352872ff))
* **mercure:** move the central hub to Mercure 1.1 and espace to the 1.0 protocol ([#242](https://github.com/lychen-lab/lychen/issues/242)) ([7ac0578](https://github.com/lychen-lab/lychen/commit/7ac0578db57a0c45516ea76f4d183bba50f7db36))
* **tera:** scaffold tera's front libs, with lands as the first resource ([#262](https://github.com/lychen-lab/lychen/issues/262)) ([732fb09](https://github.com/lychen-lab/lychen/commit/732fb0900453c5f11d8a755b0c9802efa33b6481))
* **website:** add fallback Open Graph image for SEO embeds ([#196](https://github.com/lychen-lab/lychen/issues/196)) ([6393200](https://github.com/lychen-lab/lychen/commit/63932002b6e5ceb846b0f58c8f4521452a0631a2))
* **website:** add per-page schema.org structured data ([#192](https://github.com/lychen-lab/lychen/issues/192)) ([75ba179](https://github.com/lychen-lab/lychen/commit/75ba1796a70f991af864ad269f2ff69c9bacd3a8))
* **website:** compléter la politique de confidentialité et supprimer la page CGU ([#188](https://github.com/lychen-lab/lychen/issues/188)) ([d4c7fcc](https://github.com/lychen-lab/lychen/commit/d4c7fcc4d0f31f91d00966323a9ec8b8c911c2f8))
* **zitadel:** deploy self-hosted Zitadel via the dokploy pipeline ([#200](https://github.com/lychen-lab/lychen/issues/200)) ([cbe656d](https://github.com/lychen-lab/lychen/commit/cbe656dcd62c2ab2bacf86e5389c10913fafc1ec))
* **zitadel:** support v4 Login V2 (separate login container) ([#201](https://github.com/lychen-lab/lychen/issues/201)) ([4b6e19a](https://github.com/lychen-lab/lychen/commit/4b6e19a5daf2ba718203b64ab49a095938019bfc))


### Bug Fixes

* **api:** let every RabbitMQ tenant's password come from the environment ([#236](https://github.com/lychen-lab/lychen/issues/236)) ([1014a17](https://github.com/lychen-lab/lychen/commit/1014a17d469de3f139c4894a8e274bcc53e5ba4d))
* **ci:** keep lint-fix out of the frontend lint check ([#234](https://github.com/lychen-lab/lychen/issues/234)) ([130f058](https://github.com/lychen-lab/lychen/commit/130f058b65e64c58443d95f01410b50362af5669))
* **ci:** make the frontend format check fail on unformatted files ([#232](https://github.com/lychen-lab/lychen/issues/232)) ([3b3c533](https://github.com/lychen-lab/lychen/commit/3b3c53313a8edbd3896f8c2053e5273a199f4a4c))
* **compose:** keep flora and tera database and redis off lychen-network ([#266](https://github.com/lychen-lab/lychen/issues/266)) ([08b0e9d](https://github.com/lychen-lab/lychen/commit/08b0e9d912f18c86efa8c271f164cdd5e95e8eee))
* **deps:** patch the Symfony security advisories of November 2026 ([#252](https://github.com/lychen-lab/lychen/issues/252)) ([036591e](https://github.com/lychen-lab/lychen/commit/036591ed00c0b54f860d0e1f8386ee0ab9587607))
* **deps:** resolve the open Dependabot security alerts ([#244](https://github.com/lychen-lab/lychen/issues/244)) ([1badcfa](https://github.com/lychen-lab/lychen/commit/1badcfaf9ea2ac58612989b30f0b46e486afd855))
* **espace-api:** make the API bootable from a fresh checkout ([#212](https://github.com/lychen-lab/lychen/issues/212)) ([b109a68](https://github.com/lychen-lab/lychen/commit/b109a68bcde7ef4ec4d857ea1ed8dffe87ca683e))
* **espace-api:** secure and fix the AreaProposal/AreaRequest DTO exposure ([#220](https://github.com/lychen-lab/lychen/issues/220)) ([de44c9b](https://github.com/lychen-lab/lychen/commit/de44c9bd24db2058de251a48510ef4f8d26d9f1b))
* **espace-website:** fix what is broken or misleading on the public site ([#264](https://github.com/lychen-lab/lychen/issues/264)) ([0d73a5f](https://github.com/lychen-lab/lychen/commit/0d73a5ffd64c2d272c035a0ce46b95d4ed5c74af))
* repair the frontend checks hidden by affected-only CI ([#226](https://github.com/lychen-lab/lychen/issues/226)) ([d41ddfa](https://github.com/lychen-lab/lychen/commit/d41ddfab62d09d2acd089e73bcc0eb7b010b9813))
* **website:** escape the @ in the legal pages' email addresses ([#239](https://github.com/lychen-lab/lychen/issues/239)) ([d5f2a06](https://github.com/lychen-lab/lychen/commit/d5f2a067180ea9ae96c8c87716825c3416a03328))


### Refactors

* **api:** build every API from one shared Dockerfile ([#271](https://github.com/lychen-lab/lychen/issues/271)) ([1f0083d](https://github.com/lychen-lab/lychen/commit/1f0083d21cce3412ff29e8815e5d1d35a9f12a59))
* **api:** share the APIs' PHPUnit base classes through lychen/test ([#235](https://github.com/lychen-lab/lychen/issues/235)) ([23fd390](https://github.com/lychen-lab/lychen/commit/23fd3901ff5aa2856d6b649bed14277e34bd752e))
* **front-model:** a concept's workflow position is its state ([#267](https://github.com/lychen-lab/lychen/issues/267)) ([1f4c2d3](https://github.com/lychen-lab/lychen/commit/1f4c2d313eb697adb1fdec5b16ed7f19a67b105e))
* **zitadel:** move the Zitadel lib to libs/vue/zitadel ([#261](https://github.com/lychen-lab/lychen/issues/261)) ([aafac1d](https://github.com/lychen-lab/lychen/commit/aafac1d188f3b6d5c327fa3dd9e285dc795c661c))

## 0.1.0 (2026-06-14)


### Bug Fixes

* **api:** replace removed monolog "sentry" handler type with service handler ([#167](https://github.com/lychen-lab/lychen/issues/167)) ([6eba6c6](https://github.com/lychen-lab/lychen/commit/6eba6c69242ee938f784f4f6af593cdc15609a8c))
