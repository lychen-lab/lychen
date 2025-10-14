<template>
  <span
    v-if="navigationExpanded"
    class="text-xs font-bold uppercase opacity-50"
    >{{ label }}</span
  >
  <hr
    v-else
    class="mb-2 opacity-20"
  />
</template>

<script lang="ts" setup>
import { inject } from 'vue';
import { INJECTION_KEY_NAVIGATION_EXPANDED, type NavigationSection } from '.';

defineProps<Pick<NavigationSection, 'label'>>();

const navigationExpanded = inject(INJECTION_KEY_NAVIGATION_EXPANDED);
</script>
