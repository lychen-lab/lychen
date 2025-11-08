<template>
  <div
    :class="
      cn(VARIANT_VALUES[variant], props.class, 'text-on-surface', 'component-section-setting')
    "
  >
    <div class="flex flex-col gap-1 p-4">
      <BaseHeading variant="h2">{{ title }}</BaseHeading>
      <p class="opacity-80">{{ description }}</p>
      <div class="flex gap-2 py-2">
        <slot name="subTitle" />
      </div>
    </div>
    <div class="flex flex-col gap-8 p-4">
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { VARIANT_VALUES, type VariantKey } from '.';
import type { HTMLAttributes } from 'vue';
import BaseHeading from '../base-heading/BaseHeading.vue';

const props = withDefaults(
  defineProps<{
    title?: string;
    description?: string;
    variant?: VariantKey;
    class?: HTMLAttributes['class'];
  }>(),
  {
    variant: 'default',
    title: undefined,
    description: undefined,
    class: undefined,
  },
);
</script>

<style scoped>
.component-section-setting {
  border: none;

  & ~ & {
    border-top: solid 2px var(--color-surface-container);
  }
}
</style>
