import type { RouteRecordRaw } from 'vue-router';

export const ROUTE_CULTURES_DETAIL: RouteRecordRaw = {
  path: '/cultures/:id',
  name: 'cultureDetail',
  component: () => import('./CultureDetailView.vue'),
};
