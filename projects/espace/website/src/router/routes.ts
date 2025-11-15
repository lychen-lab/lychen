import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';

const routes: RouteRecordRaw[] = [
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
