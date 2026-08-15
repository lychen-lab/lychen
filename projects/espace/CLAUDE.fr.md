# CLAUDE.md — espace.lychen (FR)

> **Note** : cette version française est un complément à destination des développeurs humains qui préfèrent lire en français. Elle n'est **pas** chargée automatiquement par Claude Code (qui lit `CLAUDE.md`, la version anglaise, en priorité). En cas de divergence entre les deux, `CLAUDE.md` (anglais) fait foi.

Ce fichier complète le `CLAUDE.md` racine du monorepo. Il s'applique à tout travail dans `projects/espace/` (`api`, `app`, `worker`).

## Contexte à charger obligatoirement

Avant toute implémentation dans ce projet, lis en entier :

- [`ARCHITECTURE.md`](./ARCHITECTURE.md)

Ce document décrit :

- l'infrastructure partagée (Temporal, Novu, Zitadel, Minio) et l'environnement DEV
- les 4 workflows métier (`ValidationAreaRequestWorkflow`, `ValidationAreaProposalWorkflow`, `MatchingWorkflow`, `MatchLifecycleWorkflow`)
- les conventions Temporal non négociables (IDs uniquement entre workflows/activities, statut dupliqué en base, notifications via activities uniquement, images servies directement depuis Minio)

**Ne pas s'écarter de ces conventions sans validation explicite** — elles conditionnent la cohérence de tout le domaine `espace`.

## Structure du projet

```
projects/espace/
├── api/          # Symfony + API Platform
├── app/          # Vue 3 SPA
├── worker/       # Point d'entrée Temporal worker (bin/worker.php) — même image Docker que api/
├── CLAUDE.md     # version anglaise (référence, lue par Claude Code)
└── CLAUDE.fr.md  # ce fichier (complément humain)
```
