import { createRouter, createWebHistory } from 'vue-router';

import type { Auth } from '@/auth';

import { createRoutes } from './routes';

export function createAppRouter(auth: Auth) {
  return createRouter({
    history: createWebHistory(import.meta.env.BASE_URL),
    routes: createRoutes(auth),
  });
}
