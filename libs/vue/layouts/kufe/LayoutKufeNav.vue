<template>
  <nav :class="cn('flex flex-row justify-between gap-4', props.class)">
    <slot />
  </nav>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import type { HTMLAttributes } from 'vue';

interface Props {
  class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>
