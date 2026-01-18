export const ROUTE_CHATS = {
  path: '/chats',
  component: () => import('./View.vue'),
  name: 'chats',
} as const;
