# Changelog

## [0.2.0](https://github.com/lychen-lab/lychen/compare/v0.1.0...v0.2.0) (2026-11-28)


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
* **front-model-business:** suggest the next log label from the existing ones ([#358](https://github.com/lychen-lab/lychen/issues/358)) ([6497315](https://github.com/lychen-lab/lychen/commit/6497315bfb6370a369dc4c818f11879c36d79fec))
* **front-model:** add the merge of a mushroom log's timeline ([#335](https://github.com/lychen-lab/lychen/issues/335)) ([846d38c](https://github.com/lychen-lab/lychen/commit/846d38c455c6cffa015e4fe7f81788fd9f0649fe))
* **front-model:** add the Myko mushroom concepts ([#326](https://github.com/lychen-lab/lychen/issues/326)) ([46f733d](https://github.com/lychen-lab/lychen/commit/46f733d4fe326e8e4015cdbee251733c3e61e769))
* **front-model:** check the input of the mushroom forms ([#340](https://github.com/lychen-lab/lychen/issues/340)) ([22ac832](https://github.com/lychen-lab/lychen/commit/22ac832849d4cbb1c312ff00eeb66aecf9b9738f))
* initialize AreaRequest structure ([#210](https://github.com/lychen-lab/lychen/issues/210)) ([a2e06c5](https://github.com/lychen-lab/lychen/commit/a2e06c5fe608bafce9a4d3cb440fef98f548c026))
* **mco:** opt-in PostgreSQL MCO stack for the three APIs (POC) ([#265](https://github.com/lychen-lab/lychen/issues/265)) ([44d98e6](https://github.com/lychen-lab/lychen/commit/44d98e647d2fb4c206414f6f292c434a352872ff))
* **mercure:** move the central hub to Mercure 1.1 and espace to the 1.0 protocol ([#242](https://github.com/lychen-lab/lychen/issues/242)) ([7ac0578](https://github.com/lychen-lab/lychen/commit/7ac0578db57a0c45516ea76f4d183bba50f7db36))
* **myko-app:** add the bottom navigation, every route and the "Plus" page ([#329](https://github.com/lychen-lab/lychen/issues/329)) ([91ba735](https://github.com/lychen-lab/lychen/commit/91ba73583e15c2506cf4c8c0ec9c893f336b5296))
* **myko-app:** configure the origin the QR codes of the labels open ([#341](https://github.com/lychen-lab/lychen/issues/341)) ([94d9ed8](https://github.com/lychen-lab/lychen/commit/94d9ed81f17f40529c6a5ded365c27365c6ac80d))
* **myko-app:** create the Myko PWA project ([#325](https://github.com/lychen-lab/lychen/issues/325)) ([1d0850c](https://github.com/lychen-lab/lychen/commit/1d0850c73bc144facfce50cc970ce781b3eaf892))
* **myko-app:** protect every route behind sign-in, with a sign-in screen ([#331](https://github.com/lychen-lab/lychen/issues/331)) ([2d5a8ca](https://github.com/lychen-lab/lychen/commit/2d5a8ca4ba2eeb692d146b2c54d4d8dee566aa62))
* **myko-app:** title every page, for the tab and for screen readers ([#343](https://github.com/lychen-lab/lychen/issues/343)) ([2c79379](https://github.com/lychen-lab/lychen/commit/2c79379e8879ba9ce531968d91cad52d46a1b887))
* **php:** PHP errors in the editor: Intelephense as you type, the monorepo's PHPStan on save ([#275](https://github.com/lychen-lab/lychen/issues/275)) ([5bd166d](https://github.com/lychen-lab/lychen/commit/5bd166d5e1e600bd063fc7b1e8ce6b7ae60c171d))
* **tera:** scaffold tera's front libs, with lands as the first resource ([#262](https://github.com/lychen-lab/lychen/issues/262)) ([732fb09](https://github.com/lychen-lab/lychen/commit/732fb0900453c5f11d8a755b0c9802efa33b6481))
* **typescript-myko:** add the batch labels and the QR code content helpers ([#334](https://github.com/lychen-lab/lychen/issues/334)) ([6289190](https://github.com/lychen-lab/lychen/commit/62891908768579db92867de73ddcad35e19da81d))
* **typescript-myko:** compute the layout of an A4 sheet of labels ([#338](https://github.com/lychen-lab/lychen/issues/338)) ([8bee914](https://github.com/lychen-lab/lychen/commit/8bee914c68325186c5cdc291933b1bbda817fa23))
* **typescript-myko:** read a scanned QR code and explain a camera that cannot be used ([#345](https://github.com/lychen-lab/lychen/issues/345)) ([7e9465b](https://github.com/lychen-lab/lychen/commit/7e9465b4abdbed5bf211c4a40132e1dcc6d417fb))
* **typescript-myko:** read QR codes with jsQR where the browser has no BarcodeDetector ([#349](https://github.com/lychen-lab/lychen/issues/349)) ([adec295](https://github.com/lychen-lab/lychen/commit/adec295a06cefc5688f76637987a5d3d086e05ea))
* **typescript-myko:** run a bulk entry over selected logs ([#342](https://github.com/lychen-lab/lychen/issues/342)) ([2dfc0f2](https://github.com/lychen-lab/lychen/commit/2dfc0f207375d56503672b20fcf7230e25557859))
* **vue-myko:** add the harvest statistics vocabulary, period ranges and weight format ([#332](https://github.com/lychen-lab/lychen/issues/332)) ([352129a](https://github.com/lychen-lab/lychen/commit/352129a126e6a8283bf0613fab43eeab54f929ad))
* **vue-myko:** add the journal entry kind icon ([#333](https://github.com/lychen-lab/lychen/issues/333)) ([9378bc5](https://github.com/lychen-lab/lychen/commit/9378bc53b3af39f6e2c2ebb52e995c12f916cc96))
* **vue-myko:** draw the A4 sheets of QR code labels ([#346](https://github.com/lychen-lab/lychen/issues/346)) ([7b148aa](https://github.com/lychen-lab/lychen/commit/7b148aa5501ffb2f1e79351f142eaf511624c7a0))
* **vue-myko:** draw the QR code of a log ([#339](https://github.com/lychen-lab/lychen/issues/339)) ([726b559](https://github.com/lychen-lab/lychen/commit/726b559cd24c4391e0e1de68b34bbc6f1abbae87))
* **vue-myko:** filter the list of logs through the URL and select several of them ([#344](https://github.com/lychen-lab/lychen/issues/344)) ([10be2e4](https://github.com/lychen-lab/lychen/commit/10be2e4cb1309a8561253d9c2084c78a00e5ef65))
* **vue-myko:** form to create and edit a log ([#356](https://github.com/lychen-lab/lychen/issues/356)) ([5d9e1b4](https://github.com/lychen-lab/lychen/commit/5d9e1b405dbf6414a59ad4412f9faf8e34f590bf))
* **vue-myko:** form to create logs in a series ([#357](https://github.com/lychen-lab/lychen/issues/357)) ([e8a4b58](https://github.com/lychen-lab/lychen/commit/e8a4b580fec478ba9f998486afc18fb7e3618ffd))
* **vue-myko:** form to inoculate a log ([#353](https://github.com/lychen-lab/lychen/issues/353)) ([7afb007](https://github.com/lychen-lab/lychen/commit/7afb007fe3707d6bbd7c4b01a2ce7ec338e0ee07))
* **vue-myko:** forms to create and edit a zone and a strain ([#355](https://github.com/lychen-lab/lychen/issues/355)) ([d39976e](https://github.com/lychen-lab/lychen/commit/d39976e82821b2749377fbac470f3750a4618edf))
* **vue-myko:** forms to weigh a harvest and write a journal entry ([#352](https://github.com/lychen-lab/lychen/issues/352)) ([db3457c](https://github.com/lychen-lab/lychen/commit/db3457c9e026656e5bcb426dfab8556c4a7d10c1))
* **vue-myko:** name the states, journal kinds and inoculation methods, and badge a log's state ([#330](https://github.com/lychen-lab/lychen/issues/330)) ([251419f](https://github.com/lychen-lab/lychen/commit/251419fd5836b4e94c90106789ec55da4f4d7904))
* **vue-myko:** scan QR codes with the camera ([#348](https://github.com/lychen-lab/lychen/issues/348)) ([9a90ce8](https://github.com/lychen-lab/lychen/commit/9a90ce855472923693d6f5aa0284e9535628e1ed))
* **vue-myko:** translate the fungus and wood species codes ([#328](https://github.com/lychen-lab/lychen/issues/328)) ([d37d7bc](https://github.com/lychen-lab/lychen/commit/d37d7bcac6551b6e4bf6ed58b24ad7347a632749))
* **website:** add fallback Open Graph image for SEO embeds ([#196](https://github.com/lychen-lab/lychen/issues/196)) ([6393200](https://github.com/lychen-lab/lychen/commit/63932002b6e5ceb846b0f58c8f4521452a0631a2))
* **website:** add per-page schema.org structured data ([#192](https://github.com/lychen-lab/lychen/issues/192)) ([75ba179](https://github.com/lychen-lab/lychen/commit/75ba1796a70f991af864ad269f2ff69c9bacd3a8))
* **website:** compléter la politique de confidentialité et supprimer la page CGU ([#188](https://github.com/lychen-lab/lychen/issues/188)) ([d4c7fcc](https://github.com/lychen-lab/lychen/commit/d4c7fcc4d0f31f91d00966323a9ec8b8c911c2f8))
* **zitadel:** deploy self-hosted Zitadel via the dokploy pipeline ([#200](https://github.com/lychen-lab/lychen/issues/200)) ([cbe656d](https://github.com/lychen-lab/lychen/commit/cbe656dcd62c2ab2bacf86e5389c10913fafc1ec))
* **zitadel:** support v4 Login V2 (separate login container) ([#201](https://github.com/lychen-lab/lychen/issues/201)) ([4b6e19a](https://github.com/lychen-lab/lychen/commit/4b6e19a5daf2ba718203b64ab49a095938019bfc))


### Bug Fixes

* **api:** clear the PHPStan baselines of espace, flora and tera ([#277](https://github.com/lychen-lab/lychen/issues/277)) ([b099d86](https://github.com/lychen-lab/lychen/commit/b099d8656f275faf31834285d9c0b2bf4a1bc1ed))
* **api:** let every RabbitMQ tenant's password come from the environment ([#236](https://github.com/lychen-lab/lychen/issues/236)) ([1014a17](https://github.com/lychen-lab/lychen/commit/1014a17d469de3f139c4894a8e274bcc53e5ba4d))
* **api:** mount libs/php over /packages in the dev stack only ([#279](https://github.com/lychen-lab/lychen/issues/279)) ([cf4a72a](https://github.com/lychen-lab/lychen/commit/cf4a72a9b40ab24393166623687baac8b3a6bcfb))
* **ci:** keep lint-fix out of the frontend lint check ([#234](https://github.com/lychen-lab/lychen/issues/234)) ([130f058](https://github.com/lychen-lab/lychen/commit/130f058b65e64c58443d95f01410b50362af5669))
* **ci:** make the frontend format check fail on unformatted files ([#232](https://github.com/lychen-lab/lychen/issues/232)) ([3b3c533](https://github.com/lychen-lab/lychen/commit/3b3c53313a8edbd3896f8c2053e5273a199f4a4c))
* **compose:** keep flora and tera database and redis off lychen-network ([#266](https://github.com/lychen-lab/lychen/issues/266)) ([08b0e9d](https://github.com/lychen-lab/lychen/commit/08b0e9d912f18c86efa8c271f164cdd5e95e8eee))
* **deps:** patch the Symfony security advisories of November 2026 ([#252](https://github.com/lychen-lab/lychen/issues/252)) ([036591e](https://github.com/lychen-lab/lychen/commit/036591ed00c0b54f860d0e1f8386ee0ab9587607))
* **deps:** resolve the open Dependabot security alerts ([#244](https://github.com/lychen-lab/lychen/issues/244)) ([1badcfa](https://github.com/lychen-lab/lychen/commit/1badcfaf9ea2ac58612989b30f0b46e486afd855))
* **espace-api:** make the API bootable from a fresh checkout ([#212](https://github.com/lychen-lab/lychen/issues/212)) ([b109a68](https://github.com/lychen-lab/lychen/commit/b109a68bcde7ef4ec4d857ea1ed8dffe87ca683e))
* **espace-api:** secure and fix the AreaProposal/AreaRequest DTO exposure ([#220](https://github.com/lychen-lab/lychen/issues/220)) ([de44c9b](https://github.com/lychen-lab/lychen/commit/de44c9bd24db2058de251a48510ef4f8d26d9f1b))
* **espace-website:** fix what is broken or misleading on the public site ([#264](https://github.com/lychen-lab/lychen/issues/264)) ([0d73a5f](https://github.com/lychen-lab/lychen/commit/0d73a5ffd64c2d272c035a0ce46b95d4ed5c74af))
* **myko-app:** make the inactive navigation labels meet WCAG AA contrast ([#350](https://github.com/lychen-lab/lychen/issues/350)) ([3957f8d](https://github.com/lychen-lab/lychen/commit/3957f8d1c3ee823bbda3dbbec6705e1f5becfdb3))
* **myko-app:** send an unknown path home without carrying its params along ([#347](https://github.com/lychen-lab/lychen/issues/347)) ([5c8cbe9](https://github.com/lychen-lab/lychen/commit/5c8cbe9cf36e002098a9997a01f896e6e642fc6f))
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
