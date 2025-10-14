<template>
  <div
    class="bg-surface-container-high text-on-surface-container active:bg-surface-container-highest flex h-full flex-row items-center justify-between gap-4 rounded-3xl p-4"
  >
    <div class="flex flex-col gap-1">
      <span class="truncate text-sm"
        >{{ landGreenhouse.name }}
      </span>
    </div>
  </div>
</template>

<script lang="ts" setup>
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { VARIANT, type Variant } from '.';

const { variant = VARIANT.Default, landGreenhouse } = defineProps<{
  landGreenhouse: components['schemas']['LandGreenhouse.jsonld'];
  variant?: Variant;
}>();
</script>
