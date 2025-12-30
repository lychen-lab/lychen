<template>
  <div
    :class="
      cn(
        'duration-300 relative flex flex-col items-center justify-center border-2 transition-all',
        'clip-shield',
        sizeClasses.container,
        colors.bg,
        colors.border,
        colors.text,
        tranche === 'regeneration' ? colors.glow : '',
        props.className,
      )
    "
  >
    <!-- Icon/Illustration placeholder based on level could go here -->
    <div class="z-10 flex flex-col items-center">
      <span :class="cn('font-bold uppercase tracking-wider', sizeClasses.text)">Robust</span>
      <span :class="cn('leading-none font-black', sizeClasses.number)">{{ level }}</span>
    </div>

    <!-- Decorative background elements -->
    <div
      v-if="tranche === 'regeneration'"
      class="absolute inset-0 bg-gradient-to-t from-yellow-400/20 to-transparent"
    ></div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';

const props = defineProps<{
  level: number;
  size?: 'sm' | 'md' | 'lg';
  className?: string; // Allow custom classes
}>();

const level = computed(() => Math.max(1, Math.min(10, props.level)));

const tranche = computed(() => {
  if (level.value <= 3) return 'transition';
  if (level.value <= 7) return 'preservation';
  return 'regeneration';
});

const colors = computed(() => {
  switch (tranche.value) {
    case 'transition':
      return {
        bg: 'bg-yellow-100 dark:bg-yellow-900/30',
        border: 'border-yellow-500',
        text: 'text-yellow-700 dark:text-yellow-300',
        icon: 'text-yellow-600 dark:text-yellow-400',
      };
    case 'preservation':
      return {
        bg: 'bg-emerald-100 dark:bg-emerald-900/30',
        border: 'border-emerald-500',
        text: 'text-emerald-700 dark:text-emerald-300',
        icon: 'text-emerald-600 dark:text-emerald-400',
      };
    case 'regeneration':
    default:
      return {
        bg: 'bg-amber-100 dark:bg-amber-900/30',
        border: 'border-amber-500', // Gold-ish
        text: 'text-amber-800 dark:text-amber-200',
        icon: 'text-amber-600 dark:text-amber-400',
        glow: 'shadow-[0_0_15px_rgba(245,158,11,0.5)]',
      };
  }
});

const sizeClasses = computed(() => {
  switch (props.size) {
    case 'sm':
      return { container: 'h-20 w-16', text: 'text-[10px]', number: 'text-xl' };
    case 'lg':
      return { container: 'h-48 w-40', text: 'text-base', number: 'text-5xl' };
    default: // md
      return { container: 'h-28 w-24', text: 'text-xs', number: 'text-3xl' };
  }
});
</script>

<style scoped>
.clip-shield {
  clip-path: path('M 50 0 L 100 25 L 100 65 Q 50 100 0 65 L 0 25 Z');
  /* Normalized to 100x100 for SVG path scaling, but CSS clip-path usually needs absolute units or complex calc. 
     Let's use a simpler polygon for robustness if SVG path is tricky without fixed dimensions,
     OR better yet, use a standard shield shape using border-radius. */
  clip-path: none; /* Reset for border-radius approach below */
  border-radius: 0 0 50% 50% / 0 0 100% 100%;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
}
</style>
