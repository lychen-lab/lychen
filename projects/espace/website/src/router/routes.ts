import type { RouteRecordRaw } from 'vue-router';

import { ROUTE_HOME } from '@/views/home';
import { ROUTE_PRIVACY_POLICY } from '@lychen/vue-components-website/views/privacy-policy';
import { ROUTE_TERMS_OF_USE } from '@lychen/vue-components-website/views/terms-of-use';

const routes: RouteRecordRaw[] = [
  {
    path: '',
    component: () => import('@/layouts/TheLayout.vue'),
    children: [ROUTE_HOME, ROUTE_PRIVACY_POLICY, ROUTE_TERMS_OF_USE],
  },
];

export default routes;
