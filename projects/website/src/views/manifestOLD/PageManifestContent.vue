<template>
  <div class="flex flex-col gap-4">
    <Container
      v-for="(sectionKey, index) in Object.keys(messages['fr-FR'].section)"
      :key="index"
      class="md:py-10"
    >
      <div class="flex flex-col gap-2 md:flex-row md:gap-8">
        <div
          class="bg-tertiary-container text-on-tertiary-container flex basis-1/4 flex-col gap-2 rounded-3xl p-4 md:p-6"
        >
          <Title
            variant="h2"
            class="break-words"
            >{{ t(`section.${sectionKey}.title`) }}</Title
          >
          <Paragraph class="opacity-80">{{ t(`section.${sectionKey}.description`) }}</Paragraph>
        </div>

        <div class="flex basis-3/4 flex-col gap-6 p-4 md:p-6">
          <div
            v-for="(subSectionKey, _index) in Object.keys(
              messages['fr-FR'].section[sectionKey].sub_section,
            )"
            :key="_index"
            class="flex flex-col gap-1"
          >
            <Title variant="h3">{{
              t(`section.${sectionKey}.sub_section.${subSectionKey}.title`)
            }}</Title>
            <Paragraph class="opacity-80">{{
              t(`section.${sectionKey}.sub_section.${subSectionKey}.description`)
            }}</Paragraph>
          </div>
        </div>
      </div>
    </Container>
  </div>
</template>

<script setup lang="ts">
import { defineAsyncComponent } from 'vue';
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

const Paragraph = defineAsyncComponent(
  () => import('@lychen/vue-components-website/paragraph/Paragraph.vue'),
);

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Container = defineAsyncComponent(
  () => import('@lychen/vue-components-website/container/Container.vue'),
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
