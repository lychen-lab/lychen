import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MANIFEST } from '@/views/manifest';
import { ROUTE_SPONSOR } from '@/views/sponsor';
import { ROUTE_MISSION } from '@/views/mission';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/TheLayout.vue'),
    children: [ROUTE_HOME, ROUTE_MISSION, ROUTE_MANIFEST, ROUTE_SPONSOR],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
