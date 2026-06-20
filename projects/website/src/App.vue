<template>
  <TooltipProvider>
    <RouterView />
  </TooltipProvider>
</template>

<script setup lang="ts">
import { usePreferredColorScheme } from '@lychen/vue-color-scheme/composables/usePreferredColorScheme';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  defineOrganization,
  defineWebPage,
  defineWebSite,
  useSchemaOrg,
} from '@unhead/schema-org/vue';
import { useHead, useSeoMeta } from '@unhead/vue';
import { defineAsyncComponent } from 'vue';
import ogImageDefault from '@/assets/og-default.webp';

const TooltipProvider = defineAsyncComponent(
  () => import('@lychen/vue-components-core/tooltip/TooltipProvider.vue'),
);

usePreferredColorScheme();

const { locale } = useI18nExtended();

useHead({
  titleTemplate: 'lychen | %s',
  htmlAttrs: {
    lang: () => locale.value,
  },
  templateParams: {
    schemaOrg: {
      host: `https://${import.meta.env.VITE_UNHEAD_HOST}`,
    },
  },
});

// Site-wide fallback Open Graph / Twitter image. Pages that pass their own
// `ogImage` to `useExtendedHead` override this; every other page (and any page
// added later) still embeds a branded thumbnail. Placeholder for now — see
// `docs/seo-og-images.md` for the per-page branded images roadmap.
useSeoMeta({
  ogImage: `https://${import.meta.env.VITE_UNHEAD_HOST}${ogImageDefault}`,
  twitterCard: 'summary_large_image',
});

useSchemaOrg([
  defineOrganization({
    name: 'lychen',
    logo: '/logos/lychen/logo-lychen.svg',
  }),
  defineWebSite({
    name: 'lychen',
  }),
  defineWebPage(),
]);
</script>
