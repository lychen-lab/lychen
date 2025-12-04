export const ROUTE_APPLICATIONS = {
  path: '/applications',
  component: () => import('./View.vue'),
  name: 'applications',
} as const;
