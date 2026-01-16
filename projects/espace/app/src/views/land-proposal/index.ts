export const ROUTE_LAND_PROPOSAL = {
  path: '/land-proposal/:uuid',
  component: () => import('./View.vue'),
  name: 'land-proposal',
} as const;
