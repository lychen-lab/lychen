<template>
  <section class="odd relative scroll-mt-24 overflow-hidden">
    <div
      aria-hidden="true"
      class="odd-blob absolute bottom-[-10%] left-[-12%]"
    />
    <Container class="relative flex flex-col gap-12">
      <div
        ref="headerRef"
        class="reveal-group flex max-w-2xl flex-col gap-4"
        :class="{ 'is-visible': headerVisible }"
      >
        <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
          t('goals.kicker')
        }}</span>
        <Title
          variant="h2"
          class="font-lexend text-4xl font-extrabold tracking-tight md:text-5xl"
          >{{ t('goals.title') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="opacity-80"
          >{{ t('goals.description') }}</Paragraph
        >
      </div>

      <div
        class="flex w-full flex-col-reverse gap-10 md:grid md:grid-cols-[34%_1fr] md:items-center"
      >
        <div class="flex flex-col justify-center gap-8">
          <GoalSubSection
            v-for="goal in goals"
            :key="goal.index"
            :title="goal.title"
            :description="goal.description"
            :link="{
              title: t(`goals.link_title.${goal.index}`),
              href: goal.link,
            }"
            :expanded="goal.index === selectedGoal.index"
            @click="selectedGoal = goal"
          />
        </div>
        <figure
          class="ring-on-surface/10 relative mx-auto overflow-hidden rounded-3xl shadow-xl ring-1"
        >
          <img
            v-if="selectedGoal"
            :src="`/sustainable-development-goals/icons/${selectedGoal.icon}`"
            class="odd-icon absolute z-10 h-14 rounded-2xl shadow-lg md:h-20"
            :alt="`Icône de l'objectif de développement durable n° ${selectedGoal.index}`"
          />
          <img
            :key="selectedGoal.index"
            :src="images[selectedGoal.index]"
            :alt="`Image de l'objectif de développement durable n° ${selectedGoal.index}`"
            class="motion-preset-slide-left-sm size-full object-cover"
          />
          <div
            aria-hidden="true"
            class="odd-photo-grade absolute inset-0"
          />
        </figure>
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
import Goal2Url from './assets/goal-2.webp';
import Goal12Url from './assets/goal-12.webp';
import Goal11Url from './assets/goal-11.webp';
import { computed, defineAsyncComponent, ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';
import { useSustainableDevelopmentGoals } from '@lychen/vue-sustainable-development-goals/composables/useSustainableDevelopmentGoals';
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

const GoalSubSection = defineAsyncComponent(
  () => import('@/views/home/component/GoalSubSection.vue'),
);

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Paragraph = defineAsyncComponent(
  () => import('@lychen/vue-components-website/paragraph/Paragraph.vue'),
);

const Container = defineAsyncComponent(
  () => import('@lychen/vue-components-website/container/Container.vue'),
);

const { t } = usePrefixedI18n(CONFIG);

const { two, eleven, twelve } = useSustainableDevelopmentGoals();

const goals = computed(() => [eleven.value, two.value, twelve.value]);
const selectedGoal = ref(eleven.value);

const images: { [key: number]: string } = {
  2: Goal2Url,
  11: Goal11Url,
  12: Goal12Url,
};

const headerRef = ref<HTMLElement | null>(null);
const headerVisible = ref(false);
useIntersectionObserver(
  headerRef,
  (entries) => {
    if (entries.some((entry) => entry.isIntersecting)) {
      headerVisible.value = true;
    }
  },
  { threshold: 0.2 },
);
</script>

<style scoped>
.odd-blob {
  width: clamp(280px, 36vw, 520px);
  height: clamp(280px, 36vw, 520px);
  border-radius: 9999px;
  filter: blur(80px);
  pointer-events: none;
  z-index: 0;
  background: var(--color-tertiary);
  opacity: 0.1;
}

.odd-icon {
  top: -20px;
  left: -20px;
}

.odd-photo-grade {
  background: linear-gradient(180deg, transparent 55%, rgb(0 0 0 / 0.18) 100%);
}

.reveal-group > * {
  opacity: 0;
  translate: 0 26px;
  transition:
    opacity 0.7s ease,
    translate 0.7s ease;
}

.reveal-group > *:nth-child(2) {
  transition-delay: 0.1s;
}

.reveal-group > *:nth-child(3) {
  transition-delay: 0.2s;
}

.reveal-group.is-visible > * {
  opacity: 1;
  translate: 0 0;
}

@media (prefers-reduced-motion: reduce) {
  .reveal-group > * {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }
}
</style>
