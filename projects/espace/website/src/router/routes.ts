import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';

const routes: RouteRecordRaw[] = [
  {
    path: '',
    component: () => import('@/layouts/TheLayout.vue'),
    children: [ROUTE_HOME],
  },
];

export default routes;
