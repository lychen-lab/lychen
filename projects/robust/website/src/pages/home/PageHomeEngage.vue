<template>
  <Container
    wrapper-class="bg-gradient-to-b from-surface-container to-surface"
    class="flex flex-col"
  >
    <div class="flex flex-col items-center gap-8 text-center">
      <Title
        variant="h2"
        class="md:w-2/3"
        >{{ t('engage.main_title') }}</Title
      >
      <Paragraph
        variant="website-highlight"
        class="md:w-2/3"
        >{{ t('engage.highlight_text') }}</Paragraph
      >
      <div class="flex flex-col gap-8 md:flex-row">
        <div
          class="from-primary to-primary/60 shadow-primary/50 dark:shadow-primary-container/50 text-on-primary outline-primary-container/90 flex flex-col items-center justify-center gap-4 rounded-2xl bg-gradient-to-tr p-8 shadow-2xl outline-1 outline-offset-4 md:basis-1/3"
        >
          <Title variant="h4">{{ t('engage.ecosystem.title') }}</Title>
          <p>
            {{ t('engage.ecosystem.text') }}
          </p>
        </div>
        <div
          class="bg-surface-container-high outline-surface-container-high/50 flex flex-col items-center justify-center gap-4 rounded-2xl p-8 outline-1 outline-offset-4 md:basis-1/3"
        >
          <Title variant="h4">{{ t('engage.connections.title') }}</Title>
          <p>
            {{ t('engage.connections.text') }}
          </p>
        </div>
        <div
          class="bg-surface-container-high outline-surface-container-high/50 flex flex-col items-center justify-center gap-4 rounded-2xl p-8 outline-1 outline-offset-4 md:basis-1/3"
        >
          <Title variant="h4">{{ t('engage.commitments.title') }}</Title>
          <p>
            {{ t('engage.commitments.text') }}
          </p>
        </div>
      </div>
    </div>
  </Container>
</template>

<script setup lang="ts">
import { messages, TRANSLATION_KEY } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
