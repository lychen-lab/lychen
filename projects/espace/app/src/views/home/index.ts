export const ROUTE_HOME = {
  path: '/home',
  component: () => import('./View.vue'),
  name: 'home',
} as const;
