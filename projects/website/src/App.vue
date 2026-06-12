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
import { useHead } from '@unhead/vue';
import { defineAsyncComponent } from 'vue';

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
