<template>
  <div class="absolute top-full z-60 flex justify-center">
    <NavigationMenuViewport
      v-bind="forwardedProps"
      :class="
        cn(
          'origin-top-center relative mt-3 h-[var(--reka-navigation-menu-viewport-height)] w-full overflow-hidden rounded-3xl shadow-lg data-[state=open]:animate-in data-[state=closed]:animate-out data-[state=closed]:zoom-out-95 data-[state=open]:zoom-in-90 md:w-[var(--reka-navigation-menu-viewport-width)]',
          props.class,
        )
      "
    />
  </div>
</template>

<script setup lang="ts">
import { NavigationMenuViewport, type NavigationMenuViewportProps, useForwardProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

import { cn } from '@lychen/typescript-utils/tailwind/Cn';

const props = defineProps<NavigationMenuViewportProps & { class?: HTMLAttributes['class'] }>();

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});

const forwardedProps = useForwardProps(delegatedProps);
</script>
