export const ROUTE_LAND_REQUEST = {
  path: '/land-request/:uuid',
  component: () => import('./View.vue'),
  name: 'land-request',
} as const;
