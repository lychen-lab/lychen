export const ROUTE_HOME = {
  path: '/home',
  component: () => import('./ViewHome.vue'),
  name: 'home',
} as const;
