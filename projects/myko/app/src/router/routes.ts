import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_MORE } from '@/views/more';
import { ROUTES_PROVISIONAL } from '@/views/provisional';

import { ROUTE_NAME } from './routeNames';

const routes: RouteRecordRaw[] = [
  {
    path: '/',
    component: () => import('@/layouts/main/LayoutMain.vue'),
    children: [ROUTE_HOME, ROUTE_MORE, ...ROUTES_PROVISIONAL],
  },
  {
    // What a printed label's QR code opens: `<app origin>/l/<log uuid>`. This path must never
    // change, labels are printed (and later engraved): it only forwards to the log's page.
    path: '/l/:uuid',
    name: ROUTE_NAME.LogQrEntry,
    redirect: (to) => ({ name: ROUTE_NAME.LogDetail, params: { uuid: to.params.uuid } }),
  },
  {
    path: '/:pathMatch(.*)*',
    redirect: { name: ROUTE_NAME.Home },
  },
];

export default routes;
