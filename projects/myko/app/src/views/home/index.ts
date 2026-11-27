import { ROUTE_NAME } from '@/router/routeNames';

export const ROUTE_HOME = {
  path: '',
  component: () => import('./View.vue'),
  name: ROUTE_NAME.Home,
} as const;
