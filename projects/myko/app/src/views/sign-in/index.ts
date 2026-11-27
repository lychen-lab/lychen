import { ROUTE_NAME } from '@/router/routeNames';

export const ROUTE_SIGN_IN = {
  path: '/sign-in',
  component: () => import('./View.vue'),
  name: ROUTE_NAME.SignIn,
} as const;
