<template>
  <article class="bento-card group relative overflow-hidden rounded-3xl shadow-lg">
    <img
      :src="image"
      :alt="goal.title"
      class="size-full object-cover transition-transform duration-500 group-hover:scale-[1.04]"
    />
    <img
      :src="`/sustainable-development-goals/icons/${goal.icon}`"
      class="absolute top-4 left-4 h-10 rounded-xl shadow-md ring-2 ring-white/20 md:h-12"
      :alt="`ODD n°${goal.index}`"
    />
    <div class="bento-overlay absolute inset-0" />
    <div class="absolute right-0 bottom-0 left-0 flex flex-col gap-2 p-5 text-white">
      <h3
        class="font-lexend leading-tight font-bold"
        :class="large ? 'text-2xl md:text-3xl' : 'text-lg md:text-xl'"
      >
        {{ goal.title }}
      </h3>
      <p
        v-if="large"
        class="line-clamp-3 text-sm leading-relaxed opacity-80 md:text-base"
      >
        {{ goal.description }}
      </p>
      <a
        :href="goal.link"
        target="_blank"
        rel="noopener noreferrer"
        class="mt-0.5 inline-flex items-center gap-1.5 text-sm font-semibold opacity-90 hover:opacity-100"
      >
        <span class="underline underline-offset-2">{{ linkTitle }}</span>
        <IconArrowUpRight class="h-4 w-4 shrink-0" />
      </a>
    </div>
  </article>
</template>

<script setup lang="ts">
import IconArrowUpRight from '@lychen/vue-icons/IconArrowUpRight.vue';
import type { SustainableDevelopmentGoal } from '@lychen/vue-sustainable-development-goals/model/SustainableDevelopmentGoal';

interface Props {
  goal: SustainableDevelopmentGoal;
  image: string;
  linkTitle: string;
  large?: boolean;
}

defineProps<Props>();
</script>

<style scoped>
.bento-overlay {
  background: linear-gradient(
    to top,
    rgb(0 0 0 / 0.72) 0%,
    rgb(0 0 0 / 0.05) 55%,
    transparent 100%
  );
}
</style>
