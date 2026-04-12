import type { RouteRecordRaw } from 'vue-router';
import { ROUTE_CULTURES } from '@/views/cultures';
import { ROUTE_CULTURES_DETAIL } from '@/views/culture-detail';

const routes: RouteRecordRaw[] = [
  {
    path: '',
    redirect: ROUTE_CULTURES,
  },
  {
    path: '/',
    component: () => import('@/layouts/main/LayoutMain.vue'),
    children: [ROUTE_CULTURES, ROUTE_CULTURES_DETAIL],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_CULTURES,
  },
];

export default routes;
