# Analyse : Temporal vs Symfony Workflow pour les workflows des APIs

> Contexte : POC demandé sur l'API **espace**, en prenant le cycle de vie d'une
> `AreaProposal` (_landProposal_) comme cas réel. Objectif : déterminer si
> remplacer le composant **Symfony Workflow** par **Temporal** est pertinent.

## TL;DR — Recommandation

**Ne pas migrer les workflows existants vers Temporal aujourd'hui.** Le composant
Symfony Workflow couvre parfaitement le besoin actuel (machines à états courtes,
pilotées par des requêtes HTTP). Temporal résout une catégorie de problèmes que
Lychen **n'a pas encore** : orchestrations longues, multi-étapes, avec attentes,
timers et appels externes non fiables.

➡️ **Garder Symfony Workflow** pour les transitions de statut métier
(`AreaProposal`, `AreaRequest`, etc.).

➡️ **Réévaluer Temporal le jour où** apparaît un vrai processus distribué et
durable (ex. : cycle complet d'une mise en relation `landRequest ↔ landProposal`
avec relances, délais d'expiration, paiement, contractualisation, jobs externes).
Le POC de ce dossier est la base de cette réévaluation.

---

## Ce que fait chaque outil

### Symfony Workflow (l'existant)

`config/packages/workflow/workflow.php` définit la machine à états
`area_proposal_publishing` :

```
draft ──submit──▶ verification ──publish──▶ published ──archive──▶ archived
                  verification ──reject───▶ draft
```

C'est un **ensemble de règles** : « depuis quelle place puis-je aller vers
quelle place ». Le composant :

- valide qu'une transition est autorisée (`can()` / `apply()`) ;
- déplace une propriété de l'entité (`place`) ;
- déclenche des événements (`workflow.*`) sur lesquels on branche des listeners ;
- fournit un audit trail en mémoire.

L'état vit **dans la base** (colonne `place`). Le « moteur » n'est qu'une
validation appliquée pendant une requête HTTP, puis il disparaît. C'est simple,
synchrone, testable, sans aucune infrastructure.

### Temporal (le POC)

Temporal est un **moteur d'orchestration durable**. Le workflow est du code PHP
qui **s'exécute dans la durée** : il peut attendre des heures ou des mois, dormir,
réagir à des signaux, appeler des « activités » (effets de bord) et **survivre aux
redéploiements et aux crashs** car Temporal rejoue l'historique d'événements pour
reconstruire l'état exact.

Dans le POC (`src/AreaProposalWorkflow.php`) :

- la table de transitions est identique à la version Symfony ;
- chaque transition déclenche des **activités retentées automatiquement**
  (persistance, notifications, indexation SEO) ;
- un **timer durable** auto-archive une proposition publiée depuis trop longtemps,
  sans cron ni Messenger.

---

## Comparaison

| Critère | Symfony Workflow | Temporal |
| --- | --- | --- |
| **Nature** | Validation de transitions, synchrone | Orchestration durable, asynchrone |
| **Où vit l'état** | Colonne en base (`place`) | Historique d'événements Temporal (+ projection en base via activités) |
| **Processus longs / attentes** | ❌ Non (il faut cron + Messenger + colonnes de suivi) | ✅ Natif (`await`, timers durables de plusieurs mois) |
| **Reprise après crash / redeploy** | N/A (rien ne tourne) | ✅ Garantie, rejeu déterministe |
| **Retry des effets de bord** | À coder à la main (Messenger retry) | ✅ Intégré, configurable (backoff, max attempts) |
| **Unicité d'exécution** | À gérer soi-même | ✅ `WorkflowId` unique imposé par Temporal |
| **Visibilité / debug** | Audit trail mémoire + logs | ✅ UI dédiée : historique complet, état, replay |
| **Tests** | Unitaires triviaux, aucun service | SDK de test + worker ; plus lourd |
| **Infrastructure** | **Aucune** (déjà dans Symfony) | **Lourde** : serveur Temporal + sa base, **RoadRunner**, extensions `grpc`/`protobuf` |
| **Runtime** | FrankenPHP (déjà en place) | RoadRunner en **plus** de FrankenPHP (2e runtime PHP à opérer) |
| **Langage** | PHP/Symfony idiomatique | PHP via SDK + contraintes de **déterminisme** dans le workflow |
| **Courbe d'apprentissage** | Faible (connu de l'équipe) | Élevée (déterminisme, signals/queries, versioning) |
| **Coût opérationnel** | ~0 | Cluster à héberger, monitorer, mettre à jour |

---

## Le point structurant : le runtime

L'API tourne sur **FrankenPHP** (`APP_RUNTIME: Runtime\FrankenPhpSymfony\Runtime`).
Le SDK PHP de Temporal impose **RoadRunner** comme hôte des workers (workflows et
activités s'exécutent dans des process RoadRunner pilotés en gRPC).

Adopter Temporal signifie donc faire **cohabiter deux runtimes PHP** (FrankenPHP
pour l'API HTTP, RoadRunner pour les workers Temporal), plus le cluster Temporal
et sa base. Pour la valeur apportée au besoin **actuel** (de simples transitions
de statut), ce surcoût d'exploitation n'est pas justifié.

À noter : Lychen possède **déjà RabbitMQ + Symfony Messenger**. Une bonne partie
de ce qu'on irait chercher dans Temporal à court terme (retry d'effets de bord,
traitement asynchrone) est **déjà disponible** avec la stack en place.

---

## Quand Temporal deviendrait le bon choix

Le besoin exprimé dans la tâche — « un workflow **complet** pour une landRequest
ou landProposal » — est précisément le terrain où Temporal brille **si** ce
workflow devient réellement long et distribué, par exemple :

1. une `AreaRequest` est ouverte ;
2. on attend des `AreaProposal` correspondantes (attente de plusieurs jours) ;
3. relances automatiques au demandeur / aux propriétaires (timers) ;
4. étape de vérification humaine (signal externe, possiblement après des semaines) ;
5. mise en relation, voire contractualisation / paiement via services externes
   (appels faillibles → retry) ;
6. expiration automatique si rien n'aboutit (timer durable).

Un tel processus, écrit « à la main » avec Symfony Workflow + cron + Messenger +
colonnes de suivi, devient vite fragile et difficile à observer. C'est exactement
ce que Temporal industrialise. **Tant que ce processus n'existe pas**, Temporal
est une solution sans problème à résoudre.

### Critères de décision (checklist)

Migrer/adopter Temporal devient pertinent dès que **plusieurs** de ces points sont vrais :

- [ ] Le processus s'étale sur **heures/jours/mois**, pas sur une requête HTTP.
- [ ] Il enchaîne **plusieurs appels externes faillibles** nécessitant retry/compensation.
- [ ] Il faut des **timers durables** (expirations, relances) fiables.
- [ ] La **reprise après crash** au milieu du processus est critique.
- [ ] Le besoin de **visibilité/audit** dépasse ce que donnent les logs.
- [ ] Le **coût d'exploitation** d'un cluster Temporal est acceptable pour l'équipe.

Aujourd'hui sur espace : **aucun** de ces points n'est rempli pour
`AreaProposal`. → Symfony Workflow reste le bon outil.

---

## Conclusion

| Question | Réponse |
| --- | --- |
| Est-ce techniquement possible ? | **Oui** — le POC reproduit fidèlement la machine à états et ajoute retries + timer durable. |
| Est-ce « mieux » pour le besoin actuel ? | **Non** — surcoût d'infrastructure et de runtime (RoadRunner + cluster) disproportionné face à de simples transitions de statut. |
| Faut-il jeter l'idée ? | **Non** — garder ce POC comme socle ; réévaluer dès qu'un workflow réellement long/distribué (mise en relation landRequest ↔ landProposal de bout en bout) sera au programme. |

**Décision recommandée : statu quo (Symfony Workflow), avec Temporal en option
documentée pour les futurs processus longue durée.**
