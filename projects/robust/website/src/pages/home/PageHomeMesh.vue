<template>
  <Container>
    <div
      class="bg-secondary-container text-on-secondary-container outline-secondary-container flex flex-col-reverse items-stretch rounded-3xl outline outline-offset-4 xl:flex-row"
    >
      <div class="flex flex-col gap-6 p-8 md:basis-1/2 md:p-20">
        <Title variant="h2">{{ t('mesh.title') }}</Title>
        <Paragraph variant="website-highlight">{{ t('mesh.highlight_text') }}</Paragraph>
        <div class="flex flex-col gap-4">
          <div class="bg-secondary/90 text-on-secondary rounded-xl p-4">
            {{ t('mesh.text_block_1') }}
          </div>
          <div class="bg-secondary/70 text-on-secondary rounded-xl p-4 xl:translate-x-1/10">
            {{ t('mesh.text_block_2') }}
          </div>
          <div class="bg-secondary/50 text-on-secondary rounded-xl p-4">
            {{ t('mesh.text_block_3') }}
          </div>
        </div>
      </div>
      <div
        class="flex h-[300px] flex-col items-center justify-center rounded-tl-3xl rounded-tr-3xl bg-cover xl:h-auto xl:basis-1/2 xl:rounded-tl-none xl:rounded-br-3xl"
        :style="`background-image: url('${ImageUrl}');`"
      ></div>
    </div>
  </Container>
</template>

<script setup lang="ts">
import ImageUrl from './assets/Collaborate.webp';
import { messages, TRANSLATION_KEY } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
