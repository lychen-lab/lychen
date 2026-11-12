# Stack MCO PostgreSQL (POC)

Démo de MCO (maintien en conditions opérationnelles) PostgreSQL appliquée aux trois instances de
Lychen (`espace-api`, `flora-api`, `tera-api`, chacune avec son PostgreSQL). Elle a la forme d'un
SI à une instance PostgreSQL par service. **Local uniquement** : rien n'est déployé par Dokploy,
tous les mots de passe sont des valeurs de démo.

| Domaine | Outil | Ce qu'on montre |
|---|---|---|
| Supervision | pgwatch + Grafana | santé, connexions, cache, tailles, top requêtes |
| Analyse de requêtes | PoWA | charge par requête, attentes, **index manquants validés par HypoPG** |
| Traçabilité | pgaudit | DDL, rôles, GRANT / REVOKE, écritures sur tables sensibles |
| Sauvegarde logique | Databasus | dumps planifiés, restauration en 1 clic, alerte « base indisponible » |
| Sauvegarde physique | pgBackRest | full + incrémental, archivage WAL continu, retour à l'instant T |
| Test de restauration | pgBackRest + pg_amcheck | restauration automatique dans une instance jetable, contrôle d'intégrité, durée de reprise |
| Alerting (démo) | Mailpit (`common/mailpit`) | boîte mail locale |

## 1. Architecture

```
                          réseau externe lychen-mco
  ┌───────────────────────────────────────────────────────────────────────────┐
  │  espace-database        flora-database          tera-database             │  alias uniques
  │  (+ mco-init,           (+ mco-init,            (+ mco-init,              │
  │   mco-pgbackrest)        mco-pgbackrest)         mco-pgbackrest)          │
  │                                                                           │
  │  common-mco : mco-powa-repo · mco-powa-collector · mco-powa-web ·         │
  │               mco-pgwatch (pgwatch + Grafana) · mco-databasus ────────────┼──> lychen-network ──> mailpit
  └───────────────────────────────────────────────────────────────────────────┘
        volume externe lychen-mco-pgbackrest-repo : dépôt pgBackRest commun, une stanza par domaine
```

- **Par API**, `projects/<domaine>/api/compose.mco.yml` s'ajoute à `compose.yml` et
  `compose.override.yml`. Il étend les gabarits de `compose.instance.yml` :
  - `database` passe sur l'image MCO (`lychen-local/mco-postgres:16`, Debian + PGDG), avec pgaudit,
    les extensions PoWA et l'archivage WAL. Elle tourne sur un **nouveau volume**
    (`database_data_mco`) et rejoint `lychen-mco` sous l'alias `<domaine>-database` ;
  - `mco-init` est un one-shot idempotent : rôles techniques, extensions, enregistrement dans PoWA
    et rechargement du collecteur ;
  - `mco-pgbackrest` est le sidecar : stanza, backups planifiés, test de restauration.
- **Partagé** : le projet moon `common-mco` (ce dossier) porte PoWA, pgwatch + Grafana et
  Databasus. Les alertes passent par `common-mailpit`.
- Les outils joignent toujours une instance par son alias (`espace-database`…), jamais par
  `database` : chaque stack en a une, ce nom serait ambigu. Les conteneurs `api` ne rejoignent
  pas `lychen-mco`.
- Le volume `database_data` habituel n'est **jamais touché**. L'image MCO est en glibc, les volumes
  existants ont été créés en Alpine (musl) : le tri du texte diffère, les index de texte seraient
  faux. En mode MCO, l'entrypoint de l'API rejoue les migrations Doctrine sur le volume neuf.

| Fichier | Rôle |
|---|---|
| `compose.yml` | outillage partagé (projet compose `common-mco`) |
| `compose.instance.yml` | gabarits des trois services ajoutés à chaque instance |
| `postgres/Dockerfile` | image MCO commune (`postgres:16.15-trixie` + pgBackRest, pgaudit, PoWA, HypoPG…) |
| `instance/` | scripts montés dans chaque instance : `init.sh`, `init-db.sql`, `register-powa.sql`, `pgbackrest.conf`, `pgbackrest-loop.sh`, `restore-test.sh` |
| `pgwatch/register-sources.sh` | déclare les trois instances comme sources pgwatch au démarrage |
| `../../<domaine>/api/compose.mco.yml` | ce qui est propre à chaque instance (stanza, alias, identifiants, volumes) |
| `../../../.moon/tasks/tag-mco.yml` | tâches `mco-up` / `mco-down` des API (tag `mco`) |

## 2. Lancement

Prérequis : Docker Compose ≥ 2.24 (Docker Desktop récent). Toutes les images existent en arm64.
PoWA est construit en local depuis PyPI, car les images `powateam/*` ne sont publiées qu'en amd64.

```bash
moon run espace-api:mco-up flora-api:mco-up tera-api:mco-up
```

`mco-up` démarre d'abord `common-mco`, puis mailpit, mercure et rabbitmq. Ensuite il lance la stack
de l'API avec l'overlay. Le premier lancement construit les images (quelques minutes) et l'API fait
son `composer install` si `vendor/` est vide. Une seule API suffit, par exemple
`moon run espace-api:mco-up`.

Ce qui se passe tout seul :

- l'instance s'enregistre dans PoWA, et le collecteur la prend en compte sans redémarrage ;
- elle est déclarée comme source pgwatch (preset `exhaustive_no_python`) ;
- la stanza pgBackRest est créée. Le premier backup full attend que le schéma Doctrine existe
  (`MCO_FIRST_BACKUP_AFTER_SQL`), puis un incrémental part toutes les heures. Un test de
  restauration est lancé après le premier backup, puis tous les 24 backups.

Arrêt et retour au mode normal (la base habituelle revient sur son volume d'origine) :

```bash
moon run espace-api:mco-down flora-api:mco-down tera-api:mco-down
moon run common-mco:down
moon run espace-api:up            # mode normal, inchangé
```

Après une modification de `postgres/Dockerfile` : `moon run espace-api:mco-up -- --build`.

Pour les commandes manuelles des scénarios, un raccourci (depuis la racine du repo) :

```bash
mco() { d="$1"; shift; docker compose -p "$d-api" -f "projects/$d/api/compose.yml" -f "projects/$d/api/compose.override.yml" -f "projects/$d/api/compose.mco.yml" "$@"; }
```

## 3. Accès

| Interface | URL | Identifiants |
|---|---|---|
| Grafana (pgwatch) | http://localhost:13000 | lecture anonyme, admin : `admin` / `pgwatchadmin` |
| pgwatch (sources) | http://localhost:8090 | aucun compte en démo (champs vides) |
| PoWA | http://localhost:8888 | `powa` / `powa`, serveur `mco` |
| Databasus | http://localhost:4005 | compte créé au premier accès (POC : `admin@mco.lychen.local` / `McoDemo-Local-2026`) |
| Mailpit | http://localhost:8025 | |

Ports modifiables par variables : `MCO_GRAFANA_PORT`, `MCO_PGWATCH_PORT`, `MCO_POWA_PORT`,
`MCO_DATABASUS_PORT`, `MAILPIT_UI_PORT`.

Instances, depuis les outils MCO (réseau `lychen-mco`) :

| Instance | Hôte | Base | Admin (dev) | Stanza pgBackRest | Port hôte |
|---|---|---|---|---|---|
| espace | `espace-database` | `app` | `userlocal` / `PasswordForLocal!` | `espace` | 19432 |
| flora | `flora-database` | `app` | idem | `flora` | 21432 |
| tera | `tera-database` | `app` | idem | `tera` | 22432 |

Rôles techniques créés par `mco-init` : `pgwatch` (`pg_monitor`), `databasus` (`pg_read_all_data`),
`powa` (`pg_read_all_stats` + `pg_read_all_data`), `mco_auditor` (NOLOGIN, cible de l'audit objet).
Mots de passe de démo : le nom du rôle (`MCO_*_PASSWORD` pour les changer).

## 4. Paramétrage dans les UI (5 min, une fois)

pgwatch et PoWA se configurent seuls. Il reste Databasus :

1. Créer le compte administrateur, puis un workspace (par exemple `Lychen MCO`).
2. **Notifiers** > Add notifier > Email : cible quelconque (`dba@mco.lychen.local`), SMTP
   `mailpit`, port `1025`, utilisateur et mot de passe quelconques. Dans Advanced settings,
   Connection security : **None (unencrypted)**. Le bouton « Send test notification » doit faire
   arriver un mail dans Mailpit.
3. **Add database** > PostgreSQL > Logical : hôte `espace-database`, port 5432, utilisateur
   `databasus` / `databasus`, base `app`, SSL désactivé. Quand Databasus propose de créer un
   utilisateur en lecture seule, passer (`databasus` l'est déjà). Ensuite : intervalle Hourly,
   stockage local, notifications succès et échec, notifier Mailpit. Faire de même pour
   `flora-database` et `tera-database` au besoin.

## 5. Scénarios de démo (validés sur espace)

### a. Supervision (pgwatch + Grafana)

Grafana > dashboard **« 2. Database Overview »**, source `espace` (TPS, QPS, hit ratio, débit WAL,
taille). « 1. Global Database Overview » montre les trois instances. Pour l'effet, une requête
longue :

```bash
docker exec espace-api-database-1 psql -U userlocal -d app -c "select pg_sleep(60)"
```

### b. Analyse de requêtes et index manquant (PoWA)

```bash
docker exec espace-api-database-1 psql -U userlocal -d app -c \
  "create table demo_of as select g as id, 'OF-' || g as code, g % 500 as tool_id from generate_series(1, 300000) g"
docker exec espace-api-database-1 sh -c \
  'for i in $(seq 1 300); do psql -U userlocal -d app -qAt -o /dev/null -c "select count(*) from demo_of where tool_id = $((i % 500))"; done'
```

Attendre deux instantanés (un par minute). Puis PoWA > serveur `espace` > base `app` > la requête
`select count(*) from demo_of where tool_id = $1` > onglet **Predicates** :

- Index Suggestions : `CREATE INDEX ON public.demo_of(tool_id)`, marquée « used » ;
- EXPLAIN sans index : `Parallel Seq Scan`, coût 5126.87. Avec l'index hypothétique HypoPG :
  `Index Only Scan`, coût 40.00 ;
- « Query cost gain factor with hypothetical index: 99.22 % ». Rien n'est créé sur la base.

PoWA calcule des écarts entre deux instantanés. Une charge qui tombe pendant que le collecteur se
(re)connecte à l'instance n'apparaît donc pas. Il suffit de la relancer.

### c. Traçabilité (pgaudit)

```bash
docker exec espace-api-database-1 psql -U userlocal -d app \
  -c "create role stagiaire login password 'x'" \
  -c "grant delete on all tables in schema public to stagiaire"

# audit des écritures sur une table sensible : il suffit de l'accorder au rôle mco_auditor
docker exec espace-api-database-1 psql -U userlocal -d app \
  -c "grant insert, update, delete on demo_of to mco_auditor" \
  -c "update demo_of set code = 'OF-X' where id = 1"

docker logs espace-api-database-1 2>&1 | grep AUDIT
```

Extrait réel :

```
… userlocal@app app=psql client=[local] LOG:  AUDIT: SESSION,1,1,ROLE,CREATE ROLE,,,create role stagiaire login password <REDACTED>,<not logged>
… userlocal@app app=psql client=[local] LOG:  AUDIT: SESSION,2,1,ROLE,GRANT,TABLE,,grant delete on all tables in schema public to stagiaire,<not logged>
… userlocal@app app=psql client=[local] LOG:  AUDIT: OBJECT,2,1,WRITE,UPDATE,TABLE,public.demo_of,update demo_of set code = 'OF-X' where id = 1,<not logged>
```

Les migrations Doctrine de l'API (DDL) sont tracées elles aussi.

### d. Sauvegarde logique et indisponibilité (Databasus)

- « Make backup right now », casser une donnée
  (`update demo_of set code = 'CASSE' where id <= 1000`), créer une base vide
  (`create database app_restore`), puis restaurer le backup vers `espace-database` / `app_restore`
  avec l'admin `userlocal`. Résultat : `app_restore` a ses 300 000 lignes intactes.
- `mco espace stop database` : le mail « ❌ [espace] DB is unavailable » arrive dans Mailpit après
  3 pings ratés (2 min 42 s mesurées). `mco espace start database` : « ✅ [espace] DB is online »
  arrive environ 1 min plus tard.

### e. Retour à l'instant T (pgBackRest)

```bash
docker exec espace-api-mco-pgbackrest-1 pgbackrest info
docker exec espace-api-database-1 psql -U userlocal -d app -Atc "select now()"     # noter l'heure H
docker exec espace-api-database-1 psql -U userlocal -d app -c "delete from demo_of where id <= 50000"
# attendre que le WAL soit archivé (archive_timeout = 60 s) :
docker exec espace-api-database-1 psql -U userlocal -d app -c "select last_archived_time from pg_stat_archiver"
mco espace stop mco-pgbackrest database
mco espace run --rm --no-deps --entrypoint pgbackrest mco-pgbackrest \
  --delta --type=time "--target=<H>" --target-action=promote restore
mco espace start database mco-pgbackrest
```

Mesuré : 300 000 lignes revenues, journal « recovery stopping before commit of transaction 901 »,
nouvelle timeline 2, reprise en 10 s. Le sidecar repart seul sur la nouvelle timeline : `check`,
puis un incrémental.

### f. Test de restauration automatique

Il tourne tout seul (`docker logs espace-api-mco-pgbackrest-1`). À la demande :

```bash
docker exec espace-api-mco-pgbackrest-1 sh /mco/restore-test.sh
```

Rapport réel (après le scénario e) :

```
== MCO RESTORE TEST [espace] 2026-11-12 07:04:24 +0000
-- backup restauré : incr backup: 20261112-064333F_20261112-070416I
-- données restaurées jusqu'à : fin du backup
-- bases :
   app 23 MB
   app_restore 23 MB
   postgres 7519 kB
   powa 14 MB
-- contrôle métier (app) : 13 migration(s) Doctrine
-- durées : restauration 1 s, rejeu WAL 0 s, contrôle 1 s, total 2 s
MCO RESTORE TEST [espace] : OK
```

Il prouve que la sauvegarde **et** la chaîne de WAL sont exploitables, que les données sont intègres
(`pg_amcheck` sur tables et index), et donne la fraîcheur des données restaurées ainsi que la durée
de reprise. Le contrôle métier par défaut compte les migrations Doctrine. On le change par API avec
`MCO_RESTORE_CHECK_SQL` dans `mco-pgbackrest`.

## 6. Ajouter une instance

Pour une nouvelle API `<d>` (même forme : service `database`, user `userlocal`, base `app`) :

1. Copier `projects/flora/api/compose.mco.yml` dans `projects/<d>/api/` et remplacer `flora`
   (stanza, alias `<d>-database`, `MCO_SERVER_ALIAS`, `PGHOST`). Si la `database` de l'API n'a
   pas de `networks:` dans ses composes, ajouter `default: {}` comme dans l'overlay d'espace.
2. Ajouter le tag `mco` dans `projects/<d>/api/moon.yml`.
3. Ajouter `<d>` à `MCO_PGWATCH_SOURCES` (`compose.yml`, service `mco-pgwatch`).
4. `moon run <d>-api:mco-up`. PoWA et pgBackRest se configurent seuls. Dans Databasus, déclarer
   la base dans l'UI.

Le dépôt pgBackRest, PoWA, pgwatch, Databasus et Mailpit restent uniques.

## 7. Écarts avec la stack de référence

| Changement | Pourquoi |
|---|---|
| Overlay par API qui **étend** `compose.instance.yml` ; scripts dans `instance/` (montés) au lieu de `configs.content` | trois instances : une seule source pour l'image, les scripts et les réglages ; chaque overlay ne garde que ce qui lui est propre |
| Réseau externe `lychen-mco`, alias `<domaine>-database` | `database` est ambigu entre les stacks ; les API n'y vont pas |
| Dépôt pgBackRest = volume externe `lychen-mco-pgbackrest-repo` (créé par `common-mco:create-network`) | commun aux trois instances, à l'abri d'un `down -v` |
| Mot de passe de chiffrement pgBackRest par `PGBACKREST_REPO1_CIPHER_PASS` | jamais dans un fichier |
| Restore test : instance jetable démarrée avec `shared_preload_libraries=pgaudit` (et `pgaudit.log=none`) au lieu de vide | les bases restaurées contiennent l'extension pgaudit, dont les triggers d'événement refusent tout DDL sans la bibliothèque : `pg_amcheck --install-missing` échouait sur `CREATE EXTENSION amcheck` |
| Premier backup après le schéma applicatif (`MCO_FIRST_BACKUP_AFTER_SQL`) | sur un volume neuf, le premier test de restauration tournait avant les migrations et échouait au contrôle métier |
| Contrôle métier par défaut : nombre de migrations Doctrine | commun aux trois API Symfony |
| `powa` reçoit `pg_read_all_data` | powa-web fait ses EXPLAIN HypoPG avec ce rôle : sans SELECT sur les tables, pas de suggestion d'index (« permission denied for table demo_of ») |
| Rechargement du collecteur PoWA par `NOTIFY powa_collector, 'RELOAD'` dans `register-powa.sql` | le collecteur ne lit les serveurs qu'au démarrage ; plus besoin de le redémarrer après chaque init |
| PoWA construit depuis PyPI (`powa-web==5.3.1`, `powa-collector==1.3.2`, psycopg2-binary) | images `powateam/*` en amd64 seulement |
| pgwatch-demo `6.1.0` : PostgreSQL interne forcé en 18 (`PATH`, `PGDATA`, `PG_MAJOR`) | l'image tourne sur PostgreSQL 19 beta mais n'installe timescaledb, plpython3u et pg_qualstats que pour la 18 : sa base interne ne démarre pas (vérifié aussi sur `v6.0.0`) |
| Sources pgwatch pré-enregistrées, preset `exhaustive_no_python` | pas de saisie dans l'UI ; les métriques Python demandent plpython3u sur les bases surveillées |
| Pas de `pull_policy: build` | un rebuild, même depuis le cache, donne un nouvel ID d'image (attestations BuildKit) : la base était recréée à chaque `up` |
| `mco-mail` supprimé, `common/mailpit` publie son UI (8025) | réutiliser le mailpit existant |
| Grafana sur 13000 | la tera-api annonce 3000 |
| Télémétrie Databasus coupée (`IS_DISABLE_ANONYMOUS_TELEMETRY`) | active par défaut |
| Versions épinglées : `postgres:16.15-trixie`, `pgwatch-demo:6.1.0`, `databasus:v3.68.0` | pas de `latest` |

## 8. Limites connues

- **pgwatch** garde métriques, sources et Grafana dans son conteneur (pas de volume) : un conteneur
  recréé repart d'un historique vide (les sources sont réenregistrées).
- **Dépôt pgBackRest** : si `database_data_mco` est supprimé mais pas le dépôt, `stanza-create`
  refuse la nouvelle instance (system-id différent). Supprimer la stanza
  (`pgbackrest --stanza=<d> stanza-delete --force`, base arrêtée) ou le volume du dépôt.
- **Fuseau** : conteneurs en UTC. Prendre H avec son décalage (`select now()`).
- **Fixtures** : non chargées, seules les migrations tournent (`moon <api>:load-fixtures` existe,
  mais n'a pas été testé en mode MCO).

## 9. Ce qui change en production

- une instance (ou quelques-unes) avec une base et un rôle par service plutôt qu'une instance par
  service ;
- secrets dans le coffre (chiffrement pgBackRest, rôles techniques, secret PoWA) ;
- logs pgaudit vers un stockage central non modifiable ;
- `pg_qualstats.sample_rate` par défaut et un instantané PoWA toutes les 5 min ;
- dépôt pgBackRest distant (S3 / Azure Blob) : full hebdo, diff quotidien, incrémental horaire, et
  test de restauration quotidien avec alerte ;
- `powa` en lecture sur les tables nécessaires plutôt que `pg_read_all_data`.
