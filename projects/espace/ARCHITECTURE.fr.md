# espace.lychen — Architecture (FR)

> **Note** : traduction française à destination des développeurs humains. Ce n'est **pas** la version de référence — en cas de divergence, [`ARCHITECTURE.md`](./ARCHITECTURE.md) (anglais) fait foi.

Complète `CLAUDE.md` (racine du monorepo) pour tout ce qui concerne le projet `espace` (`projects/espace/`). Claude Code chargera ce contexte en plus du fichier racine lors des sessions sur ce sous-projet.

## Infrastructure

Déployée via **Dokploy**.

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
    └── Worker Temporal (même image Docker que l'API, entrypoint bin/worker.php)
```

**Environnement DEV** : Temporal, Novu, Zitadel et Minio tournent sur des **instances partagées**, pas en local. Objectif : ne pas avoir à faire tourner ces services sur les machines de dev. Seuls l'API, le front et le worker de `espace.lychen` sont lancés localement, pointant vers ces instances partagées.

> À documenter séparément quand disponible : URLs des instances partagées DEV, procédure d'obtention des credentials/namespaces Temporal, Zitadel, Novu.

## Domaine métier — Workflows Temporal

4 workflows, séparés intentionnellement selon les règles métier de validation.

### 1. `ValidationAreaRequestWorkflow`

- **Déclencheur** : `POST` d'une `AreaRequest`
- **Rôle** : vérifie la complétude du profil _seeker_ et la cohérence de la demande ; attend une décision de modération humaine si nécessaire (avec timeout)
- **Sortie** : si approuvé → déclenche `MatchingWorkflow`

### 2. `ValidationAreaProposalWorkflow`

- **Déclencheur** : `POST` d'une `AreaProposal`
- **Rôle** : vérifie les informations foncières ; même logique de modération que ci-dessus
- **Sortie** : si approuvé → signale (`signal`) tous les `MatchingWorkflow` ouverts pour qu'ils réévaluent leurs candidats

### 3. `MatchingWorkflow`

- **Cardinalité** : un par `AreaRequest` approuvée
- **Rôle** : scanne les `AreaProposal` actives, score chaque paire, spawne un `MatchLifecycleWorkflow` par candidat pertinent
- **Particularité** : reste en vie pour traiter les nouvelles proposals arrivant après son démarrage (via signal depuis `ValidationAreaProposalWorkflow`)
- **Fin** : quand la demande est satisfaite ou expirée

### 4. `MatchLifecycleWorkflow`

- **Cardinalité** : un par paire `AreaRequest` + `AreaProposal`
- **Rôle** : notifie les deux parties, gère les délais de réponse, orchestre l'acceptation mutuelle
- **Fin (succès)** : accord des deux côtés → création de l'accord + clôture de la `AreaRequest`
- **Fin (échec)** : timeout ou refus → terminaison silencieuse

## Conventions Temporal — non négociables

- **Payloads = IDs uniquement.** Aucune donnée métier ne transite entre workflows/activités — uniquement des identifiants (UUID des entités Postgres).
- **Source de vérité = PostgreSQL.** Les données métier vivent exclusivement en base ; Temporal orchestre, ne stocke pas.
- **Statut dupliqué en base.** Le statut des entités (`AreaRequest`, `AreaProposal`, matchs...) est dupliqué dans Postgres pour permettre les requêtes API ; les _activities_ Temporal sont responsables de sa mise à jour au fil du workflow.
- **Notifications = activities uniquement.** Toute notification part exclusivement d'une activity Temporal, via Novu. Jamais d'appel direct à Novu depuis l'API.
- **Images = Minio direct.** Les images de `AreaProposal` sont stockées dans Minio et servies directement au front (URLs signées ou publiques selon le bucket) — l'API ne fait pas transiter les fichiers.

## Implications pour l'implémentation

Quand on implémente une feature touchant ce domaine :

1. **Activity vs domaine** : une _activity_ Temporal PHP appelle des services métier existants (ou à créer) côté Symfony — elle ne doit pas contenir de logique métier elle-même, juste l'orchestration + persistance du statut.
2. **Idempotence** : les activities doivent être idempotentes (retries Temporal) — vérifier avant d'écrire, pas juste écrire.
3. **Nommage** : suivre le nommage des 4 workflows ci-dessus tel quel pour toute classe PHP correspondante (`ValidationAreaRequestWorkflow`, etc.) et leurs activities associées (`*Activities`).
4. **Signals** : `ValidationAreaProposalWorkflow` → `MatchingWorkflow` se fait via signal, pas via nouvelle exécution de workflow ; documenter le nom du signal et son payload (ID de la `AreaProposal`) dans le code.
5. **Tests** : privilégier le Temporal Test Framework (workflow environment en mémoire) plutôt que de dépendre de l'instance partagée pour les tests unitaires/PHPUnit.

---

_Document à tenir à jour au fur et à mesure de l'implémentation — notamment ajouter le mapping Workflow ↔ classes PHP réelles une fois codées, et les noms exacts des queues Temporal utilisées._
