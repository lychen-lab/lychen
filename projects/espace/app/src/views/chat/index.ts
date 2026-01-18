export const ROUTE_CHAT = {
  path: '/chat/:uuid',
  component: () => import('./View.vue'),
  name: 'chat',
} as const;
