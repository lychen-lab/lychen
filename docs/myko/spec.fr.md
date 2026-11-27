# Myko · spécification fonctionnelle v0

Myko est l'application Lychen de suivi des cultures de champignons. La v0 couvre la culture sur
bûches. Ce document dit **ce que** l'application fait ; `projects/myko/CLAUDE.md` dit **comment**
elle est construite.

## Objectif

Pour chaque bûche, savoir quand et avec quelle souche elle a été inoculée, tenir son journal
(notes, observations, interventions) et peser ses récoltes. Puis voir ce que chaque souche, chaque
essence de bois et chaque zone a produit.

Usage principal : sur téléphone, au jardin ou en sous-bois, en scannant le QR code collé sur la
bûche. L'application est une PWA installable, **en ligne** en v0 (le hors ligne viendra ensuite).

## Utilisateur v0

Une personne, connectée avec son compte Lychen (Zitadel). Elle possède une ou plusieurs zones,
chacune avec une ou plusieurs bûches. Pas de membres ni de partage en v0 : personne d'autre ne voit
ses données.

## Concepts

**Zone.** Un lieu de culture, pas forcément en extérieur : sous-bois, cave, serre, cour. Un nom et
une description.

**Bûche.** Toujours dans une zone. Une étiquette lisible (par exemple `B-012`, unique pour la
personne), l'essence du bois, la date d'abattage, le diamètre et la longueur en centimètres, des
notes. Un état (voir le cycle de vie). Un QR code qui ouvre sa fiche.

**Espèce de champignon.** Référentiel commun en lecture seule : shiitake, pleurote en huître,
pholiote du peuplier, hydne hérisson, strophaire, nameko, collybie à pied velouté, reishi, etc.

**Souche.** Le catalogue personnel : une espèce, le nom ou la référence de la souche (par exemple
`M3782`), le fournisseur, des notes.

**Essence de bois.** Référentiel commun en lecture seule : chêne, hêtre, charme, châtaignier,
bouleau, peuplier, érable, aulne, noisetier, frêne, saule, merisier, etc.

**Inoculation.** Une seule par bûche en v0. La date, la souche, la méthode (chevilles, sciure,
autre), la quantité (par exemple le nombre de chevilles), des notes. Une date passée est permise :
on saisit aussi des bûches inoculées il y a des mois.

**Entrée de journal.** Une date et heure, un type et un texte. Types : note, observation, trempage,
arrosage, déplacement, traitement, autre. Les changements d'état de la bûche s'inscrivent
automatiquement au journal (type « changement d'état », non modifiable).

**Récolte.** Une date, un poids en grammes, des notes.

## Cycle de vie d'une bûche

```
préparée ──inoculation──▶ en incubation ──première récolte ou action manuelle──▶ en production
    │                          │                                                    │
    └──────────────────────────┴───────────────── retrait ──────────────────────────┴──▶ retirée
```

- Une bûche est créée « préparée ».
- Enregistrer son inoculation la passe « en incubation ». Supprimer l'inoculation d'une bûche en
  incubation la remet « préparée » ; impossible une fois en production.
- Sa première récolte, ou l'action « elle fructifie », la passe « en production ».
- Elle peut être retirée depuis tout état (épuisée, cassée, donnée). Pas de retour en arrière en v0.
- On ne peut pas peser de récolte sur une bûche préparée ou retirée.

## Écrans

Mobile d'abord (390 px de large), utilisables à une main. Navigation du bas : Accueil, Bûches,
Scanner, Stats, Plus (zones, souches, étiquettes).

1. **Accueil.** Nombre de bûches par état, total récolté sur la saison, dernières récoltes,
   dernières entrées de journal, raccourcis « scanner » et « nouvelle bûche ». Un premier lancement
   sans zone guide vers la création de la première zone.
2. **Zones.** Liste avec le nombre de bûches, création, édition, suppression (refusée si la zone
   contient des bûches).
3. **Bûches.** Liste filtrable par zone, état, espèce et essence, recherche par étiquette, tri par
   étiquette. Sélection multiple pour la saisie groupée et l'impression d'étiquettes.
4. **Nouvelle bûche** et **création en série** : « créer B-001 à B-040 dans la zone Sous-bois, en
   chêne, inoculées le 12 mars avec la souche M3782 ». C'est l'écran qui permet de saisir les
   bûches existantes en quelques minutes.
5. **Fiche bûche.** Infos, état, inoculation, total récolté, chronologie (inoculation, journal,
   récoltes, changements d'état, du plus récent au plus ancien), actions rapides.
6. **Saisies rapides** depuis la fiche, en feuille qui monte du bas : ajouter une entrée de
   journal, peser une récolte, inoculer, « elle fructifie », retirer. Trois touches au plus pour
   une saisie courante.
7. **Saisie groupée** depuis la liste : la même entrée de journal (par exemple un trempage) ou la
   même inoculation sur toutes les bûches sélectionnées.
8. **Souches.** Catalogue personnel : liste, création, édition.
9. **Étiquettes.** Impression d'une planche A4 de QR codes avec l'étiquette de chaque bûche
   sélectionnée. Le QR code contient l'adresse `<adresse de l'app>/l/<uuid de la bûche>`, qui ne
   changera jamais (les étiquettes seront un jour gravées sur acier).
10. **Scanner.** Lecture d'un QR code avec la caméra, ouverture de la fiche. L'appareil photo du
    téléphone ouvre aussi la fiche directement, sans passer par l'app.
11. **Statistiques.** Poids récolté et nombre de récoltes par bûche, souche, espèce, essence, zone
    et mois, sur une période choisie (par défaut la saison en cours).

## Données de démo

Deux zones, quarante bûches de plusieurs essences, quatre souches de trois espèces, des entrées de
journal et deux saisons de récoltes. Elles servent au développement local et aux captures, jamais
en production.

## Hors périmètre v0

À venir, dans cet ordre probable : hors ligne avec synchronisation, photos, export CSV, totems (une
section de totem devient une bûche dont l'inoculation a été faite en totem), membres et permissions
par zone, ballots et autres substrats, connecteurs vers les autres produits Lychen.

## Définition de « v0 terminée »

Depuis son téléphone, Nathan peut :

1. créer ses zones et ses souches ;
2. saisir ses quarante bûches existantes en série, avec leur inoculation ;
3. imprimer leurs étiquettes QR ;
4. scanner une bûche et ouvrir sa fiche ;
5. noter un trempage sur dix bûches d'un coup ;
6. peser une récolte en trois touches ;
7. voir ce que chaque souche et chaque essence a produit cette saison.
