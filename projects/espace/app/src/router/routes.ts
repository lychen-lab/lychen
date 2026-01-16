import type { RouteRecordRaw } from 'vue-router';
import { ROUTE_HOME } from '@/views/home';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { ROUTE_LAND_REQUESTS } from '@/views/land-requests';
import { ROUTE_FAVORITES } from '@/views/favorites';
import { ROUTE_CHATS } from '@/views/chats';

const routes: RouteRecordRaw[] = [
  {
    path: '',

    redirect: ROUTE_HOME,
  },
  {
    path: '/',
    component: () => import('@/layouts/main/LayoutMain.vue'),
    children: [ROUTE_HOME, ROUTE_LAND_PROPOSALS, ROUTE_LAND_REQUESTS, ROUTE_FAVORITES, ROUTE_CHATS],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
