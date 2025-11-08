<template>
  <ProgressRoot
    v-bind="delegatedProps"
    :class="cn('relative h-4 w-full overflow-hidden rounded-full bg-secondary', props.class)"
  >
    <ProgressIndicator
      class="bg-primary h-full w-full flex-1 transition-all"
      :style="`transform: translateX(-${100 - (props.modelValue ?? 0)}%);`"
    />
  </ProgressRoot>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { ProgressIndicator, ProgressRoot, type ProgressRootProps } from 'reka-ui';
import { computed, type HTMLAttributes } from 'vue';

const props = withDefaults(defineProps<ProgressRootProps & { class?: HTMLAttributes['class'] }>(), {
  modelValue: 0,
  class: undefined,
});

const delegatedProps = computed(() => {
  const { class: _, ...delegated } = props;

  return delegated;
});
</script>
