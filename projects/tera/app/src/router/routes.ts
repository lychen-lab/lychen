import type { RouteRecordRaw } from 'vue-router';

import zitadelAuth from '@lychen/vue-zitadel/ZitadelAuth';
import { RoutePageDashboard } from '@/pages/dashboard';
import { RoutePageOnboarding } from '@/pages/onboarding';
import { LAND_ROUTE_PATH } from '@/pages/land';
import { RoutePageFoodSafety } from '@/pages/food-safety';
import { RoutePageGreeningPermit } from '@/pages/greening-permit';
import { RoutePageCalendars } from '@/pages/calendars';
import { RoutePageCultureItinaries } from '@/pages/culture-itinaries';
import { NODE_PATH } from '@/pages/co-gardening';
import { RoutePageCoGardening } from '@/pages/co-gardening/dashboard';
import { RoutePageDiary } from '@/pages/diary';
import { RoutePageData } from '@/pages/data';
import { RoutePageGrainLibrary } from '@/pages/grain-library';
import { RoutePageHerbarium } from '@/pages/herbarium';
import { RoutePageSharedGardens } from '@/pages/shared-gardens';
import { RoutePageTasks } from '@/pages/tasks';
import { RoutePageLands } from '@/pages/lands';
import { RoutePageLandTasks } from '@/pages/land/tasks';
import { RoutePageLandDashboard } from '@/pages/land/dashboard';
import { RoutePageLandDiary } from '@/pages/land/diary';
import { RoutePageLandCultureItinaries } from '@/pages/land/culture-itinaries';
import { RoutePageLandCalendars } from '@/pages/land/calendars';
import { RoutePageLandSettings } from '@/pages/land/settings';
import { RoutePageLandMemberSettings } from '@/pages/land/member-settings';
import { RoutePageCoGardeningRequests } from '@/pages/co-gardening/requests';
import { RoutePageCoGardeningProposals } from '@/pages/co-gardening/proposals';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('../layouts/main/TheLayout.vue'),
    meta: {
      authName: zitadelAuth.oidcAuth.authName,
    },
    redirect: RoutePageLands,
    children: [
      RoutePageDashboard,
      {
        path: LAND_ROUTE_PATH,
        component: () => import('@/layouts/land-layout/LandLayout.vue'),
        children: [
          RoutePageLandTasks,
          RoutePageLandDashboard,
          RoutePageLandDiary,
          RoutePageLandCultureItinaries,
          RoutePageLandCalendars,
          RoutePageLandSettings,
          RoutePageLandMemberSettings,
        ],
      },
      RoutePageFoodSafety,
      RoutePageGreeningPermit,
      RoutePageCalendars,
      RoutePageCultureItinaries,
      {
        path: NODE_PATH,
        children: [
          RoutePageCoGardening,
          RoutePageCoGardeningRequests,
          RoutePageCoGardeningProposals,
        ],
      },
      RoutePageDiary,
      RoutePageData,
      RoutePageGrainLibrary,
      RoutePageHerbarium,
      RoutePageSharedGardens,
      RoutePageTasks,
      RoutePageLands,
    ],
  },
  RoutePageOnboarding,
  {
    path: '/:pathMatch(.*)*',
    redirect: RoutePageDashboard,
  },
];

export default routes;
