<template>
  <div class="flex flex-col gap-8">
    <Title variant="h2">{{ t(`${i18nKey}.title`) }}</Title>
    <div class="flex flex-col gap-12">
      <slot />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Title from '@lychen/vue-components-website/title/Title.vue';

defineProps<{
  i18nKey: string;
}>();

const { t } = usePrefixedI18n(CONFIG);
</script>
