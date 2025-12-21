<template>
  <Container class="flex flex-col items-center gap-4">
    <Title
      variant="h2"
      class="text-center md:w-2/3"
      >{{ t('use_cases.title') }}</Title
    >
    <Paragraph
      variant="website-highlight"
      class="text-center opacity-80 md:w-2/3"
    >
      {{ t('use_cases.description') }}
    </Paragraph>
    <LychenEcosystem />
  </Container>
</template>

<script setup lang="ts">
import { defineAsyncComponent } from 'vue';
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

const LychenEcosystem = defineAsyncComponent(
  () => import('@lychen/vue-drawio-core/LychenEcosystem.vue'),
);

const Paragraph = defineAsyncComponent(
  () => import('@lychen/vue-components-website/paragraph/Paragraph.vue'),
);

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Container = defineAsyncComponent(
  () => import('@lychen/vue-components-website/container/Container.vue'),
);

const { t } = usePrefixedI18n(CONFIG);
</script>
