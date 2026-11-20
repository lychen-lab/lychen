export default {
  navigation: {
    home: 'Accueil',
    proposals: 'Terrains',
    requests: 'Recherches',
    favorites: 'Favoris',
  },
  // Les codes sont ceux de la ressource AreaActivity de l'API.
  activities: {
    general_maintenance: 'Entretien général',
    gardening: 'Jardinage',
    beehives: 'Ruches',
    vegetable_sharing: 'Partage de légumes',
    fruit_sharing: 'Partage de fruits',
    flower_planting: 'Plantation de fleurs',
    tree_planting: "Plantation d'arbres",
  },
  state: {
    loading: 'Chargement…',
    error: 'Impossible de charger ces informations.',
    retry: 'Réessayer',
  },
  filters: {
    city: 'Rechercher par ville',
    activity: 'Activité',
    all_activities: 'Toutes les activités',
    order: 'Tri',
    newest_first: "Les plus récentes d'abord",
    oldest_first: "Les plus anciennes d'abord",
    reset: 'Effacer les filtres',
  },
  pagination: {
    label: 'Pagination',
    previous: 'Page précédente',
    next: 'Page suivante',
    position: 'Page {page} sur {total}',
  },
  favorite: {
    add: 'Ajouter aux favoris',
    remove: 'Retirer des favoris',
  },
  share: {
    action: 'Partager',
    copied: 'Lien copié dans le presse-papiers.',
    failed: 'Impossible de partager ou de copier le lien.',
  },
  back: 'Retour',
  image_alt: "Illustration générique : aucune photo n'est disponible",
};
