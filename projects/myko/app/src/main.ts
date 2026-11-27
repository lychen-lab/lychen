import '@lychen/css-core/all.css';

import { createApp } from 'vue';

import { createI18n } from '@lychen/vue-i18n/configs/createI18n';

import { VueQueryPlugin } from '@tanstack/vue-query';

import App from './App.vue';
import { AUTH_KEY, createAuth } from './auth';
import { createAppRouter } from './router';

async function bootstrap(): Promise<void> {
  const app = createApp(App);

  const auth = await createAuth();
  const router = createAppRouter(auth);
  app.provide(AUTH_KEY, auth);
  await auth.startup(router);

  app.use(router);
  app.use(createI18n());
  app.use(VueQueryPlugin);

  app.mount('#app');
}

void bootstrap();
