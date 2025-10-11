<template>
  <Sheet side="right">
    <SheetTrigger as-child>
      <slot name="trigger" />
    </SheetTrigger>
    <SheetContent
      :class="
        cn('bg-surface-container/70 text-on-surface-container w-full backdrop-blur-lg', props.class)
      "
    >
      <slot name="content" />
    </SheetContent>
  </Sheet>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import type { HTMLAttributes } from 'vue';

interface Props {
  class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>
