<template>
  <div class="flex w-full flex-col gap-2">
    <!-- Zone labels, sized to their level range (3 / 4 / 3 out of 10). -->
    <div
      class="flex items-end gap-1 text-[11px] font-bold tracking-wide uppercase md:text-xs"
      :class="onDark ? 'text-white/90' : ''"
    >
      <span
        class="grow basis-0 truncate"
        :class="onDark ? '' : 'text-yellow-600 dark:text-yellow-400'"
        :style="{ flexGrow: 3 }"
        >{{ transitionLabel }}</span
      >
      <span
        class="grow basis-0 truncate text-center"
        :class="onDark ? '' : 'text-emerald-600 dark:text-emerald-400'"
        :style="{ flexGrow: 4 }"
        >{{ preservationLabel }}</span
      >
      <span
        class="grow basis-0 truncate text-right"
        :class="onDark ? '' : 'text-amber-600 dark:text-amber-400'"
        :style="{ flexGrow: 3 }"
        >{{ regenerationLabel }}</span
      >
    </div>

    <!-- Gradient track: transition (yellow) → preservation (emerald) → regeneration (gold). -->
    <div
      class="relative h-2.5 w-full overflow-hidden rounded-full ring-1 ring-inset"
      :class="onDark ? 'ring-white/25' : 'ring-on-surface/10'"
    >
      <div
        aria-hidden="true"
        class="scalebar-fill absolute inset-0"
      />
    </div>

    <!-- Level ticks 1 → 10. -->
    <div
      class="flex justify-between px-px text-[10px] font-bold tabular-nums"
      :class="onDark ? 'text-white/70' : 'text-on-surface/55'"
    >
      <span
        v-for="n in 10"
        :key="n"
        >{{ n }}</span
      >
    </div>
  </div>
</template>

<script setup lang="ts">
defineProps<{
  transitionLabel: string;
  preservationLabel: string;
  regenerationLabel: string;
  onDark?: boolean;
}>();
</script>

<style scoped>
.scalebar-fill {
  background: linear-gradient(
    90deg,
    oklch(0.84 0.15 95) 0%,
    oklch(0.8 0.14 110) 24%,
    oklch(0.72 0.14 155) 50%,
    oklch(0.74 0.14 130) 70%,
    oklch(0.82 0.15 80) 100%
  );
}
</style>
