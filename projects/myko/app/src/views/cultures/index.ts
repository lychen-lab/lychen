import type { RouteRecordRaw } from 'vue-router';

export const ROUTE_CULTURES: RouteRecordRaw = {
  path: '/cultures',
  name: 'cultures',
  component: () => import('./CulturesListView.vue'),
};
