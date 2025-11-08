<template>
  <Page
    :title="t('title')"
    :description="t('description')"
  >
    <div
      class="text-on-warning bg-warning text-on-negative bg-negative text-on-positive bg-positive bg-surface-container bg-surface-container-low bg-surface-container-lowest bg-surface-container-high bg-surface-container-highest hidden"
    ></div>
    <div class="flex flex-col gap-8 p-8">
      <section
        v-for="theme in themes"
        :key="theme.value"
        class="bg-surface text-on-surface border-on-surface/30 flex flex-col items-stretch gap-4 rounded-2xl border-1 p-8"
        :data-theme="theme.value"
      >
        <BaseHeading variant="h2">{{ theme.label }}</BaseHeading>
        <div class="grid grid-cols-3 gap-4">
          <div
            v-for="feedbackColor in feedbackColorVariants"
            :key="feedbackColor"
            :class="`bg-${feedbackColor} rounded-xl h-40 p-4 text-on-${feedbackColor}`"
          >
            <h2>{{ feedbackColor }}</h2>
          </div>
        </div>
        <div class="bg-surface border-on-surface/20 h-40 rounded-xl border-1 p-4">
          <h2>surface</h2>
        </div>
        <div :class="`grid grid-cols-5 gap-4`">
          <div
            v-for="variant in surfaceContainerVariants"
            :key="variant"
            :class="`bg-${variant}`"
            class="h-40 rounded-xl p-4"
          >
            <h2>{{ variant }}</h2>
          </div>
        </div>
      </section>
    </div>
  </Page>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { messages, TRANSLATION_KEY } from './i18n';
import { defineAsyncComponent } from 'vue';

const Page = defineAsyncComponent(() => import('@/components/Page.vue'));
const BaseHeading = defineAsyncComponent(
  () => import('@lychen/vue-components-app/base-heading/BaseHeading.vue'),
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const surfaceContainerVariants = [
  'surface-container-lowest',
  'surface-container-low',
  'surface-container',
  'surface-container-high',
  'surface-container-highest',
];

const themes = [
  {
    label: 'Light theme',
    value: 'light',
  },
  {
    label: 'Dark theme',
    value: 'dark',
  },
];

const feedbackColorVariants = ['positive', 'negative', 'warning'];
</script>

<style lang="css" scoped></style>
