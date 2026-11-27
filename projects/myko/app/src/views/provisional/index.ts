import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_NAME } from '@/router/routeNames';

/**
 * The screens that are not built yet, each with the path `projects/myko/CLAUDE.md` gives it. A
 * ticket that builds a screen moves its route out of this list, into its own `views/<name>/`.
 */
export const PROVISIONAL_ROUTES = [
  { path: '/logs', name: ROUTE_NAME.Logs },
  { path: '/logs/new', name: ROUTE_NAME.LogNew },
  { path: '/logs/new-batch', name: ROUTE_NAME.LogNewBatch },
  { path: '/logs/:uuid', name: ROUTE_NAME.LogDetail },
  { path: '/logs/:uuid/edit', name: ROUTE_NAME.LogEdit },
  { path: '/zones', name: ROUTE_NAME.Zones },
  { path: '/zones/new', name: ROUTE_NAME.ZoneNew },
  { path: '/zones/:uuid/edit', name: ROUTE_NAME.ZoneEdit },
  { path: '/strains', name: ROUTE_NAME.Strains },
  { path: '/strains/new', name: ROUTE_NAME.StrainNew },
  { path: '/strains/:uuid/edit', name: ROUTE_NAME.StrainEdit },
  { path: '/labels', name: ROUTE_NAME.Labels },
  { path: '/scan', name: ROUTE_NAME.Scan },
  { path: '/stats', name: ROUTE_NAME.Stats },
] as const;

export const ROUTES_PROVISIONAL: RouteRecordRaw[] = PROVISIONAL_ROUTES.map((route) => ({
  ...route,
  component: () => import('./View.vue'),
}));
