<template>
  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h1">{{ t('title') }}</Title>
    <Paragraph>{{ t('description') }}</Paragraph>
  </Container>

  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h2">{{ t('goals.title') }}</Title>
    <Paragraph>{{ t('goals.description') }}</Paragraph>
    <div class="grid grid-cols-2 grid-rows-2 items-stretch gap-8">
      <div
        class="bg-surface-variant row-span-2 flex flex-col items-center justify-center gap-4 rounded-2xl p-8"
      >
        <!-- Placeholder for Image if needed, or remove img tag if no source -->
        <div class="bg-surface-2 h-48 w-full rounded-xl opacity-20"></div>
        <Title variant="h3">{{ t('goals.food.title') }}</Title>
        <p class="opacity-60">
          {{ t('goals.food.description') }}
        </p>
      </div>
      <div
        class="bg-surface-variant flex flex-col items-center justify-center gap-4 rounded-2xl p-8"
      >
        <Title variant="h3">{{ t('goals.biodiversity.title') }}</Title>
        <p>
          {{ t('goals.biodiversity.description') }}
        </p>
      </div>
      <div
        class="bg-surface-variant flex flex-col items-center justify-center gap-4 rounded-2xl p-8"
      >
        <Title variant="h3">{{ t('goals.social.title') }}</Title>
        <p>
          {{ t('goals.social.description') }}
        </p>
      </div>
    </div>
  </Container>

  <Container class="flex flex-col items-center gap-8 pt-20 text-center">
    <Title variant="h2">{{ t('status.title') }}</Title>
    <Paragraph>{{ t('status.description') }}</Paragraph>
    <p class="mx-auto max-w-3xl text-justify whitespace-pre-line opacity-60">
      {{ t('status.content') }}
    </p>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';

const { t } = usePrefixedI18n(CONFIG);
</script>
