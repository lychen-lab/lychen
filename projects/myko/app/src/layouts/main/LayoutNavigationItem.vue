<template>
  <RouterLink
    :to="{ name: routeName }"
    :aria-current="isActive ? 'page' : undefined"
    :class="[
      'flex min-w-0 flex-1 flex-col items-center justify-center gap-0.5',
      isActive ? 'font-semibold text-on-surface' : INACTIVE_LABEL_CLASS,
    ]"
  >
    <span
      :class="[
        'rounded-full px-4 py-1',
        isActive ? 'bg-primary-container text-on-primary-container' : '',
      ]"
    >
      <slot name="icon" />
    </span>
    <span class="text-[0.7rem]">{{ label }}</span>
  </RouterLink>
</template>

<script lang="ts" setup>
import { computed } from 'vue';
import { useRoute } from 'vue-router';

import { isPathActive } from './isPathActive';

const props = defineProps<{
  label: string;
  routeName: string;
  /** Path prefixes of the screens this item stands for (a log's page is under "Bûches"). */
  activePaths: string[];
}>();

const route = useRoute();

/**
 * 70% and not 60%: the small label on the bar's background measures 4.41:1 in the light theme at 60%,
 * under the 4.5:1 that WCAG AA asks of text this size (found with axe at 390 px). At 70% it is
 * 6.16:1, and still lighter than the active item's label.
 */
const INACTIVE_LABEL_CLASS = 'text-on-surface/70';

const isActive = computed(() => props.activePaths.some((path) => isPathActive(route.path, path)));
</script>
