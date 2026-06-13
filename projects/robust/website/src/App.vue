<template>
  <RouterView />
</template>

<script setup lang="ts">
import { usePreferredColorScheme } from '@lychen/vue-color-scheme/composables/usePreferredColorScheme';
import {
  defineOrganization,
  defineWebPage,
  defineWebSite,
  useSchemaOrg,
} from '@unhead/schema-org/vue';
import { useHead } from '@unhead/vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

const title = 'robust';

const { locale } = useI18nExtended();

usePreferredColorScheme();

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
