<template>
  <TooltipProvider>
    <RouterView />
  </TooltipProvider>
</template>

<script setup lang="ts">
import { usePreferredColorScheme } from '@lychen/vue-color-scheme/composables/usePreferredColorScheme';
import { TooltipProvider } from '@lychen/vue-components-core/tooltip';
import {
  defineOrganization,
  defineWebPage,
  defineWebSite,
  useSchemaOrg,
} from '@unhead/schema-org/vue';
import { useHead, useSeoMeta } from '@unhead/vue';
import ogImageDefault from '@/views/home/assets/hero-market-garden.webp';
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
      host: `https://${import.meta.env.VITE_UNHEAD_HOST}`,
    },
  },
});

// Site-wide Open Graph / Twitter image: the pages that pass their own `ogImage`
// to `useExtendedHead` override it, the others (legal pages) still get one, as
// their `summary_large_image` card requires.
useSeoMeta({
  ogSiteName: title,
  ogImage: `https://${import.meta.env.VITE_UNHEAD_HOST}${ogImageDefault}`,
});

useSchemaOrg([
  defineOrganization({
    name: title,
    logo: '/logos/lychen/logo-lychen.svg',
  }),
  defineWebSite({
    name: title,
  }),
  defineWebPage(),
]);
</script>
