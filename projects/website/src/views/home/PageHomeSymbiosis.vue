<template>
  <!--
    Photos (Pexels, free to use, no attribution required):
    #7944396 hands planting, #33352121 gardeners, #7341749 market,
    #7658777 community garden, #12208088 aerial farmland.
  -->
  <section
    ref="root"
    class="symbiosis relative"
  >
    <div class="lg:h-[190svh]">
      <div
        class="relative flex flex-col items-center justify-center gap-12 overflow-hidden px-4 py-24 lg:sticky lg:top-0 lg:h-svh"
      >
        <div
          aria-hidden="true"
          class="symbiosis-blob symbiosis-blob-primary absolute z-0"
        />
        <div
          aria-hidden="true"
          class="symbiosis-blob symbiosis-blob-tertiary absolute z-0"
        />

        <div
          ref="copy"
          class="symbiosis-copy z-10 mx-auto flex max-w-3xl flex-col items-center gap-5 text-center"
          :class="{ 'symbiosis-revealed': revealed }"
        >
          <span
            class="text-primary dark:text-primary text-xs font-bold tracking-[0.2em] uppercase"
            >{{ t('symbiosis.kicker') }}</span
          >
          <Title
            variant="h2"
            class="font-lexend text-4xl font-extrabold tracking-tight md:text-5xl lg:text-6xl"
            >{{ t('symbiosis.title.prepend')
            }}<span
              class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.55_0.12_180)] bg-clip-text text-transparent"
              >{{ t('symbiosis.title.key_word') }}</span
            >{{ t('symbiosis.title.append') }}</Title
          >
          <Paragraph
            variant="website-highlight"
            class="opacity-80"
            >{{ t('symbiosis.description') }}</Paragraph
          >
          <ul class="mt-2 flex flex-wrap items-center justify-center gap-2">
            <li
              v-for="actor in ACTORS"
              :key="actor.key"
              class="border-on-surface/10 bg-surface-container-low flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold"
            >
              <span
                class="size-1.5 rounded-full"
                :class="actor.dotClass"
              />
              {{ t(`actors.${actor.key}.title`) }}
            </li>
          </ul>
        </div>

        <div
          class="z-10 mt-2 grid w-full max-w-lg grid-cols-2 gap-4 lg:static lg:mt-0 lg:max-w-none"
        >
          <figure
            v-for="(card, index) in CARDS"
            :key="card.key"
            class="symbiosis-card ring-on-surface/10 group relative overflow-hidden rounded-2xl shadow-xl ring-1 lg:absolute lg:rounded-3xl"
            :class="[card.positionClass, { 'symbiosis-revealed': revealed }]"
            :style="{
              '--depth': card.depth,
              '--tilt': card.tilt,
              '--drift-x': card.driftX,
              '--reveal-delay': `${0.15 + index * 0.12}s`,
            }"
          >
            <img
              :src="card.image"
              :alt="t(`symbiosis.cards.${card.key}.alt`)"
              loading="lazy"
              class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
              :class="card.imageClass"
            />
            <figcaption
              class="bg-surface/85 text-on-surface absolute bottom-2.5 left-2.5 flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold backdrop-blur-md lg:bottom-3 lg:left-3 lg:text-xs"
            >
              <span
                class="size-1.5 rounded-full"
                :class="card.dotClass"
              />
              {{ t(`symbiosis.cards.${card.key}.label`) }}
            </figcaption>
          </figure>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import handsUrl from './assets/symbiosis-hands.webp';
import womenUrl from './assets/symbiosis-women.webp';
import marketUrl from './assets/symbiosis-market.webp';
import gardenUrl from './assets/symbiosis-garden.webp';
import aerialUrl from './assets/symbiosis-aerial.webp';
import { ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useParallax } from '@/composables/useParallax';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';

const ACTORS = [
  { key: 'farmers', dotClass: 'bg-primary' },
  { key: 'citizens', dotClass: 'bg-tertiary' },
  { key: 'associations', dotClass: 'bg-positive' },
  { key: 'companies', dotClass: 'bg-warning' },
  { key: 'localAuthorities', dotClass: 'bg-secondary' },
  { key: 'researchers', dotClass: 'bg-negative' },
] as const;

const CARDS = [
  {
    key: 'hands',
    image: handsUrl,
    positionClass: 'aspect-[4/3] lg:left-[4%] lg:top-[14%] lg:w-[clamp(180px,16vw,270px)]',
    imageClass: '',
    dotClass: 'bg-primary',
    depth: -150,
    tilt: '-5deg',
    driftX: -14,
  },
  {
    key: 'women',
    image: womenUrl,
    positionClass: 'aspect-[3/4] lg:right-[5%] lg:top-[10%] lg:w-[clamp(170px,14vw,240px)]',
    imageClass: '',
    dotClass: 'bg-tertiary',
    depth: 180,
    tilt: '4deg',
    driftX: 18,
  },
  {
    key: 'market',
    image: marketUrl,
    positionClass: 'aspect-[4/3] lg:bottom-[16%] lg:left-[9%] lg:w-[clamp(200px,17vw,300px)]',
    imageClass: '',
    dotClass: 'bg-warning',
    depth: 130,
    tilt: '3deg',
    driftX: -10,
  },
  {
    key: 'garden',
    image: gardenUrl,
    positionClass: 'aspect-[4/3] lg:bottom-[20%] lg:right-[8%] lg:w-[clamp(210px,18vw,310px)]',
    imageClass: '',
    dotClass: 'bg-positive',
    depth: -210,
    tilt: '-3deg',
    driftX: 12,
  },
  {
    key: 'aerial',
    image: aerialUrl,
    positionClass:
      'hidden aspect-[16/10] lg:left-[42%] lg:top-[5%] lg:block lg:w-[clamp(180px,15vw,260px)]',
    imageClass: '',
    dotClass: 'bg-secondary',
    depth: -90,
    tilt: '2deg',
    driftX: 8,
  },
] as const;

const root = ref<HTMLElement | null>(null);
useParallax(root, { pointer: true });

const copy = ref<HTMLElement | null>(null);
const revealed = ref(false);
useIntersectionObserver(
  copy,
  (entries) => {
    if (entries.some((entry) => entry.isIntersecting)) {
      revealed.value = true;
    }
  },
  { threshold: 0.3 },
);

const { t } = usePrefixedI18n(CONFIG);
</script>

<style scoped>
.symbiosis {
  --scroll-y: 0;
  --center-delta: 0;
  --pointer-x: 0;
  --pointer-y: 0;
}

.symbiosis-blob {
  width: clamp(320px, 42vw, 640px);
  height: clamp(320px, 42vw, 640px);
  border-radius: 9999px;
  filter: blur(80px);
}

.symbiosis-blob-primary {
  top: 8%;
  left: -10%;
  background: var(--color-primary);
  opacity: 0.14;
  transform: translate3d(0, calc(var(--center-delta) * 60px), 0);
}

.symbiosis-blob-tertiary {
  right: -12%;
  bottom: 4%;
  background: var(--color-tertiary);
  opacity: 0.1;
  transform: translate3d(0, calc(var(--center-delta) * -80px), 0);
}

.symbiosis-card {
  transform: translate3d(0, calc(var(--center-delta) * var(--depth, 0) * 0.3px), 0)
    rotate(var(--tilt, 0deg));
}

@media (min-width: 1024px) {
  .symbiosis-card {
    transform: translate3d(
        calc(var(--pointer-x, 0) * var(--drift-x, 0) * 1px),
        calc(var(--center-delta) * var(--depth, 0) * 1px),
        0
      )
      rotate(var(--tilt, 0deg));
    will-change: transform;
  }
}

.symbiosis-copy > *,
.symbiosis-card {
  opacity: 0;
  translate: 0 26px;
  transition:
    opacity 0.7s ease,
    translate 0.7s ease;
}

.symbiosis-copy > *:nth-child(2) {
  transition-delay: 0.1s;
}

.symbiosis-copy > *:nth-child(3) {
  transition-delay: 0.2s;
}

.symbiosis-copy > *:nth-child(4) {
  transition-delay: 0.3s;
}

.symbiosis-card {
  transition-delay: var(--reveal-delay, 0s);
}

.symbiosis-revealed > *,
.symbiosis-card.symbiosis-revealed {
  opacity: 1;
  translate: 0 0;
}

@media (prefers-reduced-motion: reduce) {
  .symbiosis-copy > *,
  .symbiosis-card {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }
}
</style>
