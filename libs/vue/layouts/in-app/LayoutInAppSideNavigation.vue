<template>
  <nav class="layout-in-app-side-navigation flex h-full flex-col p-3">
    <div
      :class="
        cn(
          'flex flex-col h-full rounded-xl gap-8',
          'bg-surface-container-low',
          'relative transition-all duration-200 ease-in-out',
          expanded ? 'w-[250px] p-4' : 'w-[60px] px-2 py-4 items-center',
          props.class,
        )
      "
    >
      <slot :expanded />
    </div>
  </nav>
</template>

<script lang="ts" setup>
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { type HTMLAttributes } from 'vue';
import { INJECTION_KEY_NAVIGATION_EXPANDED } from '.';
import { provide } from 'vue';

const expanded = defineModel<boolean>({ default: true });

const props = defineProps<{ class?: HTMLAttributes['class'] }>();

provide(INJECTION_KEY_NAVIGATION_EXPANDED, expanded);
</script>
