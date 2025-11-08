<template>
  <div
    class="component bg-on-surface-container dark:bg-surface-container outline-on-surface-container/60 dark:outline-surface-container/60 flex flex-col items-center gap-4 rounded-3xl p-6 outline outline-offset-4"
  >
    <small class="text-center italic md:hidden">{{ t('mobile_message') }}</small>
    <slot>
      <div
        v-if="mxgraph"
        class="mxgraph"
        :data-mxgraph="mxgraph"
      ></div>
    </slot>
  </div>
</template>

<script setup lang="ts">
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { onMounted } from 'vue';

defineProps<{ mxgraph?: string }>();
onMounted(() => {
  let drawioScript = document.createElement('script');
  drawioScript.setAttribute('src', 'https://viewer.diagrams.net/js/viewer-static.min.js');
  document.head.appendChild(drawioScript);
});

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>

<style scoped>
.component {
  max-width: 100%;
}
</style>

<style>
.geDarkMode {
  border: 1px solid transparent !important;
}
</style>
