<template>
  <div
    v-if="kind"
    class="bg-secondary-container text-on-secondary-container flex flex-row items-center gap-2 rounded-md px-2 py-1 text-xs uppercase"
  >
    {{ t(`property.kind.${kind}.default`) }}
  </div>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-area';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
defineProps<{ kind?: string }>();
</script>
