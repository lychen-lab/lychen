# Découpler les API des frontends

> Proposition d'architecture — réponse à la question « l'API ne devrait pas
> déclencher un redéploiement des fronts ; les fronts doivent pouvoir s'adapter
> progressivement (dépréciations, nouveautés) à leur rythme. »

## 1. Contexte : comment le couplage existe aujourd'hui

Dans le monorepo, chaque frontend dépend du SDK TypeScript généré à partir de la
spec OpenAPI de l'API correspondante. Pour `tera` :

- `libs/typescript/tera/api-sdk/generated/tera-api.ts` est **généré** depuis
  `projects/tera/api/openapi_docs.json`
  (`moon typescript-tera-api-sdk:generate-api`).
- `projects/tera/app/moon.yml` déclare `dependsOn: [… typescript-tera-api-sdk …]`
  et `projects/tera/app/package.json` la consomme via
  `"@lychen/typescript-tera-api-sdk": "workspace:*"`.

La chaîne de redéploiement qui en découle :

1. **L'API change** → un PR doit régénérer la spec et le SDK. Le workflow
   `.github/workflows/openapi-sdk-sync.yml` **échoue tant que le SDK committé
   n'est pas régénéré et committé** (`git diff --exit-code` sur
   `openapi_docs.json` et `libs/typescript/tera/api-sdk/generated/`).
2. **Le fichier généré du SDK change** dans `libs/**`.
3. Au merge sur `main`, `.github/workflows/build-and-push-docker-images.yml`
   (déclenché sur `libs/**` et `projects/**`) appelle
   `.github/actions/moon-affected-projects-list` avec
   `moon query projects --affected --downstream deep`.
4. `--downstream deep` remonte **tous** les projets en aval du SDK : comme les
   fronts en `dependsOn`, ils sont marqués **affected** et rebuildés
   (`docker-buildx`).
5. `.github/workflows/deploy.yml` redéploie alors chaque projet affecté.

**Conséquence** : toute évolution de l'API — même purement additive, voire un
simple changement de description dans la spec — régénère le SDK, ce qui marque
mécaniquement les fronts comme « affected » et provoque leur rebuild +
redéploiement, sans qu'aucune ligne de code front n'ait été écrite.

## 2. Est-ce « bien » ou « pas bien » ? — analyse

Il faut séparer deux couplages que le mécanisme actuel mélange :

### 2.1. Couplage de **compilation** (types) — à conserver

Le SDK n'apporte que des **types** (et des chemins). Ils sont consommés au
*build time*. Le fait qu'une rupture de contrat de l'API casse la compilation
TypeScript du front est une **bonne chose** : c'est un garde-fou qui empêche un
front de partir en production en croyant à un contrat qui n'existe plus. On ne
veut **pas** supprimer ce couplage-là.

### 2.2. Couplage de **déploiement** — le vrai problème

Régénérer le SDK déclenche un rebuild + redéploiement de tous les fronts, même
quand :

- le changement d'API est **purement additif** (nouvel endpoint, nouveau champ
  optionnel) → le bundle front est inchangé ou quasi inchangé ;
- le changement ne touche **que la documentation** de la spec ;
- le front n'utilise même pas l'endpoint modifié.

Les coûts :

- **Gaspillage CI/CD** : builds multi-plateformes (`linux/arm64,linux/amd64`)
  et déploiements Dokploy inutiles.
- **Risque opérationnel** : chaque redéploiement est une occasion de casser la
  prod (cache busting PWA, invalidation de session, fenêtre d'indisponibilité)
  pour un changement qui ne concernait pas le front.
- **Pas de fenêtre d'adaptation** : aujourd'hui l'API et le front avancent en
  lockstep. Il n'existe aucune notion de « le front a N semaines pour suivre une
  dépréciation ». Une rupture de contrat est soit bloquée par l'échec de
  compilation, soit elle force une correction immédiate dans le **même** flux.

**Verdict** : le couplage de *types* est sain et doit rester. Le couplage de
*déploiement* est à supprimer, et il faut introduire un **cycle de vie de
dépréciation** pour que les fronts suivent les évolutions à leur rythme.

## 3. Solution proposée

Trois axes complémentaires. L'axe A règle le redéploiement subi ; l'axe B donne
le filet de sécurité (pas de rupture silencieuse) ; l'axe C formalise
l'adaptation progressive demandée.

### Axe A — Le SDK devient un artefact **versionné**, plus une dépendance `workspace:*` implicite

Aujourd'hui `workspace:*` + `--downstream deep` = « toute régénération pousse
immédiatement tout le monde ». On remplace ce lien implicite par un lien
**explicite et versionné** :

1. Le SDK porte une **version SemVer** (`libs/typescript/tera/api-sdk/package.json`)
   et est **publié** comme paquet (registre privé — GitHub Packages convient,
   le repo publie déjà des images sur `ghcr.io`). La version suit le contrat :
   - **patch** : changement de doc / cosmétique de la spec ;
   - **minor** : ajout additif (nouvel endpoint, champ optionnel, marquage
     `deprecated`) — rétrocompatible ;
   - **major** : suppression / rupture de contrat.
2. Les fronts épinglent une **plage** (`^x.y.z`) au lieu de `workspace:*`.
3. La régénération du SDK après un changement d'API **ne touche plus le code
   source d'un front**. Les fronts ne sont donc plus « affected » par la
   régénération : `--downstream deep` ne les remonte plus tant que **leur**
   `package.json` (version épinglée) n'a pas changé.
4. La montée de version côté front devient une **action délibérée** : un bot
   (Renovate / Dependabot) ouvre un PR « bump @lychen/…-api-sdk vers x.y ». Ce
   PR :
   - fait tourner le build TS du front (le garde-fou de §2.1 est conservé) ;
   - est **revu** — c'est le moment où l'on traite les dépréciations ;
   - et **lui seul** déclenche le redéploiement du front concerné.

> Variante « moins de plomberie » si l'on veut éviter un registre tout de suite :
> garder `workspace:*` mais **découpler le déclencheur de déploiement** de la
> régénération du SDK — p. ex. exclure les fichiers `generated/` de la détection
> `affected` pour les fronts, ou ne déclencher `docker-buildx` d'un front que
> lorsque son propre `projectRoot` change. C'est plus rapide à mettre en place
> mais cela **affaiblit** le garde-fou de compilation (un front pourrait être
> déployé sans avoir recompilé contre le nouveau contrat). L'axe A versionné est
> préférable car il conserve le garde-fou **et** rend l'adoption explicite.

### Axe B — Empêcher les ruptures **silencieuses** : détection de breaking changes sur la spec

Le découplage du déploiement ne doit pas se payer en ruptures non détectées.
On ajoute au CI de l'API une **comparaison de la spec OpenAPI** entre la base et
le PR (outil type [`oasdiff`](https://github.com/oasdiff/oasdiff)) :

- un changement **breaking** (suppression de champ/endpoint, resserrage de type,
  champ optionnel devenu requis) **échoue le PR** sauf label explicite
  `api-breaking-change` + bump **major** du SDK ;
- les changements additifs passent en **minor**, la doc seule en **patch**.

Cela transforme « l'API casse, on verra bien » en une règle de contrat
appliquée automatiquement, et alimente le versioning SemVer de l'axe A.

### Axe C — Cycle de vie de **dépréciation** (l'adaptation progressive demandée)

Pour qu'un front ait « le temps de s'adapter », l'API doit pouvoir évoluer
**sans casser** pendant une fenêtre donnée :

1. **Ne jamais casser en place.** Un champ/endpoint à retirer est d'abord
   **marqué déprécié**, pas supprimé. Côté API Platform, `deprecationReason`
   produit `deprecated: true` dans la spec OpenAPI ; `openapi-typescript` le
   propage en JSDoc `@deprecated`, donc visible dans l'IDE des devs front.
2. **Signaler au runtime.** Renvoyer les en-têtes HTTP standard
   [`Deprecation`](https://datatracker.ietf.org/doc/html/draft-ietf-httpapi-deprecation-header)
   et [`Sunset`](https://datatracker.ietf.org/doc/html/rfc8594) sur les routes
   dépréciées, pour tracer l'usage résiduel en observabilité.
3. **Fenêtre de support.** Politique explicite (p. ex. « une route dépréciée est
   maintenue au moins 2 releases / N semaines »). La suppression = bump **major**
   du SDK + label `api-breaking-change`, jamais avant la fin de la fenêtre.
4. **Évolution parallèle plutôt que mutation.** Pour un vrai changement de
   contrat, exposer la nouvelle forme **à côté** de l'ancienne (nouveau champ,
   nouvelle route/version) ; le front bascule via son PR de bump SDK (axe A),
   puis l'ancienne forme est dépréciée puis retirée à la release suivante.

Résultat : l'API peut livrer en continu ; chaque front consomme les nouveautés
et résorbe les dépréciations **quand il le décide**, dans un PR revu, et n'est
redéployé **que** pour ses propres changements.

## 4. Plan de mise en œuvre (incrémental, sans big bang)

1. **Filet de sécurité d'abord (axe B + C.1/C.2)** — faible risque, gros gain :
   - ajouter le diff OpenAPI (`oasdiff`) au CI des API ;
   - poser la convention `deprecated` + en-têtes `Deprecation`/`Sunset` ;
   - documenter la politique de fenêtre de dépréciation.
2. **Découpler le déploiement (axe A)** sur **un** couple pilote (`tera`) :
   - versionner et publier `@lychen/typescript-tera-api-sdk` (GitHub Packages) ;
   - passer `tera-app` de `workspace:*` à une plage épinglée ;
   - brancher Renovate pour ouvrir les PR de bump SDK ;
   - vérifier que la régénération du SDK ne marque plus `tera-app` comme affecté.
3. **Généraliser** à `espace` (et aux autres consommateurs) une fois le pilote
   validé.
4. **Garde-fou conservé** : le build TS de chaque front reste la barrière contre
   l'adoption d'un contrat incompatible — il se joue désormais dans le PR de bump
   SDK, pas dans un redéploiement subi.

## 5. Ce qu'on ne change pas (et pourquoi)

- **Le couplage de types reste** : c'est lui qui empêche un front de mentir sur
  le contrat de l'API. On le déplace du « redéploiement automatique » vers un
  « PR de montée de version revu ».
- **La génération depuis OpenAPI reste** la source de vérité unique du contrat —
  on ne réécrit pas de client à la main.

## 6. TL;DR

| Couplage | Aujourd'hui | Cible |
| --- | --- | --- |
| Types (build) | Implicite via `workspace:*`, rejoue à chaque régén. | Conservé, via SDK **versionné** épinglé par le front |
| Déploiement | Subi : toute régén. SDK redéploie tous les fronts | Déclenché **uniquement** par un PR de bump SDK revu |
| Ruptures de contrat | Détectées tard (échec de build) ou non détectées | Détectées au PR API (`oasdiff`) + gérées par dépréciation |
| Rythme du front | Lockstep avec l'API | Le front adopte **quand il veut**, fenêtre de dépréciation |
