# Changelog

## [0.2.0](https://github.com/lychen-lab/lychen/compare/v0.1.0...v0.2.0) (2026-10-28)


### ⚠ BREAKING CHANGES

* **api:** turn the central RabbitMQ into a topic-exchange event bus ([#221](https://github.com/lychen-lab/lychen/issues/221))
* **api:** replace the per-API RabbitMQ with one central broker ([#215](https://github.com/lychen-lab/lychen/issues/215))

### Features

* add a central Mercure hub and push espace area proposals live ([#218](https://github.com/lychen-lab/lychen/issues/218)) ([a4be8a5](https://github.com/lychen-lab/lychen/commit/a4be8a5c7b09e7fe4e1de6fc20401bcfb64c70b2))
* **api:** replace the per-API RabbitMQ with one central broker ([#215](https://github.com/lychen-lab/lychen/issues/215)) ([783d34a](https://github.com/lychen-lab/lychen/commit/783d34abf9c76de5bd0cd6704ce9c6a7edb2042a))
* **api:** share the APIs' Symfony config through lychen/config ([#225](https://github.com/lychen-lab/lychen/issues/225)) ([5bb6a8b](https://github.com/lychen-lab/lychen/commit/5bb6a8bfcb25500c890b42f773beef2e47c26fa0))
* **api:** turn the central RabbitMQ into a topic-exchange event bus ([#221](https://github.com/lychen-lab/lychen/issues/221)) ([8a03143](https://github.com/lychen-lab/lychen/commit/8a031437e507a1449d06991637141c7174b35b0f))
* **espace-api:** expose each workflow resource's Mermaid diagram ([#237](https://github.com/lychen-lab/lychen/issues/237)) ([9dc57ad](https://github.com/lychen-lab/lychen/commit/9dc57ad05a83fd07c473cd78d8e78bc7232b337c))
* **espace-api:** link AreaProposal to its proposer ([#214](https://github.com/lychen-lab/lychen/issues/214)) ([6a29bc1](https://github.com/lychen-lab/lychen/commit/6a29bc153023daf8472e77d6bc8370d3b74e1bfd))
* **espace-website:** complete Terms of Use (CGU) page ([#191](https://github.com/lychen-lab/lychen/issues/191)) ([0237b78](https://github.com/lychen-lab/lychen/commit/0237b786d9bdfd446a6e636fc692eee9a20731fd))
* initialize AreaRequest structure ([#210](https://github.com/lychen-lab/lychen/issues/210)) ([a2e06c5](https://github.com/lychen-lab/lychen/commit/a2e06c5fe608bafce9a4d3cb440fef98f548c026))
* **mercure:** move the central hub to Mercure 1.1 and espace to the 1.0 protocol ([#242](https://github.com/lychen-lab/lychen/issues/242)) ([7ac0578](https://github.com/lychen-lab/lychen/commit/7ac0578db57a0c45516ea76f4d183bba50f7db36))
* **website:** add fallback Open Graph image for SEO embeds ([#196](https://github.com/lychen-lab/lychen/issues/196)) ([6393200](https://github.com/lychen-lab/lychen/commit/63932002b6e5ceb846b0f58c8f4521452a0631a2))
* **website:** add per-page schema.org structured data ([#192](https://github.com/lychen-lab/lychen/issues/192)) ([75ba179](https://github.com/lychen-lab/lychen/commit/75ba1796a70f991af864ad269f2ff69c9bacd3a8))
* **website:** compléter la politique de confidentialité et supprimer la page CGU ([#188](https://github.com/lychen-lab/lychen/issues/188)) ([d4c7fcc](https://github.com/lychen-lab/lychen/commit/d4c7fcc4d0f31f91d00966323a9ec8b8c911c2f8))
* **zitadel:** deploy self-hosted Zitadel via the dokploy pipeline ([#200](https://github.com/lychen-lab/lychen/issues/200)) ([cbe656d](https://github.com/lychen-lab/lychen/commit/cbe656dcd62c2ab2bacf86e5389c10913fafc1ec))
* **zitadel:** support v4 Login V2 (separate login container) ([#201](https://github.com/lychen-lab/lychen/issues/201)) ([4b6e19a](https://github.com/lychen-lab/lychen/commit/4b6e19a5daf2ba718203b64ab49a095938019bfc))


### Bug Fixes

* **api:** let every RabbitMQ tenant's password come from the environment ([#236](https://github.com/lychen-lab/lychen/issues/236)) ([1014a17](https://github.com/lychen-lab/lychen/commit/1014a17d469de3f139c4894a8e274bcc53e5ba4d))
* **ci:** keep lint-fix out of the frontend lint check ([#234](https://github.com/lychen-lab/lychen/issues/234)) ([130f058](https://github.com/lychen-lab/lychen/commit/130f058b65e64c58443d95f01410b50362af5669))
* **ci:** make the frontend format check fail on unformatted files ([#232](https://github.com/lychen-lab/lychen/issues/232)) ([3b3c533](https://github.com/lychen-lab/lychen/commit/3b3c53313a8edbd3896f8c2053e5273a199f4a4c))
* **deps:** resolve the open Dependabot security alerts ([#244](https://github.com/lychen-lab/lychen/issues/244)) ([1badcfa](https://github.com/lychen-lab/lychen/commit/1badcfaf9ea2ac58612989b30f0b46e486afd855))
* **espace-api:** make the API bootable from a fresh checkout ([#212](https://github.com/lychen-lab/lychen/issues/212)) ([b109a68](https://github.com/lychen-lab/lychen/commit/b109a68bcde7ef4ec4d857ea1ed8dffe87ca683e))
* **espace-api:** secure and fix the AreaProposal/AreaRequest DTO exposure ([#220](https://github.com/lychen-lab/lychen/issues/220)) ([de44c9b](https://github.com/lychen-lab/lychen/commit/de44c9bd24db2058de251a48510ef4f8d26d9f1b))
* repair the frontend checks hidden by affected-only CI ([#226](https://github.com/lychen-lab/lychen/issues/226)) ([d41ddfa](https://github.com/lychen-lab/lychen/commit/d41ddfab62d09d2acd089e73bcc0eb7b010b9813))
* **website:** escape the @ in the legal pages' email addresses ([#239](https://github.com/lychen-lab/lychen/issues/239)) ([d5f2a06](https://github.com/lychen-lab/lychen/commit/d5f2a067180ea9ae96c8c87716825c3416a03328))


### Refactors

* **api:** share the APIs' PHPUnit base classes through lychen/test ([#235](https://github.com/lychen-lab/lychen/issues/235)) ([23fd390](https://github.com/lychen-lab/lychen/commit/23fd3901ff5aa2856d6b649bed14277e34bd752e))

## 0.1.0 (2026-06-14)


### Bug Fixes

* **api:** replace removed monolog "sentry" handler type with service handler ([#167](https://github.com/lychen-lab/lychen/issues/167)) ([6eba6c6](https://github.com/lychen-lab/lychen/commit/6eba6c69242ee938f784f4f6af593cdc15609a8c))
