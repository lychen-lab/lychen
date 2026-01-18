import type { RouteRecordRaw } from 'vue-router';
import { ROUTE_HOME } from '@/views/home';
import { ROUTE_LAND_PROPOSALS } from '@/views/land-proposals';
import { ROUTE_LAND_REQUESTS } from '@/views/land-requests';
import { ROUTE_FAVORITES } from '@/views/favorites';
import { ROUTE_CHATS } from '@/views/chats';
import { ROUTE_LAND_PROPOSAL } from '@/views/land-proposal';
import { ROUTE_LAND_REQUEST } from '@/views/land-request';
import { ROUTE_CHAT } from '@/views/chat';

const routes: RouteRecordRaw[] = [
  {
    path: '',

    redirect: ROUTE_HOME,
  },
  {
    path: '/',
    component: () => import('@/layouts/main/LayoutMain.vue'),
    children: [
      ROUTE_HOME,
      ROUTE_LAND_PROPOSALS,
      ROUTE_LAND_REQUESTS,
      ROUTE_FAVORITES,
      ROUTE_CHATS,
      ROUTE_LAND_PROPOSAL,
      ROUTE_LAND_REQUEST,
      ROUTE_CHAT,
    ],
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
