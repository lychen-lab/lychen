<template>
  <PageManifestHero />
  <PageManifestContent />
</template>

<script lang="ts" setup>
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { defineAsyncComponent } from 'vue';
import { messages, TRANSLATION_KEY } from './i18n';

const PageManifestHero = defineAsyncComponent(() => import('./PageManifestHero.vue'));
const PageManifestContent = defineAsyncComponent(() => import('./PageManifestContent.vue'));

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

useExtendedHead(t);
</script>

<style lang="css" scoped></style>
