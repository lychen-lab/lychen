<template>
  <div class="flex flex-col gap-2">
    <span
      v-if="title"
      class="text-xs font-bold uppercase opacity-50"
      >{{ title }}</span
    >
    <slot />
  </div>
</template>

<script lang="ts" setup>
import { type NavigationSection } from '.';

defineProps<NavigationSection>();
</script>
