export const ROUTE_MANIFEST = {
  path: '/manifest',
  component: () => import('./PageManifest.vue'),
  name: 'manifest',
} as const;
