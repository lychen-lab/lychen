<template>
  <PageHomeHero />
  <!--<SectionCrowdfunding />
  <PageHomeApplications />
  <PageHomeUseCases />
  <PageHomeBento />
  <PageHomeODD />
  <PageHomeOSS />
  <PageHomeActors />-->
</template>

<script lang="ts" setup>
import ogImageUrl from './assets/HomeOgImage.webp';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import PageHomeHero from './PageHomeHero.vue';

const { t } = usePrefixedI18n(CONFIG);

useExtendedHead(t, { ogImage: ogImageUrl });
</script>
