<template>
  <FooterMain
    :legal-menus
    :seo-paragraph="t('footer.seo_paragraph')"
    display-pronunciation
  />
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { ROUTE_PRIVACY_POLICY } from '@lychen/vue-components-website/views/privacy-policy';

const FooterMain = defineAsyncComponent(
  () => import('@lychen/vue-components-website/footer/FooterMain.vue'),
);

const legalMenus = [{ title: 'Protection des données', to: { name: ROUTE_PRIVACY_POLICY.name } }];

const { t } = usePrefixedI18n(CONFIG);
</script>
