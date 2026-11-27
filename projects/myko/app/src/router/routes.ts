import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/main/LayoutMain.vue'),
    children: [ROUTE_HOME],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: { name: ROUTE_HOME.name },
  },
];

export default routes;
