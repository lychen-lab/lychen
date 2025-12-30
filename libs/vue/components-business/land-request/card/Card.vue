<template>
  <div
    class="bg-surface-container-low border-on-surface/5 flex max-w-[275px] min-w-[275px] flex-col gap-2 rounded-3xl border-1 p-4 text-left"
    data-uuid="uuid"
  >
    <div>
      <p class="font-bold">{{ firstName }} {{ lastName }}</p>
    </div>
    <p class="line-clamp-3 text-sm opacity-70">{{ description }}</p>
    <div class="flex items-center gap-1 text-xs">
      <IconSquareDashed class="!size-4 text-lime-700 dark:text-lime-200" />{{
        n(minimalSurfaceRequested, 'square-meter')
      }}
    </div>
  </div>
</template>

<script setup lang="ts">
import type { LandRequest } from '.';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import IconSquareDashed from '@lychen/vue-icons/IconSquareDashed.vue';

interface Props extends LandRequest {}

const props = defineProps<Props>();

const { n } = usePrefixedI18n();
</script>
