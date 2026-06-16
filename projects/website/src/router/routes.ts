import { type RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MISSION } from '@/views/mission';
import { ROUTE_CHARTER } from '@/views/charter';
import { ROUTE_TEAM } from '@/views/team';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_CAREER } from '@/views/career';
import { ROUTE_LABEL } from '@/views/label';
import { ROUTE_PRIVACY_POLICY } from '@lychen/vue-components-website/views/privacy-policy';

const routes: RouteRecordRaw[] = [
  {
    path: '',
    component: () => import('@/layouts/TheLayout.vue'),
    children: [
      ROUTE_HOME,
      ROUTE_MISSION,
      ROUTE_CHARTER,
      ROUTE_TEAM,
      ROUTE_PRIVACY_POLICY,
      ROUTE_APPLICATIONS,
      ROUTE_PARTNERSHIPS,
      ROUTE_CAREER,
      ROUTE_LABEL,
    ],
  },
];

export default routes;
