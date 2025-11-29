import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MISSION } from '@/views/mission';
import { ROUTE_CHARTER } from '@/views/charter';
import { ROUTE_TEAM } from '@/views/team';
import { ROUTE_PRIVACY } from '@/views/privacy';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/TheLayout.vue'),
    children: [ROUTE_HOME, ROUTE_MISSION, ROUTE_CHARTER, ROUTE_TEAM, ROUTE_PRIVACY],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
