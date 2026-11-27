import type { RouteName } from '@/router/routeNames';

const titles: Partial<Record<RouteName, string>> = {
  logs: 'Logs',
  log_new: 'New log',
  log_new_batch: 'Batch creation',
  log_detail: 'Log details',
  log_edit: 'Edit log',
  zones: 'Zones',
  zone_new: 'New zone',
  zone_edit: 'Edit zone',
  strains: 'Strains',
  strain_new: 'New strain',
  strain_edit: 'Edit strain',
  labels: 'Labels',
  scan: 'Scan',
  stats: 'Statistics',
};

export default {
  titles,
  coming_soon: 'This screen is coming soon.',
};
