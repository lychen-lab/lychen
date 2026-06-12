<template>
  <!-- Photo: Pexels #1834402 (Eberhard Grossgasteiger) — free to use, no attribution required. -->
  <section
    ref="root"
    class="hero relative h-svh min-h-[640px] w-full overflow-hidden"
  >
    <img
      :src="heroBackgroundUrl"
      alt=""
      aria-hidden="true"
      fetchpriority="high"
      class="hero-layer-photo absolute inset-0 z-0 size-full object-cover"
    />
    <div
      aria-hidden="true"
      class="hero-grade absolute inset-0 z-[1]"
    />
    <div
      aria-hidden="true"
      class="hero-contrast absolute inset-0 z-[2] dark:opacity-90"
    />

    <svg
      aria-hidden="true"
      class="hero-ridge hero-ridge-far absolute inset-x-0 bottom-[15svh] z-[3] h-[32svh] w-full dark:brightness-[0.55]"
      viewBox="0 0 1440 320"
      preserveAspectRatio="none"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient
          id="hero-ridge-gradient-far"
          x1="0"
          y1="0"
          x2="0"
          y2="1"
        >
          <stop
            offset="0"
            stop-color="oklch(0.62 0.05 150)"
          />
          <stop
            offset="1"
            stop-color="oklch(0.45 0.055 145)"
          />
        </linearGradient>
      </defs>
      <path
        d="M0 170 C 110 150, 230 112, 360 120 C 490 128, 560 178, 690 186 C 820 194, 900 148, 1020 140 C 1140 132, 1240 162, 1340 170 C 1380 173, 1410 174, 1440 174 L 1440 320 L 0 320 Z"
        fill="url(#hero-ridge-gradient-far)"
      />
      <path
        d="M0 170 C 110 150, 230 112, 360 120 C 490 128, 560 178, 690 186 C 820 194, 900 148, 1020 140 C 1140 132, 1240 162, 1340 170 C 1380 173, 1410 174, 1440 174"
        stroke="oklch(0.95 0.02 140 / 0.22)"
        stroke-width="1.5"
        vector-effect="non-scaling-stroke"
      />
    </svg>

    <div
      aria-hidden="true"
      class="hero-mist hero-mist-far absolute bottom-[13svh] z-[4] h-[15svh] dark:opacity-40"
    />

    <svg
      aria-hidden="true"
      class="hero-ridge hero-ridge-mid absolute inset-x-0 bottom-[6svh] z-[5] h-[28svh] w-full dark:brightness-[0.55]"
      viewBox="0 0 1440 320"
      preserveAspectRatio="none"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient
          id="hero-ridge-gradient-mid"
          x1="0"
          y1="0"
          x2="0"
          y2="1"
        >
          <stop
            offset="0"
            stop-color="oklch(0.46 0.065 144)"
          />
          <stop
            offset="1"
            stop-color="oklch(0.31 0.06 142)"
          />
        </linearGradient>
      </defs>
      <path
        d="M0 200 C 100 180, 190 134, 320 140 C 450 146, 510 196, 640 204 C 770 212, 860 162, 980 152 C 1100 142, 1200 180, 1300 192 C 1350 198, 1400 204, 1440 206 L 1440 320 L 0 320 Z"
        fill="url(#hero-ridge-gradient-mid)"
      />
      <path
        d="M0 200 C 100 180, 190 134, 320 140 C 450 146, 510 196, 640 204 C 770 212, 860 162, 980 152 C 1100 142, 1200 180, 1300 192 C 1350 198, 1400 204, 1440 206"
        stroke="oklch(0.95 0.02 140 / 0.16)"
        stroke-width="1.5"
        vector-effect="non-scaling-stroke"
      />
    </svg>

    <div
      aria-hidden="true"
      class="hero-mist hero-mist-near absolute bottom-[4svh] z-[6] h-[13svh] dark:opacity-30"
    />

    <div
      aria-hidden="true"
      class="hero-spores absolute inset-0 z-[7]"
    >
      <span
        v-for="spore in SPORES"
        :key="spore.id"
        class="hero-spore absolute rounded-full"
        :style="{
          left: spore.left,
          top: spore.top,
          width: `${spore.size}px`,
          height: `${spore.size}px`,
          '--spore-delay': spore.delay,
          '--spore-duration': spore.duration,
          '--spore-drift': spore.drift,
        }"
      />
    </div>

    <div
      aria-hidden="true"
      class="hero-vignette pointer-events-none absolute inset-0 z-[8]"
    />

    <svg
      aria-hidden="true"
      class="hero-ridge hero-ridge-near absolute inset-x-0 -bottom-px z-10 h-[26svh] w-full dark:brightness-50"
      viewBox="0 0 1440 320"
      preserveAspectRatio="none"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient
          id="hero-ridge-gradient-near"
          x1="0"
          y1="0"
          x2="0"
          y2="1"
        >
          <stop
            offset="0"
            stop-color="oklch(0.27 0.06 140)"
          />
          <stop
            offset="1"
            stop-color="oklch(0.14 0.04 138)"
          />
        </linearGradient>
      </defs>
      <path
        d="M0 190 C 140 160, 260 118, 420 130 C 580 142, 660 194, 820 198 C 980 202, 1100 148, 1240 152 C 1320 154, 1400 168, 1440 174 L 1440 320 L 0 320 Z"
        fill="url(#hero-ridge-gradient-near)"
      />
      <path
        d="M0 190 C 140 160, 260 118, 420 130 C 580 142, 660 194, 820 198 C 980 202, 1100 148, 1240 152 C 1320 154, 1400 168, 1440 174"
        stroke="oklch(0.95 0.03 140 / 0.2)"
        stroke-width="1.5"
        vector-effect="non-scaling-stroke"
      />
    </svg>

    <div class="hero-content absolute inset-0 z-20 flex items-center justify-center">
      <div class="container flex flex-col items-center gap-5 text-center">
        <span
          class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold tracking-widest text-white/90 uppercase backdrop-blur-md"
        >
          <span class="size-1.5 animate-pulse rounded-full bg-[oklch(0.87_0.12_131)]" />
          {{ t('hero.badge') }}
        </span>
        <Title
          variant="h1"
          class="font-lexend max-w-5xl text-[clamp(2.75rem,7vw,5.5rem)] leading-[1.05] font-extrabold tracking-tight text-[oklch(0.985_0.01_120)] drop-shadow-[0_2px_24px_rgba(0,0,0,0.45)]"
          >{{ t('hero.title.prepend')
          }}<span
            class="bg-gradient-to-r from-[oklch(0.88_0.13_131)] via-[oklch(0.85_0.11_150)] to-[oklch(0.82_0.1_180)] bg-clip-text text-transparent"
            >{{ t('hero.title.key_word') }}</span
          >{{ t('hero.title.append') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="max-w-2xl text-white/85 drop-shadow-[0_1px_12px_rgba(0,0,0,0.5)]"
          >{{ t('hero.description') }}
        </Paragraph>
        <div class="mt-2 flex flex-wrap items-center justify-center gap-3">
          <RouterLink to="#discover">
            <Button
              size="lg"
              data-umami-event="discover-button"
              :label="t('hero.discover')"
              class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.35)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.5)]"
            />
          </RouterLink>
          <RouterLink :to="{ name: ROUTE_MISSION.name }">
            <Button
              size="lg"
              variant="ghost"
              data-umami-event="hero-mission-button"
              :label="t('hero.mission')"
              class="border border-white/30 bg-white/10 text-white backdrop-blur-md transition-all duration-300 hover:bg-white/20 hover:text-white"
            />
          </RouterLink>
        </div>
      </div>
    </div>

    <RouterLink
      to="#discover"
      :aria-label="t('hero.scroll_hint')"
      class="hero-scroll-hint-link absolute bottom-6 left-1/2 z-30 -translate-x-1/2"
    >
      <span
        class="hero-scroll-hint flex size-11 items-center justify-center rounded-full border border-white/25 bg-white/10 backdrop-blur-md"
      >
        <IconChevronDown class="size-5 text-white/90" />
      </span>
    </RouterLink>

    <div
      aria-hidden="true"
      class="hero-blend pointer-events-none absolute inset-x-0 bottom-0 z-30 h-[14svh]"
    />
  </section>
</template>

<script setup lang="ts">
import heroBackgroundUrl from './assets/parallax-hero.webp';
import { ref } from 'vue';
import { useHead } from '@unhead/vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useParallax } from '@/composables/useParallax';
import { ROUTE_MISSION } from '@/views/mission';
import Button from '@lychen/vue-components-core/button/Button.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import IconChevronDown from '@lychen/vue-icons/IconChevronDown.vue';

// Deterministic positions: generated once so SSR markup and hydration match.
const SPORES = [
  { id: 1, left: '8%', top: '64%', size: 5, delay: '0s', duration: '11s', drift: '26px' },
  { id: 2, left: '16%', top: '46%', size: 3, delay: '2.4s', duration: '13s', drift: '-18px' },
  { id: 3, left: '24%', top: '70%', size: 4, delay: '5.1s', duration: '10s', drift: '12px' },
  { id: 4, left: '33%', top: '55%', size: 3, delay: '1.2s', duration: '14s', drift: '-24px' },
  { id: 5, left: '42%', top: '74%', size: 5, delay: '7.8s', duration: '12s', drift: '20px' },
  { id: 6, left: '55%', top: '68%', size: 3, delay: '3.6s', duration: '11s', drift: '-14px' },
  { id: 7, left: '63%', top: '50%', size: 4, delay: '6.3s', duration: '15s', drift: '22px' },
  { id: 8, left: '72%', top: '72%', size: 5, delay: '0.9s', duration: '10s', drift: '-20px' },
  { id: 9, left: '81%', top: '58%', size: 3, delay: '4.5s', duration: '13s', drift: '16px' },
  { id: 10, left: '89%', top: '66%', size: 4, delay: '8.7s', duration: '12s', drift: '-26px' },
] as const;

const root = ref<HTMLElement | null>(null);
useParallax(root, { pointer: true });

const { t } = usePrefixedI18n(CONFIG);

useHead({
  link: [{ rel: 'preload', as: 'image', href: heroBackgroundUrl }],
});
</script>

<style scoped>
.hero {
  --scroll-y: 0;
  --center-delta: 0;
  --pointer-x: 0;
  --pointer-y: 0;
}

.hero-layer-photo {
  transform: translate3d(
      calc(var(--pointer-x) * -14px),
      calc(var(--scroll-y) * 0.42px + var(--pointer-y) * -10px),
      0
    )
    scale(1.12);
  will-change: transform;
}

.hero-grade {
  background: linear-gradient(
    180deg,
    oklch(0.6 0.07 160 / 0.35) 0%,
    oklch(0.45 0.09 145 / 0.5) 55%,
    oklch(0.3 0.08 140 / 0.6) 100%
  );
  mix-blend-mode: color;
}

.hero-contrast {
  background: linear-gradient(
    180deg,
    rgb(0 0 0 / 0.42) 0%,
    rgb(0 0 0 / 0.08) 38%,
    rgb(0 0 0 / 0.3) 100%
  );
}

.hero-vignette {
  background: radial-gradient(120% 90% at 50% 32%, transparent 55%, rgb(0 0 0 / 0.4) 100%);
  mask-image: linear-gradient(180deg, black 72%, transparent 100%);
}

.hero-ridge {
  will-change: transform;
}

.hero-ridge-far {
  transform: translate3d(calc(var(--pointer-x) * -8px), calc(var(--scroll-y) * 0.32px), 0)
    scale(1.06);
}

.hero-ridge-mid {
  transform: translate3d(calc(var(--pointer-x) * 7px), calc(var(--scroll-y) * 0.18px), 0)
    scale(1.06);
}

.hero-ridge-near {
  transform: translate3d(calc(var(--pointer-x) * 16px), calc(var(--scroll-y) * 0.05px), 0)
    scale(1.08);
}

.hero-mist {
  left: -20%;
  width: 140%;
  background: radial-gradient(60% 100% at 50% 50%, rgb(255 255 255 / 0.5), transparent 70%);
  filter: blur(28px);
  will-change: transform;
}

.hero-mist-far {
  opacity: 0.42;
  transform: translate3d(0, calc(var(--scroll-y) * 0.26px), 0);
}

.hero-mist-near {
  opacity: 0.28;
  transform: translate3d(0, calc(var(--scroll-y) * 0.12px), 0);
}

.hero-content {
  transform: translate3d(0, calc(var(--scroll-y) * 0.36px), 0);
  opacity: calc(1 - var(--scroll-y) / 520);
  will-change: transform, opacity;
}

.hero-blend {
  background: linear-gradient(180deg, transparent 0%, var(--color-surface) 82%);
}

.hero-spore {
  background: oklch(0.92 0.1 128 / 0.9);
  box-shadow: 0 0 10px 2px oklch(0.9 0.14 128 / 0.55);
  opacity: 0;
}

.hero-scroll-hint-link {
  opacity: calc(1 - var(--scroll-y) / 240);
}

.hero-scroll-hint {
  animation: hero-scroll-bounce 2.4s ease-in-out infinite;
}

@media (prefers-reduced-motion: no-preference) {
  .hero-mist-far {
    animation: hero-mist-drift 26s ease-in-out infinite alternate;
  }

  .hero-mist-near {
    animation: hero-mist-drift 34s ease-in-out infinite alternate-reverse;
  }

  .hero-spore {
    animation: hero-spore-rise var(--spore-duration) linear infinite;
    animation-delay: var(--spore-delay);
  }
}

@media (prefers-reduced-motion: reduce) {
  .hero-scroll-hint {
    animation: none;
  }
}

@keyframes hero-mist-drift {
  from {
    margin-left: -3%;
  }
  to {
    margin-left: 3%;
  }
}

@keyframes hero-spore-rise {
  0% {
    opacity: 0;
    transform: translate3d(0, 14px, 0) scale(0.6);
  }
  12% {
    opacity: 0.9;
  }
  60% {
    opacity: 0.5;
  }
  100% {
    opacity: 0;
    transform: translate3d(var(--spore-drift), -110px, 0) scale(1);
  }
}

@keyframes hero-scroll-bounce {
  0%,
  100% {
    transform: translate3d(0, 0, 0);
  }
  50% {
    transform: translate3d(0, 8px, 0);
  }
}
</style>
