import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MISSION } from '@/views/mission';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/TheLayout.vue'),
    children: [ROUTE_HOME, ROUTE_MISSION],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
