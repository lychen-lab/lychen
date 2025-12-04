import { RouterView, type RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MISSION } from '@/views/mission';
import { ROUTE_CHARTER } from '@/views/charter';
import { ROUTE_TEAM } from '@/views/team';
import { ROUTE_PRIVACY_POLICY } from '@/views/privacy-policy';
import { ROUTE_TERMS_OF_USE } from '@/views/terms-of-use';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_CAREER } from '@/views/career';

const routes: RouteRecordRaw[] = [
  {
    path: '/:locale?',
    component: RouterView,
    beforeEnter: useTrans().routeMiddleware,
    children: [
      {
        path: '/',
        component: () => import('@/layouts/TheLayout.vue'),
        children: [
          ROUTE_HOME,
          ROUTE_MISSION,
          ROUTE_CHARTER,
          ROUTE_TEAM,
          ROUTE_PRIVACY_POLICY,
          ROUTE_APPLICATIONS,
          ROUTE_TERMS_OF_USE,
          ROUTE_PARTNERSHIPS,
          ROUTE_CAREER,
        ],
      },
    ],
  },

  {
    path: '/:pathMatch(.*)*',
    redirect: ROUTE_HOME,
  },
];

export default routes;
