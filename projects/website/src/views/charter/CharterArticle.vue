<template>
  <div class="flex flex-col gap-2">
    <Title variant="h3">{{ t(`${i18nKey}.title`) }}</Title>
    <Paragraph class="opacity-80">{{ t(`${i18nKey}.content`) }}</Paragraph>
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
