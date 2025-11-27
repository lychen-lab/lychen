import type { RouteRecordRaw } from 'vue-router';
import { ROUTE_HOME } from '@/views/home';

const routes: RouteRecordRaw[] = [
  {
    path: '',
    redirect: ROUTE_HOME,
  },
  {
    path: '/',
    children: [ROUTE_HOME],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
