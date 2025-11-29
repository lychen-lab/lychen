<template>
  <div :class="cn('bg-surface-container rounded-lg p-4', props.class)">
    Card LandRequest {{ id }}
  </div>
</template>

<script setup lang="ts">
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import type { HTMLAttributes } from 'vue';

interface Props {
  id: string;
  class?: HTMLAttributes['class'];
}

const props = defineProps<Props>();
</script>
