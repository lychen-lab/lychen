<template>
  <!-- HERO / INTRO BAND -->
  <section class="mission-hero relative isolate overflow-hidden">
    <div
      aria-hidden="true"
      class="mission-blob mission-blob-primary absolute z-0"
    />
    <div
      aria-hidden="true"
      class="mission-blob mission-blob-tertiary absolute z-0"
    />

    <svg
      aria-hidden="true"
      class="mission-ridge absolute inset-x-0 bottom-0 z-0 h-[28svh] w-full dark:brightness-[0.55]"
      viewBox="0 0 1440 320"
      preserveAspectRatio="none"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient
          id="mission-ridge-gradient"
          x1="0"
          y1="0"
          x2="0"
          y2="1"
        >
          <stop
            offset="0"
            stop-color="oklch(0.62 0.05 150 / 0.35)"
          />
          <stop
            offset="1"
            stop-color="oklch(0.45 0.055 145 / 0.12)"
          />
        </linearGradient>
      </defs>
      <path
        d="M0 170 C 110 150, 230 112, 360 120 C 490 128, 560 178, 690 186 C 820 194, 900 148, 1020 140 C 1140 132, 1240 162, 1340 170 C 1380 173, 1410 174, 1440 174 L 1440 320 L 0 320 Z"
        fill="url(#mission-ridge-gradient)"
      />
    </svg>

    <Container class="relative z-10 flex flex-col items-center gap-6 pt-28 pb-20 text-center">
      <span
        class="border-on-surface/15 bg-surface-container-low inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-xs font-semibold tracking-widest uppercase backdrop-blur-md"
      >
        <span class="bg-primary size-1.5 animate-pulse rounded-full" />
        {{ t('hero.badge') }}
      </span>
      <Title
        variant="h1"
        class="font-lexend max-w-4xl text-[clamp(2.5rem,6vw,4.5rem)] leading-[1.05] font-extrabold tracking-tight"
        >{{ t('hero.titlePrepend')
        }}<span
          class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.55_0.12_180)] bg-clip-text text-transparent"
          >{{ t('hero.titleHighlight') }}</span
        >{{ t('hero.titleAppend') }}</Title
      >
      <Paragraph
        variant="website-highlight"
        class="max-w-2xl opacity-80"
        >{{ t('meta.description') }}</Paragraph
      >

      <!-- IMAGE PLACEHOLDER: wide hero banner — sunlit regenerative farmland meeting wild meadow, aerial — see Midjourney prompt in PR/ClickUp -->
      <div
        aria-hidden="true"
        class="mission-photo ring-on-surface/10 relative mt-6 flex aspect-[16/7] w-full max-w-4xl items-end overflow-hidden rounded-3xl shadow-xl ring-1"
      >
        <div class="mission-photo-grade absolute inset-0" />
        <span
          class="bg-surface/85 text-on-surface absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold backdrop-blur-md"
        >
          <span class="bg-primary size-1.5 rounded-full" />
          {{ t('hero.imageLabel') }}
        </span>
      </div>
    </Container>
  </section>

  <!-- THREE MISSION PILLARS -->
  <section class="relative">
    <Container class="flex flex-col items-center gap-12 pt-20 text-center">
      <div
        ref="goalsCopy"
        class="mission-reveal flex flex-col items-center gap-4"
        :class="{ 'mission-revealed': goalsRevealed }"
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
          class="max-w-2xl opacity-80"
          >{{ t('goals.description') }}</Paragraph
        >
      </div>

      <div class="grid w-full grid-cols-1 gap-6 text-left md:grid-cols-3">
        <article
          v-for="(pillar, index) in PILLARS"
          :key="pillar.key"
          class="mission-card border-on-surface/10 bg-surface-container-low group relative flex flex-col gap-5 overflow-hidden rounded-3xl border p-6 shadow-sm transition-shadow duration-300 hover:shadow-xl"
          :class="{ 'mission-revealed': goalsRevealed }"
          :style="{ '--reveal-delay': `${0.1 + index * 0.12}s` }"
        >
          <!-- IMAGE PLACEHOLDER: pillar photo — see Midjourney prompt in PR/ClickUp -->
          <div
            aria-hidden="true"
            class="mission-photo ring-on-surface/10 relative flex aspect-[4/3] w-full items-end overflow-hidden rounded-2xl ring-1"
            :class="pillar.photoClass"
          >
            <div class="mission-photo-grade absolute inset-0" />
            <span
              class="bg-surface/85 text-on-surface absolute bottom-2.5 left-2.5 flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold backdrop-blur-md"
            >
              <span
                class="size-1.5 rounded-full"
                :class="pillar.dotClass"
              />
              {{ t(`goals.${pillar.key}.label`) }}
            </span>
          </div>

          <div
            class="ring-on-surface/10 bg-surface flex size-11 items-center justify-center rounded-2xl shadow-sm ring-1"
            :class="pillar.iconWrapClass"
          >
            <component
              :is="pillar.icon"
              class="size-5"
              :class="pillar.iconClass"
            />
          </div>

          <Title
            variant="h3"
            class="font-lexend text-xl font-bold"
            >{{ t(`goals.${pillar.key}.title`) }}</Title
          >
          <p class="opacity-70">
            {{ t(`goals.${pillar.key}.description`) }}
          </p>
        </article>
      </div>
    </Container>
  </section>

  <!-- OFFICIAL FRAMEWORK / STATUS -->
  <section class="relative">
    <Container class="flex flex-col items-center pt-24 pb-20">
      <div
        ref="statusCopy"
        class="mission-reveal mission-status-panel border-on-surface/10 relative w-full max-w-4xl overflow-hidden rounded-3xl border p-8 shadow-sm md:p-12"
        :class="{ 'mission-revealed': statusRevealed }"
      >
        <div
          aria-hidden="true"
          class="mission-blob mission-blob-tertiary !top-auto !right-[-15%] !bottom-[-20%] !left-auto absolute z-0 opacity-60"
        />
        <div class="relative z-10 flex flex-col items-center gap-5 text-center">
          <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
            t('status.kicker')
          }}</span>
          <Title
            variant="h2"
            class="font-lexend text-3xl font-extrabold tracking-tight md:text-4xl"
            >{{ t('status.title') }}</Title
          >
          <Paragraph
            variant="website-highlight"
            class="max-w-2xl opacity-80"
            >{{ t('status.description') }}</Paragraph
          >
          <p class="mt-2 max-w-3xl text-justify whitespace-pre-line opacity-70">
            {{ t('status.content') }}
          </p>
        </div>
      </div>
    </Container>
  </section>
</template>

<script lang="ts" setup>
import { ref, type Ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import IconEarth from '@lychen/vue-icons/IconEarth.vue';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';

const { t } = usePrefixedI18n(CONFIG);
useExtendedHead(t);

const PILLARS = [
  {
    key: 'food',
    icon: IconSprout,
    dotClass: 'bg-primary',
    iconClass: 'text-primary',
    iconWrapClass: '',
    photoClass: 'mission-photo-food',
  },
  {
    key: 'biodiversity',
    icon: IconEarth,
    dotClass: 'bg-positive',
    iconClass: 'text-positive',
    iconWrapClass: '',
    photoClass: 'mission-photo-biodiversity',
  },
  {
    key: 'social',
    icon: IconHeartHandshake,
    dotClass: 'bg-tertiary',
    iconClass: 'text-tertiary',
    iconWrapClass: '',
    photoClass: 'mission-photo-social',
  },
] as const;

function useReveal(target: Ref<HTMLElement | null>) {
  const revealed = ref(false);
  useIntersectionObserver(
    target,
    (entries) => {
      if (entries.some((entry) => entry.isIntersecting)) {
        revealed.value = true;
      }
    },
    { threshold: 0.25 },
  );
  return revealed;
}

const goalsCopy = ref<HTMLElement | null>(null);
const goalsRevealed = useReveal(goalsCopy);
const statusCopy = ref<HTMLElement | null>(null);
const statusRevealed = useReveal(statusCopy);
</script>

<style scoped>
.mission-blob {
  width: clamp(280px, 38vw, 560px);
  height: clamp(280px, 38vw, 560px);
  border-radius: 9999px;
  filter: blur(80px);
  pointer-events: none;
}

.mission-blob-primary {
  top: -10%;
  left: -12%;
  background: var(--color-primary);
  opacity: 0.14;
}

.mission-blob-tertiary {
  top: 10%;
  right: -14%;
  background: var(--color-tertiary);
  opacity: 0.12;
}

/* Styled placeholder blocks standing in for photographs. */
.mission-photo {
  background:
    radial-gradient(120% 120% at 20% 0%, oklch(0.88 0.13 131 / 0.55), transparent 60%),
    linear-gradient(150deg, oklch(0.72 0.09 150) 0%, oklch(0.42 0.07 145) 100%);
}

.mission-photo-grade {
  background: linear-gradient(180deg, transparent 45%, rgb(0 0 0 / 0.28) 100%);
}

.mission-photo-food {
  background:
    radial-gradient(120% 120% at 20% 0%, oklch(0.9 0.13 128 / 0.6), transparent 60%),
    linear-gradient(150deg, oklch(0.74 0.1 135) 0%, oklch(0.44 0.08 140) 100%);
}

.mission-photo-biodiversity {
  background:
    radial-gradient(120% 120% at 20% 0%, oklch(0.85 0.1 165 / 0.6), transparent 60%),
    linear-gradient(150deg, oklch(0.68 0.09 175) 0%, oklch(0.4 0.07 185) 100%);
}

.mission-photo-social {
  background:
    radial-gradient(120% 120% at 20% 0%, oklch(0.86 0.11 70 / 0.6), transparent 60%),
    linear-gradient(150deg, oklch(0.72 0.1 60) 0%, oklch(0.46 0.09 45) 100%);
}

.mission-status-panel {
  background:
    radial-gradient(140% 120% at 0% 0%, oklch(0.88 0.05 150 / 0.12), transparent 55%),
    var(--color-surface-container-low);
}

/* Scroll-reveal. */
.mission-reveal,
.mission-card {
  opacity: 0;
  translate: 0 26px;
  transition:
    opacity 0.7s ease,
    translate 0.7s ease;
}

.mission-card {
  transition-delay: var(--reveal-delay, 0s);
}

.mission-revealed {
  opacity: 1;
  translate: 0 0;
}

@media (prefers-reduced-motion: reduce) {
  .mission-reveal,
  .mission-card {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }

  .mission-blob-primary {
    animation: none;
  }
}
</style>
