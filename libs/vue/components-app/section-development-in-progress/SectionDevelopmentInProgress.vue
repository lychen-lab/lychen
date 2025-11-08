<template>
  <PatternBackground
    :direction="PATTERN_BACKGROUND_DIRECTION.TopRight"
    :variant="PATTERN_BACKGROUND_VARIANT.Grid"
    class="bg-surface-container flex size-full flex-col items-center justify-center gap-4 rounded-3xl p-20"
  >
    <IconPickaxe
      size="2xl"
      class="text-warning"
    />
    <div class="flex flex-col items-center justify-center">
      <BaseHeading class="text-warning">{{ title }}</BaseHeading>
      <p class="text-warning text-sm opacity-70">En cours de développement</p>
    </div>
  </PatternBackground>
</template>

<script setup lang="ts">
import {
  PATTERN_BACKGROUND_DIRECTION,
  PATTERN_BACKGROUND_VARIANT,
  PatternBackground,
} from '@lychen/vue-components-core/pattern-background';
import BaseHeading from '../base-heading/BaseHeading.vue';
import IconPickaxe from '@lychen/vue-icons/IconPickaxe.vue';

defineProps<{ title?: string }>();
</script>
