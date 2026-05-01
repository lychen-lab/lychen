<template>
  <TooltipProvider>
    <RouterView />
  </TooltipProvider>
</template>

<script setup lang="ts">
import { usePreferredColorScheme } from '@lychen/vue-color-scheme/composables/usePreferredColorScheme';
import { TooltipProvider } from '@lychen/vue-components-core/tooltip';
import { defineOrganization, defineWebPage, defineWebSite } from '@unhead/schema-org';
import { useHead } from '@unhead/vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

usePreferredColorScheme();
const { locale } = useI18nExtended();
const title = 'espace';
useHead({
  titleTemplate: `${title} | %s`,
  htmlAttrs: {
    lang: () => locale.value,
  },
  templateParams: {
    schemaOrg: {
      host: import.meta.env.VITE_UNHEAD_HOST,
    },
  },
});

defineOrganization({
  name: title,
  logo: '/logos/lychen/logo-lychen.svg',
});
defineWebSite({
  name: title,
});
defineWebPage();
</script>
