<template>
  <RouterLink
    :to="to"
    class="menu-item hover:bg-surface-container-highest -mx-3 flex flex-row items-center justify-start gap-4 rounded-xl px-3 py-2"
  >
    <Icon
      v-if="icon"
      :icon="icon"
      class="h-5 w-5 shrink-0"
    />
    <span>{{ label }}</span>
  </RouterLink>
</template>

<script lang="ts" setup>
import { type NavigationItem } from '.';

defineProps<NavigationItem>();
</script>
