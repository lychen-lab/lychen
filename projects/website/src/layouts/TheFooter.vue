<template>
  <FooterMain
    :legal-menus
    :seo-paragraph="t('footer.seo_paragraph')"
    display-pronunciation
  />
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { ROUTE_TERMS_OF_USE } from '@/views/terms-of-use';
import { ROUTE_PRIVACY_POLICY } from '@/views/privacy-policy';

const FooterMain = defineAsyncComponent(
  () => import('@lychen/vue-components-website/footer/FooterMain.vue'),
);

const legalMenus = [
  { title: "Conditions générales d'utilisation", to: { name: ROUTE_TERMS_OF_USE.name } },
  { title: 'Protection des données', to: { name: ROUTE_PRIVACY_POLICY.name } },
];

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
