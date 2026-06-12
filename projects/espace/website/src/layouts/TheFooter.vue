<template>
  <FooterMain
    :legal-menus
    :seo-paragraph="t('footer.seo_paragraph')"
    display-pronunciation
  >
    <span class="text-sm"
      >Découvrir l'association sur
      <a
        href="https://lychen.org"
        class="underline"
        >lychen.org</a
      ></span
    >
  </FooterMain>
</template>

<script lang="ts" setup>
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { ROUTE_TERMS_OF_USE } from '@lychen/vue-components-website/views/terms-of-use';
import { ROUTE_PRIVACY_POLICY } from '@lychen/vue-components-website/views/privacy-policy';

import FooterMain from '@lychen/vue-components-website/footer/FooterMain.vue';

const { t } = usePrefixedI18n(CONFIG);

const legalMenus = [
  { title: t('footer.legal_menus.terms_of_use'), to: { name: ROUTE_TERMS_OF_USE.name } },
  { title: t('footer.legal_menus.privacy_policy'), to: { name: ROUTE_PRIVACY_POLICY.name } },
];
</script>
