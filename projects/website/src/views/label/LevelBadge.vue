<template>
  <div
    :class="
      cn(
        'relative flex flex-col items-center justify-center rounded-2xl ring-1 ring-inset',
        sizeClasses.container,
        colors.surface,
        colors.ring,
        colors.text,
        tranche === 'regeneration' ? colors.glow : '',
        props.class,
      )
    "
  >
    <span :class="cn('font-semibold uppercase opacity-70', sizeClasses.brand)">Robust</span>
    <span :class="cn('font-lexend leading-none font-black tabular-nums', sizeClasses.number)">{{
      clampedLevel
    }}</span>
  </div>
</template>

<script setup lang="ts">
import { computed, type HTMLAttributes } from 'vue';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';

const props = defineProps<{
  level: number;
  size?: 'sm' | 'md' | 'lg';
  class?: HTMLAttributes['class'];
}>();

const clampedLevel = computed(() => Math.max(1, Math.min(10, props.level)));

const tranche = computed(() => {
  if (clampedLevel.value <= 3) return 'transition';
  if (clampedLevel.value <= 7) return 'preservation';
  return 'regeneration';
});

const colors = computed(() => {
  switch (tranche.value) {
    case 'transition':
      return {
        surface: 'bg-yellow-50 dark:bg-yellow-950/40',
        ring: 'ring-yellow-500/35',
        text: 'text-yellow-700 dark:text-yellow-300',
        glow: '',
      };
    case 'preservation':
      return {
        surface: 'bg-emerald-50 dark:bg-emerald-950/40',
        ring: 'ring-emerald-500/35',
        text: 'text-emerald-700 dark:text-emerald-300',
        glow: '',
      };
    case 'regeneration':
    default:
      return {
        surface: 'bg-amber-50 dark:bg-amber-950/40',
        ring: 'ring-amber-500/40',
        text: 'text-amber-700 dark:text-amber-200',
        glow: 'shadow-[0_0_28px_-8px_oklch(0.78_0.13_75_/_0.7)]',
      };
  }
});

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return {
        container: 'size-14 gap-0.5',
        brand: 'text-[8px] tracking-[0.14em]',
        number: 'text-xl',
      };
    case 'lg':
      return { container: 'size-28 gap-1', brand: 'text-xs tracking-[0.22em]', number: 'text-5xl' };
    default:
      return {
        container: 'size-20 gap-0.5',
        brand: 'text-[10px] tracking-[0.18em]',
        number: 'text-3xl',
      };
  }
});
</script>
