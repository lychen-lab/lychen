<template>
  <Container class="flex flex-col gap-8 pt-20">
    <div class="flex flex-col items-center gap-2 text-center">
      <Title variant="h1">{{ t('title') }}</Title>
      <Paragraph class="opacity-60">
        {{ t('last_update', { date: d(new Date(), 'long') }) }}
      </Paragraph>
    </div>
  </Container>

  <Container>
    <div class="flex flex-col gap-6">
      <div
        v-for="section in sections"
        :key="section.title"
        class="flex flex-col gap-2"
      >
        <Title variant="h2">{{ rt(section.title) }}</Title>
        <Paragraph class="whitespace-pre-line opacity-80">{{ rt(section.content) }}</Paragraph>
      </div>
    </div>
  </Container>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { useWebPageSchema } from '@lychen/vue-unhead-composables/useWebPageSchema';
import { computed } from 'vue';

const { t, d, tm, rt } = usePrefixedI18n(CONFIG);

// `tm` returns vue-i18n's deeply-recursive LocaleMessage type; flattening it here to the
// known section shape keeps the template iteration from tripping TS2589.
const sections = computed(
  () => tm('view_terms_of_use.sections') as { title: string; content: string }[],
);

useExtendedHead(t);
useWebPageSchema(t, { siteName: 'espace' });
</script>
