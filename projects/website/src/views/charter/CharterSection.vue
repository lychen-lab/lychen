<template>
  <div class="flex flex-col items-start gap-4 rounded-3xl border border-black/10 p-4 md:p-8">
    <Title variant="h2">{{ t(`${i18nKey}.title`) }}</Title>
    <Paragraph>{{ t(`${i18nKey}.description`) }}</Paragraph>
    <div class="mt-4 flex flex-col gap-8 md:grid md:grid-cols-2">
      <slot />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';

defineProps<{
  i18nKey: string;
}>();

const { t } = usePrefixedI18n(CONFIG);
</script>
