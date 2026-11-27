import type { RouteName } from '@/router/routeNames';

const titles: Partial<Record<RouteName, string>> = {
  logs: 'Bûches',
  log_new: 'Nouvelle bûche',
  log_new_batch: 'Création en série',
  log_detail: 'Fiche bûche',
  log_edit: 'Modifier la bûche',
  zones: 'Zones',
  zone_new: 'Nouvelle zone',
  zone_edit: 'Modifier la zone',
  strains: 'Souches',
  strain_new: 'Nouvelle souche',
  strain_edit: 'Modifier la souche',
  labels: 'Étiquettes',
  scan: 'Scanner',
  stats: 'Statistiques',
};

export default {
  titles,
  coming_soon: 'Cet écran arrive bientôt.',
};
