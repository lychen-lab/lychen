import { ROUTE_NAME } from '@/router/routeNames';

export const ROUTE_MORE = {
  path: '/more',
  component: () => import('./View.vue'),
  name: ROUTE_NAME.More,
} as const;
