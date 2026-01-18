export const ROUTE_FAVORITES = {
  path: '/favorites',
  component: () => import('./View.vue'),
  name: 'favorites',
} as const;
