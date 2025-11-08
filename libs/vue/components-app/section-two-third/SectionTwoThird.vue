<template>
  <section
    class="flex flex-col-reverse gap-4 md:grid md:h-full md:grid-cols-[2fr_1fr] md:grid-rows-1"
  >
    <div :class="cn('flex flex-col gap-4', mainClass)">
      <BaseHeading v-if="title">{{ title }}</BaseHeading>
      <slot />
    </div>
    <div
      :class="
        cn(
          'rounded-xl min-h-[200px] md:min-h-full bg-secondary-container text-on-secondary-container p-4',
          sideClass,
        )
      "
    >
      <slot name="side"></slot>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { cn } from '@lychen/typescript-utils/tailwind/Cn';
import { BaseHeading } from '../base-heading';

defineProps<{ title?: string; mainClass?: string; sideClass?: string }>();
</script>

<style lang="css" scoped></style>
