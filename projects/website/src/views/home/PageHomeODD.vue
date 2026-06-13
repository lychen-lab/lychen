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

      <!-- Bento grid: large "Faim zéro" card on the right, two smaller cards on the left -->
      <div class="grid grid-cols-1 gap-4 md:grid-cols-[1fr_1.4fr]">
        <OddBentoCard
          :goal="two"
          :image="images[2]"
          :link-title="t('goals.link_title.2')"
          large
          class="min-h-80 md:col-start-2 md:row-span-2 md:row-start-1"
        />
        <OddBentoCard
          :goal="eleven"
          :image="images[11]"
          :link-title="t('goals.link_title.11')"
          class="min-h-56 md:col-start-1 md:row-start-1"
        />
        <OddBentoCard
          :goal="twelve"
          :image="images[12]"
          :link-title="t('goals.link_title.12')"
          class="min-h-56 md:col-start-1 md:row-start-2"
        />
      </div>
    </Container>
  </section>
</template>

<script setup lang="ts">
import Goal2Url from './assets/goal-2.webp';
import Goal12Url from './assets/goal-12.webp';
import Goal11Url from './assets/goal-11.webp';
import { defineAsyncComponent, ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';
import { useSustainableDevelopmentGoals } from '@lychen/vue-sustainable-development-goals/composables/useSustainableDevelopmentGoals';
import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

const OddBentoCard = defineAsyncComponent(() => import('@/views/home/component/OddBentoCard.vue'));

const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));

const Paragraph = defineAsyncComponent(
  () => import('@lychen/vue-components-website/paragraph/Paragraph.vue'),
);

const Container = defineAsyncComponent(
  () => import('@lychen/vue-components-website/container/Container.vue'),
);

const { t } = usePrefixedI18n(CONFIG);

const { two, eleven, twelve } = useSustainableDevelopmentGoals();

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
