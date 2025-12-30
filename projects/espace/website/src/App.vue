<template>
  <RouterView />
</template>

<script setup lang="ts">
import { usePreferredColorScheme } from '@lychen/vue-color-scheme/composables/usePreferredColorScheme';
import { defineOrganization, defineWebPage, defineWebSite } from '@unhead/schema-org';
import { useHead } from '@unhead/vue';

usePreferredColorScheme();
const title = 'espace';
useHead({
  titleTemplate: `${title} | %s`,
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
