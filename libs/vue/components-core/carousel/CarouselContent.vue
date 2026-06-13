<template>
  <div
    :ref="setCarouselRef"
    class="overflow-hidden"
  >
    <div
      :class="cn('flex', orientation === 'horizontal' ? '-ml-4' : '-mt-4 flex-col', props.class)"
      v-bind="$attrs"
    >
      <slot />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { ComponentPublicInstance } from 'vue';
import type { WithClassAsProps } from './interface';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { useCarousel } from './useCarousel';

defineOptions({
  inheritAttrs: false,
});

const props = defineProps<WithClassAsProps>();

const { carouselRef, orientation } = useCarousel();

// Embla exposes a `Ref<HTMLElement>`; forward the mounted node into it via a function ref.
function setCarouselRef(el: Element | ComponentPublicInstance | null) {
  carouselRef.value = el instanceof HTMLElement ? el : undefined;
}
</script>
