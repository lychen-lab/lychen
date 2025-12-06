<template>
  <RouterView />
</template>

<script setup lang="ts">
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { usePreferredColorScheme } from '@lychen/vue-color-scheme/composables/usePreferredColorScheme';
import { defineOrganization, defineWebPage, defineWebSite } from '@unhead/schema-org';
import { useHead } from '@unhead/vue';
import { CONFIG } from '@lychen/vue-espace/i18n';

const { t } = usePrefixedI18n(CONFIG);

usePreferredColorScheme();

useHead({
  titleTemplate: `${t('name')} | %s`,
  templateParams: {
    schemaOrg: {
      host: import.meta.env.VITE_UNHEAD_HOST,
    },
  },
});

defineOrganization({
  name: t('name'),
  logo: '/logos/lychen/logo-lychen.svg',
});
defineWebSite({
  name: t('name'),
});
defineWebPage();
</script>
