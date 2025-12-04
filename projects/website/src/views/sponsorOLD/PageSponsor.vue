<template>
  <PageSponsorHero />
  <PageSponsorNeeds />
  <PageSponsorHowWeUseFunds />
  <PageSponsorWaysToSupport />
</template>

<script lang="ts" setup>
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { defineAsyncComponent } from 'vue';

const PageSponsorNeeds = defineAsyncComponent(() => import('./PageSponsorNeeds.vue'));

const PageSponsorWaysToSupport = defineAsyncComponent(
  () => import('./PageSponsorWaysToSupport.vue'),
);
const PageSponsorHowWeUseFunds = defineAsyncComponent(
  () => import('./PageSponsorHowWeUseFunds.vue'),
);

const PageSponsorHero = defineAsyncComponent(() => import('./PageSponsorHero.vue'));
const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

useExtendedHead(t);
</script>

<style lang="css" scoped></style>
