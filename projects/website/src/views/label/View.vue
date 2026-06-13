<template>
  <!-- ============================================================= HERO -->
  <section
    ref="heroRoot"
    class="label-hero relative overflow-hidden"
  >
    <!-- Layered decorative background: gradient wash + blobs -->
    <div
      aria-hidden="true"
      class="label-hero-grade absolute inset-0 z-0"
    />
    <div
      aria-hidden="true"
      class="label-blob label-blob-transition absolute z-0"
    />
    <div
      aria-hidden="true"
      class="label-blob label-blob-preservation absolute z-0"
    />
    <div
      aria-hidden="true"
      class="label-blob label-blob-regeneration absolute z-0"
    />

    <!-- Decorative ridge silhouette at the bottom of the hero -->
    <svg
      aria-hidden="true"
      class="label-hero-ridge absolute inset-x-0 -bottom-px z-[1] h-[22svh] w-full dark:brightness-[0.6]"
      viewBox="0 0 1440 320"
      preserveAspectRatio="none"
      fill="none"
      xmlns="http://www.w3.org/2000/svg"
    >
      <defs>
        <linearGradient
          id="label-hero-ridge-gradient"
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
        d="M0 190 C 140 160, 260 118, 420 130 C 580 142, 660 194, 820 198 C 980 202, 1100 148, 1240 152 C 1320 154, 1400 168, 1440 174 L 1440 320 L 0 320 Z"
        fill="url(#label-hero-ridge-gradient)"
      />
      <path
        d="M0 190 C 140 160, 260 118, 420 130 C 580 142, 660 194, 820 198 C 980 202, 1100 148, 1240 152 C 1320 154, 1400 168, 1440 174"
        stroke="oklch(0.95 0.03 140 / 0.2)"
        stroke-width="1.5"
        vector-effect="non-scaling-stroke"
      />
    </svg>

    <Container class="relative z-10">
      <div
        ref="heroCopy"
        class="label-reveal-group mx-auto flex max-w-4xl flex-col items-center gap-6 pt-28 pb-32 text-center md:pt-36"
        :class="{ 'label-revealed': heroRevealed }"
      >
        <span
          class="border-on-surface/15 bg-surface-container-low/70 inline-flex items-center gap-2 rounded-full border px-4 py-1.5 text-xs font-semibold tracking-widest uppercase backdrop-blur-md"
        >
          <span class="bg-primary size-1.5 animate-pulse rounded-full" />
          {{ t('hero.badge') }}
        </span>

        <Title
          variant="h1"
          class="font-lexend max-w-4xl text-[clamp(2.5rem,6vw,4.75rem)] leading-[1.06] font-extrabold tracking-tight text-balance"
          >{{ t('hero.title.prepend')
          }}<span
            class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.6_0.12_180)] bg-clip-text text-transparent dark:from-[oklch(0.88_0.13_131)] dark:via-[oklch(0.85_0.11_150)] dark:to-[oklch(0.82_0.1_180)]"
            >{{ t('hero.title.key_word') }}</span
          >{{ t('hero.title.append') }}</Title
        >

        <Paragraph
          variant="website-highlight"
          class="max-w-2xl opacity-80"
          >{{ t('hero.description') }}</Paragraph
        >

        <!-- Scale hint chip -->
        <span
          class="border-on-surface/10 bg-surface-container-low/60 inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold backdrop-blur-md"
        >
          <IconSprout class="text-primary size-4" />
          {{ t('hero.scale_hint') }}
        </span>

        <!-- Stats row -->
        <dl class="mt-4 grid w-full max-w-2xl grid-cols-3 gap-4">
          <div
            v-for="stat in HERO_STATS"
            :key="stat.key"
            class="border-on-surface/10 bg-surface-container-low/50 flex flex-col items-center gap-1 rounded-2xl border px-3 py-5 backdrop-blur-sm"
          >
            <dt
              class="from-primary bg-gradient-to-r to-[oklch(0.6_0.12_180)] bg-clip-text font-lexend text-3xl font-black text-transparent md:text-4xl"
            >
              {{ t(`hero.stats.${stat.key}.value`) }}
            </dt>
            <dd class="text-xs font-medium opacity-60">
              {{ t(`hero.stats.${stat.key}.label`) }}
            </dd>
          </div>
        </dl>
      </div>
    </Container>
  </section>

  <!-- ====================================================== SCALE SECTION -->
  <section
    ref="scaleRoot"
    class="relative overflow-hidden py-20 md:py-28"
  >
    <div
      aria-hidden="true"
      class="label-blob label-blob-soft absolute top-10 -left-24 z-0"
    />

    <Container class="relative z-10 flex flex-col gap-14">
      <!-- Section heading -->
      <div
        ref="scaleHeading"
        class="label-reveal-group mx-auto flex max-w-3xl flex-col items-center gap-5 text-center"
        :class="{ 'label-revealed': scaleRevealed }"
      >
        <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
          t('scale.kicker')
        }}</span>
        <Title
          variant="h2"
          class="font-lexend text-4xl font-extrabold tracking-tight md:text-5xl"
          >{{ t('scale.title.prepend')
          }}<span
            class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.55_0.12_180)] bg-clip-text text-transparent"
            >{{ t('scale.title.key_word') }}</span
          >{{ t('scale.title.append') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="opacity-80"
          >{{ t('scale.description') }}</Paragraph
        >
      </div>

      <!-- Three tranches -->
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <article
          v-for="(tranche, index) in TRANCHES"
          :key="tranche.key"
          ref="trancheCards"
          class="label-tranche group border-on-surface/10 relative flex flex-col gap-5 overflow-hidden rounded-3xl border p-6"
          :class="[tranche.surfaceClass, { 'label-tranche-revealed': scaleRevealed }]"
          :style="{ '--reveal-delay': `${0.1 + index * 0.14}s` }"
        >
          <!--
            IMAGE PLACEHOLDER: tranche illustration
            (transition = field starting conversion, preservation = balanced
            plots with groves/ponds, regeneration = thriving agroecological
            farm seen from above). Midjourney prompts in ClickUp.
          -->
          <div
            aria-hidden="true"
            class="label-placeholder relative flex h-40 items-end justify-between overflow-hidden rounded-2xl p-4"
            :class="tranche.placeholderClass"
          >
            <span class="label-placeholder-grain absolute inset-0" />
            <span
              class="bg-surface/70 text-on-surface relative z-10 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold backdrop-blur-md"
            >
              <component
                :is="tranche.icon"
                class="size-3.5"
                :class="tranche.accentClass"
              />
              {{ t(`tranches.${tranche.key}.subtitle`) }}
            </span>
            <span class="relative z-10 font-lexend text-5xl font-black text-white/35">
              {{ tranche.levels }}
            </span>
          </div>

          <div class="flex flex-col items-center gap-1.5 text-center">
            <Title
              variant="h3"
              :class="tranche.accentClass"
              >{{ t(`tranches.${tranche.key}.title`) }}</Title
            >
            <span class="text-sm opacity-60">{{ t(`tranches.${tranche.key}.subtitle`) }}</span>
          </div>

          <div class="flex flex-wrap justify-center gap-4">
            <RobustLabel
              v-for="level in tranche.badges"
              :key="level"
              :level="level"
            />
          </div>

          <Paragraph class="text-center text-sm opacity-80">{{
            t(`tranches.${tranche.key}.description`)
          }}</Paragraph>
        </article>
      </div>
    </Container>
  </section>

  <!-- =============================================== GAMIFICATION SECTION -->
  <section
    ref="gamificationRoot"
    class="relative overflow-hidden py-20 md:py-28"
  >
    <div
      aria-hidden="true"
      class="label-blob label-blob-soft absolute -right-24 bottom-0 z-0"
    />

    <Container class="relative z-10">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <!-- Text + scores -->
        <div
          ref="gamificationCopy"
          class="label-reveal-group flex flex-col gap-6"
          :class="{ 'label-revealed': gamificationRevealed }"
        >
          <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
            t('gamification.kicker')
          }}</span>
          <Title
            variant="h2"
            class="font-lexend text-3xl font-extrabold tracking-tight md:text-4xl"
            >{{ t('gamification.title') }}</Title
          >
          <Paragraph class="opacity-80">{{ t('gamification.description') }}</Paragraph>

          <span class="text-sm font-bold opacity-70">{{ t('gamification.examples_title') }}</span>
          <ul class="flex flex-col gap-3">
            <li
              v-for="(example, i) in GAMIFICATION_EXAMPLES"
              :key="i"
              class="border-on-surface/10 bg-surface-container-low/60 flex items-center gap-4 rounded-2xl border p-3 backdrop-blur-sm transition-transform duration-300 hover:translate-x-1"
            >
              <RobustLabel
                :level="example.level"
                size="sm"
                class="scale-75"
              />
              <div class="flex flex-col">
                <span class="font-bold">{{ t(`gamification.examples.${i}.role`) }}</span>
                <span class="text-sm opacity-60">{{ t(`gamification.examples.${i}.points`) }}</span>
              </div>
            </li>
          </ul>
        </div>

        <!--
          IMAGE PLACEHOLDER: local community celebrating their territory's
          progress around a scoreboard. Midjourney prompt in ClickUp.
        -->
        <div
          ref="gamificationFigure"
          class="label-reveal-figure order-first lg:order-last"
          :class="{ 'label-figure-revealed': gamificationRevealed }"
        >
          <div
            aria-hidden="true"
            class="label-placeholder label-placeholder-community ring-on-surface/10 relative flex aspect-[4/3] items-end overflow-hidden rounded-3xl shadow-xl ring-1"
          >
            <span class="label-placeholder-grain absolute inset-0" />
            <span
              class="bg-surface/70 text-on-surface relative z-10 m-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold backdrop-blur-md"
            >
              <IconUsers class="text-primary size-3.5" />
              {{ t('gamification.image_alt') }}
            </span>
          </div>
        </div>
      </div>
    </Container>
  </section>

  <!-- ===================================================== VISION SECTION -->
  <section
    ref="visionRoot"
    class="relative overflow-hidden py-20 md:py-28"
  >
    <Container class="relative z-10">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <!--
          IMAGE PLACEHOLDER: a Robust label proudly displayed on a local
          product, in soft natural light. Midjourney prompt in ClickUp.
        -->
        <div
          ref="visionFigure"
          class="label-reveal-figure"
          :class="{ 'label-figure-revealed': visionRevealed }"
        >
          <div
            aria-hidden="true"
            class="label-placeholder label-placeholder-product ring-on-surface/10 relative flex aspect-[4/3] items-end overflow-hidden rounded-3xl shadow-xl ring-1"
          >
            <span class="label-placeholder-grain absolute inset-0" />
            <RobustLabel
              :level="9"
              size="lg"
              class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2"
            />
            <span
              class="bg-surface/70 text-on-surface relative z-10 m-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold backdrop-blur-md"
            >
              <IconBadgeCheck class="text-primary size-3.5" />
              {{ t('vision.image_alt') }}
            </span>
          </div>
        </div>

        <div
          ref="visionCopy"
          class="label-reveal-group flex flex-col gap-6"
          :class="{ 'label-revealed': visionRevealed }"
        >
          <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
            t('vision.kicker')
          }}</span>
          <Title
            variant="h2"
            class="font-lexend text-3xl font-extrabold tracking-tight md:text-4xl"
            >{{ t('vision.title') }}</Title
          >
          <Paragraph class="opacity-80">{{ t('vision.description') }}</Paragraph>
        </div>
      </div>
    </Container>
  </section>
</template>

<script lang="ts" setup>
import { ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import RobustLabel from './RobustLabel.vue';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import IconShieldCheck from '@lychen/vue-icons/IconShieldCheck.vue';
import IconEarth from '@lychen/vue-icons/IconEarth.vue';
import IconUsers from '@lychen/vue-icons/IconUsers.vue';
import IconBadgeCheck from '@lychen/vue-icons/IconBadgeCheck.vue';

const { t } = usePrefixedI18n(CONFIG);
useExtendedHead(t);

const HERO_STATS = [{ key: 'levels' }, { key: 'tranches' }, { key: 'scope' }] as const;

const TRANCHES = [
  {
    key: 'transition',
    levels: '1–3',
    badges: [1, 2, 3],
    icon: IconSprout,
    accentClass: 'text-yellow-600 dark:text-yellow-400',
    surfaceClass: 'bg-yellow-50/40 dark:bg-yellow-950/15',
    placeholderClass: 'label-placeholder-transition',
  },
  {
    key: 'preservation',
    levels: '4–7',
    badges: [4, 5, 6, 7],
    icon: IconShieldCheck,
    accentClass: 'text-emerald-600 dark:text-emerald-400',
    surfaceClass: 'bg-emerald-50/40 dark:bg-emerald-950/15',
    placeholderClass: 'label-placeholder-preservation',
  },
  {
    key: 'regeneration',
    levels: '8–10',
    badges: [8, 9, 10],
    icon: IconEarth,
    accentClass: 'text-amber-600 dark:text-amber-400',
    surfaceClass:
      'bg-amber-50/50 shadow-lg shadow-amber-500/10 dark:bg-amber-950/15 dark:shadow-amber-500/5',
    placeholderClass: 'label-placeholder-regeneration',
  },
] as const;

const GAMIFICATION_EXAMPLES = [
  { level: 6 },
  { level: 4 },
  { level: 5 },
  { level: 8 },
] as const;

function useReveal(threshold = 0.25) {
  const target = ref<HTMLElement | null>(null);
  const revealed = ref(false);
  useIntersectionObserver(
    target,
    (entries) => {
      if (entries.some((entry) => entry.isIntersecting)) {
        revealed.value = true;
      }
    },
    { threshold },
  );
  return { target, revealed };
}

const { target: heroCopy, revealed: heroRevealed } = useReveal(0.1);
const { target: scaleHeading, revealed: scaleRevealed } = useReveal(0.2);
const { target: gamificationCopy, revealed: gamificationRevealed } = useReveal(0.2);
const { target: visionCopy, revealed: visionRevealed } = useReveal(0.2);

// Refs used only as scroll/animation anchors (kept for clarity & future parallax).
const heroRoot = ref<HTMLElement | null>(null);
const scaleRoot = ref<HTMLElement | null>(null);
const trancheCards = ref<HTMLElement[] | null>(null);
const gamificationRoot = ref<HTMLElement | null>(null);
const gamificationFigure = ref<HTMLElement | null>(null);
const visionRoot = ref<HTMLElement | null>(null);
const visionFigure = ref<HTMLElement | null>(null);
</script>

<style scoped>
/* ---------------------------------------------------------------- HERO */
.label-hero-grade {
  background:
    radial-gradient(80% 60% at 50% 0%, oklch(0.7 0.08 150 / 0.18), transparent 70%),
    linear-gradient(180deg, oklch(0.95 0.02 150 / 0.5) 0%, transparent 60%);
}

:global(.dark) .label-hero-grade {
  background:
    radial-gradient(80% 60% at 50% 0%, oklch(0.6 0.1 150 / 0.18), transparent 70%),
    linear-gradient(180deg, oklch(0.3 0.06 150 / 0.35) 0%, transparent 60%);
}

/* ----------------------------------------------------- DECORATIVE BLOBS */
.label-blob {
  width: clamp(280px, 38vw, 560px);
  height: clamp(280px, 38vw, 560px);
  border-radius: 9999px;
  filter: blur(90px);
  pointer-events: none;
}

.label-blob-transition {
  top: -8%;
  left: -6%;
  background: oklch(0.85 0.13 95);
  opacity: 0.14;
}

.label-blob-preservation {
  top: 10%;
  right: -8%;
  background: oklch(0.7 0.13 150);
  opacity: 0.12;
}

.label-blob-regeneration {
  bottom: -10%;
  left: 30%;
  background: oklch(0.78 0.13 75);
  opacity: 0.1;
}

.label-blob-soft {
  background: var(--color-primary);
  opacity: 0.08;
}

/* ------------------------------------------------------ IMAGE PLACEHOLDERS */
.label-placeholder {
  background-position: center;
  background-size: cover;
}

/* Subtle grain/texture overlay shared by every placeholder block */
.label-placeholder-grain {
  background-image: radial-gradient(rgb(255 255 255 / 0.16) 1px, transparent 1.4px);
  background-size: 14px 14px;
  mix-blend-mode: overlay;
  opacity: 0.5;
}

.label-placeholder-transition {
  background-image: linear-gradient(
    135deg,
    oklch(0.82 0.13 95) 0%,
    oklch(0.72 0.12 120) 60%,
    oklch(0.6 0.11 135) 100%
  );
}

.label-placeholder-preservation {
  background-image: linear-gradient(
    135deg,
    oklch(0.74 0.12 150) 0%,
    oklch(0.6 0.12 160) 55%,
    oklch(0.46 0.1 165) 100%
  );
}

.label-placeholder-regeneration {
  background-image: linear-gradient(
    135deg,
    oklch(0.8 0.13 80) 0%,
    oklch(0.66 0.13 110) 55%,
    oklch(0.5 0.12 150) 100%
  );
}

.label-placeholder-community {
  background-image: linear-gradient(
    140deg,
    oklch(0.74 0.11 150) 0%,
    oklch(0.58 0.12 165) 50%,
    oklch(0.44 0.1 175) 100%
  );
}

.label-placeholder-product {
  background-image: linear-gradient(
    140deg,
    oklch(0.85 0.1 95) 0%,
    oklch(0.7 0.12 130) 55%,
    oklch(0.52 0.11 155) 100%
  );
}

/* --------------------------------------------------- SCROLL REVEALS */
/* Grouped copy: children fade/slide up with a small stagger. */
.label-reveal-group > * {
  opacity: 0;
  translate: 0 26px;
  transition:
    opacity 0.7s ease,
    translate 0.7s ease;
}

.label-reveal-group > *:nth-child(2) {
  transition-delay: 0.1s;
}

.label-reveal-group > *:nth-child(3) {
  transition-delay: 0.2s;
}

.label-reveal-group > *:nth-child(4) {
  transition-delay: 0.3s;
}

.label-reveal-group > *:nth-child(5) {
  transition-delay: 0.4s;
}

.label-reveal-group > *:nth-child(6) {
  transition-delay: 0.5s;
}

.label-revealed > * {
  opacity: 1;
  translate: 0 0;
}

/* Tranche cards: staggered reveal driven by --reveal-delay. */
.label-tranche {
  opacity: 0;
  translate: 0 32px;
  transition:
    opacity 0.7s ease,
    translate 0.7s ease;
  transition-delay: var(--reveal-delay, 0s);
}

.label-tranche-revealed {
  opacity: 1;
  translate: 0 0;
}

/* Figures slide in from the side. */
.label-reveal-figure {
  opacity: 0;
  translate: 28px 0;
  transition:
    opacity 0.8s ease,
    translate 0.8s ease;
}

.label-figure-revealed {
  opacity: 1;
  translate: 0 0;
}

@media (prefers-reduced-motion: reduce) {
  .label-reveal-group > *,
  .label-tranche,
  .label-reveal-figure {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }
}
</style>
