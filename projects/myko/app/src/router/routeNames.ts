/** Every route of the app, by name (paths: `projects/myko/CLAUDE.md`, "App conventions"). */
export const ROUTE_NAME = {
  Home: 'home',
  Logs: 'logs',
  LogNew: 'log_new',
  LogNewBatch: 'log_new_batch',
  LogDetail: 'log_detail',
  LogEdit: 'log_edit',
  LogQrEntry: 'log_qr_entry',
  Zones: 'zones',
  ZoneNew: 'zone_new',
  ZoneEdit: 'zone_edit',
  Strains: 'strains',
  StrainNew: 'strain_new',
  StrainEdit: 'strain_edit',
  Labels: 'labels',
  Scan: 'scan',
  Stats: 'stats',
  More: 'more',
} as const;

export type RouteName = (typeof ROUTE_NAME)[keyof typeof ROUTE_NAME];
