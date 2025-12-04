import '@lychen/css-core/all.css';

import { createApp } from 'vue';

import zitadelAuth from '@lychen/typescript-zitadel/ZitadelAuth';
import { createI18n } from '@lychen/vue-i18n/configs/createI18n';

import { VueQueryPlugin } from '@tanstack/vue-query';

import App from './App.vue';
import router from './router';

const app = createApp(App);

app.use(router);

const i18n = createI18n();
app.use(i18n);

declare module 'vue' {
  interface ComponentCustomProperties {
    $zitadel: typeof zitadelAuth;
  }
}
zitadelAuth.oidcAuth.startup().then((ok) => {
  if (ok) {
    app.config.globalProperties.$zitadel = zitadelAuth;
  }
});

app.use(VueQueryPlugin);

app.mount('#app');
