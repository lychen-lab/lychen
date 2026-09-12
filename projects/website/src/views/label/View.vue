<template>
  <!-- ============================================================= HERO -->
  <!-- Photo: Pexels #12208088 (aerial farmland) — free to use, no attribution required. -->
  <DivScalingDown class="flex flex-col justify-start">
    <Hero
      class="relative flex min-h-svh items-center justify-center overflow-hidden"
      overlay
      :background-image="heroFieldsUrl"
      overlay-class="bg-gradient-to-b from-black/55 via-black/20 to-black/65"
    >
      <div
        ref="heroCopy"
        class="relative z-10 flex flex-col items-center gap-9 py-24 md:gap-11"
      >
        <!-- Copy -->
        <div
          class="label-reveal-group flex w-full max-w-3xl flex-col items-center gap-6 text-center"
          :class="{ 'label-revealed': heroRevealed }"
        >
          <span
            class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold tracking-widest text-white/90 uppercase backdrop-blur-md"
          >
            <span class="size-1.5 animate-pulse rounded-full bg-[oklch(0.87_0.12_131)]" />
            {{ t('hero.badge') }}
          </span>

          <Title
            variant="h1"
            class="font-lexend max-w-3xl text-[clamp(2.5rem,6vw,4.75rem)] leading-[1.05] font-extrabold tracking-tight text-balance text-[oklch(0.985_0.01_120)] drop-shadow-[0_2px_24px_rgba(0,0,0,0.45)]"
            >{{ t('hero.title.prepend')
            }}<span
              class="bg-gradient-to-r from-[oklch(0.88_0.13_131)] via-[oklch(0.85_0.11_150)] to-[oklch(0.82_0.1_180)] bg-clip-text text-transparent"
              >{{ t('hero.title.key_word') }}</span
            >{{ t('hero.title.append') }}</Title
          >

          <Paragraph
            variant="website-highlight"
            class="max-w-xl text-white/85 drop-shadow-[0_1px_12px_rgba(0,0,0,0.5)]"
            >{{ t('hero.description') }}</Paragraph
          >

          <div class="mt-1 flex flex-wrap items-center justify-center gap-3">
            <RouterLink to="#scale">
              <Button
                size="lg"
                data-umami-event="label-scale-button"
                :label="t('hero.cta_primary')"
                class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.35)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.5)]"
              >
                {{ t('hero.cta_primary') }}
                <template #icon><IconArrowRight class="size-4" /></template>
              </Button>
            </RouterLink>
            <RouterLink :to="{ name: ROUTE_MISSION.name }">
              <Button
                size="lg"
                variant="ghost"
                data-umami-event="label-mission-button"
                :label="t('hero.cta_secondary')"
                class="border border-white/30 bg-white/10 text-white backdrop-blur-md transition-all duration-300 hover:bg-white/20 hover:text-white"
              />
            </RouterLink>
          </div>
        </div>

        <!-- Scale card — below the copy, superimposed on the hero image -->
        <aside
          class="label-reveal-card border-on-surface/10 bg-surface/85 flex w-full max-w-3xl flex-col gap-5 rounded-3xl border p-6 shadow-2xl backdrop-blur-2xl md:p-8"
          :class="{ 'label-card-revealed': heroRevealed }"
        >
          <div class="flex items-center gap-2">
            <span
              class="bg-primary/12 text-primary flex size-9 items-center justify-center rounded-xl"
            >
              <IconSprout class="size-5" />
            </span>
            <div class="flex flex-col">
              <span class="text-xs font-bold tracking-[0.18em] uppercase opacity-55">{{
                t('scale.kicker')
              }}</span>
              <span class="font-lexend text-lg leading-tight font-extrabold">{{
                t('hero.card_title')
              }}</span>
            </div>
          </div>

          <ScaleBar
            :transition-label="t('tranches.transition.title')"
            :preservation-label="t('tranches.preservation.title')"
            :regeneration-label="t('tranches.regeneration.title')"
          />

          <dl class="grid grid-cols-3 gap-2.5 md:gap-4">
            <div
              v-for="stat in HERO_STATS"
              :key="stat.key"
              class="border-on-surface/5 bg-surface-container-low/70 flex flex-col items-center gap-1 rounded-2xl border px-2 py-3.5 md:py-4"
            >
              <dt
                class="from-primary font-lexend bg-gradient-to-r to-[oklch(0.6_0.12_180)] bg-clip-text text-2xl font-black text-transparent md:text-3xl"
              >
                {{ t(`hero.stats.${stat.key}.value`) }}
              </dt>
              <dd class="text-center text-[11px] leading-tight font-medium opacity-60 md:text-xs">
                {{ t(`hero.stats.${stat.key}.label`) }}
              </dd>
            </div>
          </dl>
        </aside>
      </div>
    </Hero>
  </DivScalingDown>

  <!-- ====================================================== SCALE SECTION -->
  <section
    id="scale"
    class="relative scroll-mt-24 overflow-hidden py-20 md:py-28"
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

        <!-- Continuum scale, full width — the visual backbone of the section. -->
        <div class="mt-2 w-full max-w-2xl">
          <ScaleBar
            :transition-label="t('tranches.transition.title')"
            :preservation-label="t('tranches.preservation.title')"
            :regeneration-label="t('tranches.regeneration.title')"
          />
        </div>
      </div>

      <!-- Three tranches -->
      <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <article
          v-for="(tranche, index) in TRANCHES"
          :key="tranche.key"
          class="label-tranche group border-on-surface/10 relative flex flex-col gap-5 overflow-hidden rounded-3xl border p-6"
          :class="[tranche.surfaceClass, { 'label-tranche-revealed': scaleRevealed }]"
          :style="{ '--reveal-delay': `${0.1 + index * 0.14}s` }"
        >
          <figure class="relative h-44 overflow-hidden rounded-2xl">
            <img
              :src="tranche.image"
              :alt="t(`tranches.${tranche.key}.image_alt`)"
              loading="lazy"
              class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <div
              aria-hidden="true"
              class="absolute inset-0 bg-gradient-to-t from-black/45 via-transparent to-transparent"
            />
            <span
              class="bg-surface/80 text-on-surface absolute top-3 left-3 inline-flex items-center gap-1.5 rounded-full px-2.5 py-1 text-[11px] font-bold backdrop-blur-md"
            >
              <component
                :is="tranche.icon"
                class="size-3.5"
                :class="tranche.accentClass"
              />
              {{ t(`tranches.${tranche.key}.subtitle`) }}
            </span>
            <span
              class="font-lexend absolute right-3 bottom-2 text-4xl font-black text-white/80 drop-shadow-[0_2px_10px_rgba(0,0,0,0.5)]"
            >
              {{ tranche.levels }}
            </span>
          </figure>

          <div class="flex flex-col items-center gap-2 text-center">
            <Title
              variant="h3"
              :class="tranche.accentClass"
              >{{ t(`tranches.${tranche.key}.title`) }}</Title
            >
            <!-- Level chips: a clean replacement for the old shield badges. -->
            <div class="flex flex-wrap justify-center gap-1.5">
              <span
                v-for="level in tranche.badges"
                :key="level"
                class="flex size-7 items-center justify-center rounded-lg text-xs font-bold tabular-nums ring-1 ring-inset"
                :class="tranche.chipClass"
                >{{ level }}</span
              >
            </div>
          </div>

          <Paragraph class="text-center text-sm opacity-80">{{
            t(`tranches.${tranche.key}.description`)
          }}</Paragraph>
        </article>
      </div>
    </Container>
  </section>

  <!-- =============================================== GAMIFICATION SECTION -->
  <section class="relative overflow-hidden py-20 md:py-28">
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
              <LevelBadge
                :level="example.level"
                size="sm"
              />
              <div class="flex flex-col">
                <span class="font-bold">{{ t(`gamification.examples.${i}.role`) }}</span>
                <span class="text-sm opacity-60">{{ t(`gamification.examples.${i}.points`) }}</span>
              </div>
            </li>
          </ul>
        </div>

        <!-- Photo: Pexels #33352121 (community gardeners) — free to use, no attribution required. -->
        <div
          class="label-reveal-figure order-first lg:order-last"
          :class="{ 'label-figure-revealed': gamificationRevealed }"
        >
          <figure
            class="ring-on-surface/10 relative aspect-[4/3] overflow-hidden rounded-3xl shadow-xl ring-1"
          >
            <img
              :src="communityUrl"
              :alt="t('gamification.image_alt')"
              loading="lazy"
              class="size-full object-cover"
            />
            <div
              aria-hidden="true"
              class="absolute inset-0 bg-gradient-to-t from-black/40 via-transparent to-transparent"
            />
            <span
              class="bg-surface/80 text-on-surface absolute bottom-4 left-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold backdrop-blur-md"
            >
              <IconUsers class="text-primary size-3.5" />
              {{ t('gamification.figure_caption') }}
            </span>
            <span
              class="bg-primary absolute top-4 right-4 inline-flex items-center gap-1.5 rounded-full px-3 py-1 text-xs font-bold text-[oklch(0.22_0.06_135)] shadow-lg"
            >
              <IconStar class="size-3.5" />
              {{ t('gamification.score_highlight') }}
            </span>
          </figure>
        </div>
      </div>
    </Container>
  </section>

  <!-- ===================================================== VISION SECTION -->
  <section class="relative overflow-hidden py-20 md:py-28">
    <Container class="relative z-10">
      <div class="grid items-center gap-12 lg:grid-cols-2">
        <!-- Photo: Pexels #7341749 (local market produce) — free to use, no attribution required. -->
        <div
          class="label-reveal-figure"
          :class="{ 'label-figure-revealed': visionRevealed }"
        >
          <figure
            class="ring-on-surface/10 relative aspect-[4/3] overflow-hidden rounded-3xl shadow-xl ring-1"
          >
            <img
              :src="visionProductUrl"
              :alt="t('vision.image_alt')"
              loading="lazy"
              class="size-full object-cover"
            />
            <div
              aria-hidden="true"
              class="absolute inset-0 bg-gradient-to-t from-black/35 via-transparent to-transparent"
            />
            <!-- Product certification sticker — the label as it appears in the field. -->
            <div
              class="border-on-surface/10 bg-surface/90 absolute top-4 left-4 flex items-center gap-3 rounded-2xl border px-3.5 py-2.5 shadow-lg backdrop-blur-md"
            >
              <LevelBadge
                :level="9"
                size="sm"
              />
              <div class="flex flex-col pr-1">
                <span
                  class="inline-flex items-center gap-1 text-[11px] font-semibold tracking-wider uppercase opacity-60"
                >
                  <IconBadgeCheck class="text-primary size-3.5" />
                  {{ t('vision.sticker_label') }}
                </span>
                <span class="font-lexend text-lg leading-tight font-extrabold">{{
                  t('vision.sticker_level')
                }}</span>
              </div>
            </div>
          </figure>
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
import { defineAsyncComponent, ref, type Ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';
import { useHead } from '@unhead/vue';

import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { CONFIG } from './i18n';
import Container from '@lychen/vue-components-website/container/Container.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { useWebPageSchema } from '@/composables/useWebPageSchema';
import { ROUTE_MISSION } from '@/views/mission';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import IconCircleCheck from '@lychen/vue-icons/IconCircleCheck.vue';
import IconEarth from '@lychen/vue-icons/IconEarth.vue';
import IconUsers from '@lychen/vue-icons/IconUsers.vue';
import IconBadgeCheck from '@lychen/vue-icons/IconBadgeCheck.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import IconStar from '@lychen/vue-icons/IconStar.vue';
import LevelBadge from './LevelBadge.vue';
import ScaleBar from './ScaleBar.vue';

import heroFieldsUrl from './assets/hero-fields.webp';
import transitionUrl from './assets/transition.webp';
import preservationUrl from './assets/preservation.webp';
import regenerationUrl from './assets/regeneration.webp';
import communityUrl from './assets/community.webp';
import visionProductUrl from './assets/vision-product.webp';
import ogImageUrl from './assets/LabelOgImage.webp';

const DivScalingDown = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-scaling-down/DivScalingDown.vue'),
);
const Hero = defineAsyncComponent(() => import('@lychen/vue-components-website/hero/Hero.vue'));

const { t } = usePrefixedI18n(CONFIG);
useExtendedHead(t, { ogImage: ogImageUrl });
useWebPageSchema(t, { siteName: 'lychen' });
useHead({
  link: [{ rel: 'preload', as: 'image', href: heroFieldsUrl, fetchpriority: 'high' }],
});

const HERO_STATS = [{ key: 'levels' }, { key: 'tranches' }, { key: 'scope' }] as const;

// Tranche photos (Pexels, free to use, no attribution required):
// #7944396 seedling in soil, #13127129 hedgerow farmland, #89267 market-garden rows.
const TRANCHES = [
  {
    key: 'transition',
    levels: '1–3',
    badges: [1, 2, 3],
    icon: IconSprout,
    image: transitionUrl,
    accentClass: 'text-yellow-600 dark:text-yellow-400',
    chipClass: 'bg-yellow-500/15 text-yellow-700 ring-yellow-500/30 dark:text-yellow-300',
    surfaceClass: 'bg-yellow-50/40 dark:bg-yellow-950/15',
  },
  {
    key: 'preservation',
    levels: '4–7',
    badges: [4, 5, 6, 7],
    icon: IconCircleCheck,
    image: preservationUrl,
    accentClass: 'text-emerald-600 dark:text-emerald-400',
    chipClass: 'bg-emerald-500/15 text-emerald-700 ring-emerald-500/30 dark:text-emerald-300',
    surfaceClass: 'bg-emerald-50/40 dark:bg-emerald-950/15',
  },
  {
    key: 'regeneration',
    levels: '8–10',
    badges: [8, 9, 10],
    icon: IconEarth,
    image: regenerationUrl,
    accentClass: 'text-amber-600 dark:text-amber-400',
    chipClass: 'bg-amber-500/15 text-amber-700 ring-amber-500/35 dark:text-amber-200',
    surfaceClass:
      'bg-amber-50/50 shadow-lg shadow-amber-500/10 dark:bg-amber-950/15 dark:shadow-amber-500/5',
  },
] as const;

const GAMIFICATION_EXAMPLES = [{ level: 6 }, { level: 4 }, { level: 5 }, { level: 8 }] as const;

function useReveal(target: Ref<HTMLElement | null>, threshold = 0.25) {
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
  return revealed;
}

const heroCopy = ref<HTMLElement | null>(null);
const heroRevealed = useReveal(heroCopy, 0.1);
const scaleHeading = ref<HTMLElement | null>(null);
const scaleRevealed = useReveal(scaleHeading, 0.2);
const gamificationCopy = ref<HTMLElement | null>(null);
const gamificationRevealed = useReveal(gamificationCopy, 0.2);
const visionCopy = ref<HTMLElement | null>(null);
const visionRevealed = useReveal(visionCopy, 0.2);
</script>

<style scoped>
/* ----------------------------------------------------- DECORATIVE BLOBS */
.label-blob {
  width: clamp(280px, 38vw, 560px);
  height: clamp(280px, 38vw, 560px);
  border-radius: 9999px;
  filter: blur(90px);
  pointer-events: none;
}

.label-blob-soft {
  background: var(--color-primary);
  opacity: 0.08;
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

/* Scale card: slides up beneath the hero copy. */
.label-reveal-card {
  opacity: 0;
  translate: 0 28px;
  transition:
    opacity 0.8s ease,
    translate 0.8s ease;
  transition-delay: 0.25s;
}

.label-card-revealed {
  opacity: 1;
  translate: 0 0;
}

@media (prefers-reduced-motion: reduce) {
  .label-reveal-group > *,
  .label-tranche,
  .label-reveal-figure,
  .label-reveal-card {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }
}
</style>
