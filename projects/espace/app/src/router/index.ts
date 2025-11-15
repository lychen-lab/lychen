import { createRouter, createWebHistory } from 'vue-router';

import zitadelAuth from '@lychen/typescript-zitadel/ZitadelAuth';

import routes from './routes';

const router = createRouter({ history: createWebHistory(import.meta.env.BASE_URL), routes });

zitadelAuth.oidcAuth.useRouter(router);

export default router;
