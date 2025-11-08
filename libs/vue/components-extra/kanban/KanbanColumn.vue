<template>
  <div class="bg-surface-container flex flex-col gap-2 rounded-md p-4">
    <div class="flex min-h-[40px] flex-row items-center justify-between">
      <div class="flex flex-row items-center gap-2">
        <slot name="state">
          <Badge>{{ state }}</Badge>
        </slot>
        <small class="opacity-50">{{ totalItems }}</small>
      </div>
      <div>
        <slot name="actions" />
      </div>
    </div>
    <div class="flex flex-col gap-2">
      <slot />
    </div>
  </div>
</template>

<script lang="ts" setup>
import { Badge } from '@lychen/vue-components-core/badge';

defineProps<{
  state: string;
  totalItems?: number;
  closed?: boolean;
}>();
</script>
