<template>
  <!--
    Photos (Pexels, free to use, no attribution required):
    #31882650 valley hero, #10912895 hand & seedling, #16798032 countryside,
    #33438410 vegetable crate, #8136318 bee on wildflowers, #5029923 tree planting,
    #38013987 aerial fields, #1368384 misty hills, #29716080 lichen, #700460 sunset hills.
  -->

  <!-- HERO — scales down & rounds on scroll (DivScalingDown) -->
  <DivScalingDown class="flex flex-col justify-start">
    <Hero
      class="relative flex min-h-[88svh] items-center justify-center overflow-hidden md:min-h-[92svh]"
      overlay
      :background-image="heroValleyUrl"
      overlay-class="bg-gradient-to-b from-black/55 via-black/15 to-black/65"
    >
      <div class="relative z-10 flex flex-col items-center gap-6 py-24 text-center">
        <span
          class="inline-flex items-center gap-2 rounded-full border border-white/25 bg-white/10 px-4 py-1.5 text-xs font-semibold tracking-widest text-white/90 uppercase backdrop-blur-md"
        >
          <span class="size-1.5 animate-pulse rounded-full bg-[oklch(0.87_0.12_131)]" />
          {{ t('hero.badge') }}
        </span>

        <Title
          variant="h1"
          class="font-lexend max-w-5xl text-[clamp(2.75rem,7vw,5.5rem)] leading-[1.04] font-extrabold tracking-tight text-[oklch(0.985_0.01_120)] drop-shadow-[0_2px_24px_rgba(0,0,0,0.45)]"
          >{{ t('hero.titlePrepend')
          }}<span
            class="bg-gradient-to-r from-[oklch(0.88_0.13_131)] via-[oklch(0.85_0.11_150)] to-[oklch(0.82_0.1_180)] bg-clip-text text-transparent"
            >{{ t('hero.titleHighlight') }}</span
          >{{ t('hero.titleAppend') }}</Title
        >

        <Paragraph
          variant="website-highlight"
          class="max-w-2xl text-white/85 drop-shadow-[0_1px_12px_rgba(0,0,0,0.5)]"
          >{{ t('hero.subtitle') }}</Paragraph
        >

        <div class="mt-2 flex flex-wrap items-center justify-center gap-3">
          <RouterLink to="#pillars">
            <Button
              size="lg"
              data-umami-event="mission-hero-pillars"
              :label="t('hero.primaryCta')"
              class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.35)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.5)]"
            >
              <template #icon><IconArrowRight class="size-4" /></template>
            </Button>
          </RouterLink>
        </div>
      </div>

      <RouterLink
        to="#conviction"
        :aria-label="t('hero.scrollHint')"
        class="mission-scroll-hint absolute bottom-6 left-1/2 z-10 -translate-x-1/2"
      >
        <span
          class="flex size-11 items-center justify-center rounded-full border border-white/25 bg-white/10 backdrop-blur-md"
        >
          <IconChevronDown class="size-5 text-white/90" />
        </span>
      </RouterLink>
    </Hero>
  </DivScalingDown>

  <!-- CONVICTION — text + image collage -->
  <section
    id="conviction"
    class="relative scroll-mt-24 overflow-hidden"
  >
    <div
      aria-hidden="true"
      class="mission-blob mission-blob-primary absolute top-[-10%] left-[-12%]"
    />
    <div
      class="relative container grid items-center gap-12 py-20 md:grid-cols-2 md:py-28 lg:gap-16"
    >
      <div
        ref="convictionRef"
        class="reveal-group flex flex-col items-start gap-5"
        :class="{ 'is-visible': convictionVisible }"
      >
        <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
          t('conviction.kicker')
        }}</span>
        <Title
          variant="h2"
          class="font-lexend text-4xl font-extrabold tracking-tight md:text-5xl"
          >{{ t('conviction.title')
          }}<span
            class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.55_0.12_180)] bg-clip-text text-transparent"
            >{{ t('conviction.titleHighlight') }}</span
          ></Title
        >
        <Paragraph
          variant="website-highlight"
          class="opacity-80"
          >{{ t('conviction.description') }}</Paragraph
        >
        <blockquote
          class="border-primary/40 text-on-surface/90 mt-1 border-l-2 pl-4 text-lg leading-relaxed font-medium italic"
        >
          «&nbsp;{{ t('conviction.quote') }}&nbsp;»
        </blockquote>
        <ul class="mt-1 flex flex-wrap gap-2">
          <li
            v-for="tag in CONVICTION_TAGS"
            :key="tag"
            class="border-on-surface/10 bg-surface-container-low flex items-center gap-2 rounded-full border px-3 py-1.5 text-xs font-semibold"
          >
            <span class="bg-primary size-1.5 rounded-full" />
            {{ t(`conviction.tags.${tag}`) }}
          </li>
        </ul>
      </div>

      <div
        class="reveal-card relative mx-auto w-full max-w-md md:mr-0"
        :class="{ 'is-visible': convictionVisible }"
      >
        <figure
          class="ring-on-surface/10 relative aspect-[4/5] overflow-hidden rounded-3xl shadow-xl ring-1"
        >
          <img
            :src="convictionLandscapeUrl"
            :alt="t('conviction.imageLandscapeLabel')"
            loading="lazy"
            class="size-full object-cover"
          />
          <div
            aria-hidden="true"
            class="mission-photo-grade absolute inset-0"
          />
          <figcaption
            class="bg-surface/85 text-on-surface absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold backdrop-blur-md"
          >
            <span class="bg-positive size-1.5 rounded-full" />
            {{ t('conviction.imageLandscapeLabel') }}
          </figcaption>
        </figure>

        <figure
          class="ring-on-surface/10 border-surface absolute -bottom-8 -left-8 hidden w-44 overflow-hidden rounded-2xl border-4 shadow-2xl ring-1 sm:block lg:w-52"
        >
          <img
            :src="convictionHandsUrl"
            :alt="t('conviction.imageHandsLabel')"
            loading="lazy"
            class="aspect-[4/3] w-full object-cover"
          />
          <figcaption
            class="bg-surface/85 text-on-surface absolute bottom-2 left-2 flex items-center gap-1.5 rounded-full px-2.5 py-0.5 text-[10px] font-bold backdrop-blur-md"
          >
            <span class="bg-primary size-1.5 rounded-full" />
            {{ t('conviction.imageHandsLabel') }}
          </figcaption>
        </figure>
      </div>
    </div>
  </section>

  <!-- PILLARS — three cards, biodiversity highlighted -->
  <section
    id="pillars"
    class="relative scroll-mt-24 overflow-hidden"
  >
    <div
      aria-hidden="true"
      class="mission-blob mission-blob-tertiary absolute top-[-8%] right-[-14%]"
    />
    <div class="relative container flex flex-col items-center gap-12 py-20 md:py-28">
      <div
        ref="pillarsRef"
        class="reveal-group flex max-w-2xl flex-col items-center gap-4 text-center"
        :class="{ 'is-visible': pillarsVisible }"
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

      <div class="grid w-full grid-cols-1 gap-6 md:grid-cols-3">
        <article
          v-for="(pillar, index) in PILLARS"
          :key="pillar.key"
          class="reveal-card group relative flex flex-col gap-5 overflow-hidden rounded-3xl border p-6 shadow-sm transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
          :class="[
            pillar.highlight
              ? 'mission-card-accent'
              : 'border-on-surface/10 bg-surface-container-low',
            { 'is-visible': pillarsVisible },
          ]"
          :style="{ '--reveal-delay': `${0.1 + index * 0.12}s` }"
        >
          <figure
            class="ring-on-surface/10 relative aspect-[4/3] overflow-hidden rounded-2xl ring-1"
          >
            <img
              :src="pillar.image"
              :alt="t(`goals.${pillar.key}.title`)"
              loading="lazy"
              class="size-full object-cover transition-transform duration-700 group-hover:scale-105"
            />
            <div
              aria-hidden="true"
              class="mission-photo-grade absolute inset-0"
            />
            <figcaption
              class="absolute bottom-2.5 left-2.5 flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold backdrop-blur-md"
              :class="pillar.highlight ? 'bg-white/20 text-white' : 'bg-surface/85 text-on-surface'"
            >
              <span
                class="size-1.5 rounded-full"
                :class="pillar.dot"
              />
              {{ t(`goals.${pillar.key}.label`) }}
            </figcaption>
          </figure>

          <div
            class="flex size-11 items-center justify-center rounded-2xl shadow-sm ring-1"
            :class="
              pillar.highlight
                ? 'bg-white/15 text-white ring-white/20'
                : 'bg-surface text-primary ring-on-surface/10'
            "
          >
            <component
              :is="pillar.icon"
              class="size-5"
            />
          </div>

          <Title
            variant="h3"
            class="font-lexend text-xl font-bold"
            >{{ t(`goals.${pillar.key}.title`) }}</Title
          >
          <p :class="pillar.highlight ? 'text-white/85' : 'opacity-70'">
            {{ t(`goals.${pillar.key}.description`) }}
          </p>
        </article>
      </div>
    </div>
  </section>

  <!-- APPROACH — feature image + numbered steps -->
  <section class="bg-surface-container-low/50 relative overflow-hidden">
    <div
      class="relative container grid items-center gap-12 py-20 md:grid-cols-2 md:py-28 lg:gap-16"
    >
      <figure
        class="reveal-card ring-on-surface/10 relative order-2 aspect-[4/3] overflow-hidden rounded-3xl shadow-xl ring-1 md:order-1"
        :class="{ 'is-visible': approachVisible }"
      >
        <img
          :src="approachFieldsUrl"
          :alt="t('approach.imageLabel')"
          loading="lazy"
          class="size-full object-cover"
        />
        <div
          aria-hidden="true"
          class="mission-photo-grade absolute inset-0"
        />
        <figcaption
          class="bg-surface/85 text-on-surface absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold backdrop-blur-md"
        >
          <span class="bg-tertiary size-1.5 rounded-full" />
          {{ t('approach.imageLabel') }}
        </figcaption>
      </figure>

      <div
        ref="approachRef"
        class="reveal-group order-1 flex flex-col gap-6 md:order-2"
        :class="{ 'is-visible': approachVisible }"
      >
        <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
          t('approach.kicker')
        }}</span>
        <Title
          variant="h2"
          class="font-lexend text-4xl font-extrabold tracking-tight md:text-5xl"
          >{{ t('approach.title')
          }}<span
            class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.55_0.12_180)] bg-clip-text text-transparent"
            >{{ t('approach.titleHighlight') }}</span
          ></Title
        >
        <Paragraph
          variant="website-highlight"
          class="opacity-80"
          >{{ t('approach.description') }}</Paragraph
        >

        <ol class="mt-2 flex flex-col gap-6">
          <li
            v-for="(step, index) in STEPS"
            :key="step.key"
            class="flex gap-4"
          >
            <span class="mission-step-num shrink-0">{{ String(index + 1).padStart(2, '0') }}</span>
            <div class="flex flex-col gap-1">
              <div class="flex items-center gap-2">
                <component
                  :is="step.icon"
                  class="text-primary size-4"
                />
                <Title
                  variant="h3"
                  class="font-lexend text-lg font-bold"
                  >{{ t(`approach.steps.${step.key}.title`) }}</Title
                >
              </div>
              <p class="opacity-70">{{ t(`approach.steps.${step.key}.description`) }}</p>
            </div>
          </li>
        </ol>
      </div>
    </div>
  </section>

  <!-- BAND — full-bleed manifesto quote -->
  <section class="relative">
    <div class="relative flex min-h-[58svh] items-center justify-center overflow-hidden">
      <img
        :src="bandMistyUrl"
        alt=""
        aria-hidden="true"
        loading="lazy"
        class="absolute inset-0 size-full object-cover"
      />
      <div
        aria-hidden="true"
        class="absolute inset-0 bg-gradient-to-b from-black/60 via-black/35 to-black/60"
      />
      <div
        class="relative z-10 container flex max-w-3xl flex-col items-center gap-5 py-20 text-center"
      >
        <span
          class="flex size-12 items-center justify-center rounded-full border border-white/25 bg-white/10 backdrop-blur-md"
        >
          <IconSprout class="size-5 text-white/90" />
        </span>
        <p
          class="font-lexend text-[clamp(1.6rem,3.6vw,2.75rem)] leading-snug font-bold text-balance text-white drop-shadow-[0_2px_18px_rgba(0,0,0,0.5)]"
        >
          «&nbsp;{{ t('band.quote') }}&nbsp;»
        </p>
        <span class="text-xs font-semibold tracking-[0.2em] text-white/70 uppercase">{{
          t('band.author')
        }}</span>
      </div>
    </div>
  </section>

  <!-- OFFICIAL FRAMEWORK — lichen photo + statutes -->
  <section class="relative overflow-hidden">
    <div
      class="relative container grid items-start gap-12 py-20 md:grid-cols-[0.85fr_1.15fr] md:py-28 lg:gap-16"
    >
      <figure
        class="ring-on-surface/10 relative aspect-square overflow-hidden rounded-3xl shadow-xl ring-1 md:sticky md:top-28"
      >
        <img
          :src="statusLichenUrl"
          :alt="t('status.imageLabel')"
          loading="lazy"
          class="size-full object-cover"
        />
        <div
          aria-hidden="true"
          class="mission-photo-grade absolute inset-0"
        />
        <figcaption
          class="bg-surface/85 text-on-surface absolute bottom-3 left-3 flex items-center gap-1.5 rounded-full px-3 py-1 text-[11px] font-bold backdrop-blur-md"
        >
          <span class="bg-positive size-1.5 rounded-full" />
          {{ t('status.imageLabel') }}
        </figcaption>
      </figure>

      <div
        ref="statusRef"
        class="reveal-group mission-status-panel border-on-surface/10 flex flex-col items-start gap-5 rounded-3xl border p-8 shadow-sm md:p-10"
        :class="{ 'is-visible': statusVisible }"
      >
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
          class="opacity-80"
          >{{ t('status.description') }}</Paragraph
        >
        <p class="max-w-2xl text-justify whitespace-pre-line opacity-70">
          {{ t('status.content') }}
        </p>
        <RouterLink
          :to="{ name: ROUTE_CHARTER.name }"
          class="text-primary mt-1 inline-flex items-center gap-1.5 font-semibold transition-all hover:gap-2.5"
        >
          {{ t('status.linkLabel') }}
          <IconArrowRight class="size-4" />
        </RouterLink>
      </div>
    </div>
  </section>

  <!-- CTA — full-bleed, giant wordmark -->
  <section class="relative">
    <div class="relative flex min-h-[68svh] items-center justify-center overflow-hidden">
      <img
        :src="ctaSunsetUrl"
        alt=""
        aria-hidden="true"
        loading="lazy"
        class="absolute inset-0 size-full object-cover"
      />
      <div
        aria-hidden="true"
        class="absolute inset-0 bg-gradient-to-b from-black/50 via-black/35 to-black/70"
      />
      <span
        aria-hidden="true"
        class="mission-wordmark"
        >lychen</span
      >
      <div
        class="relative z-10 container flex max-w-3xl flex-col items-center gap-6 py-20 text-center"
      >
        <Title
          variant="h2"
          class="font-lexend text-[clamp(2rem,5vw,3.5rem)] leading-[1.08] font-extrabold tracking-tight text-white drop-shadow-[0_2px_20px_rgba(0,0,0,0.5)]"
          >{{ t('cta.title')
          }}<span
            class="bg-gradient-to-r from-[oklch(0.88_0.13_131)] via-[oklch(0.85_0.11_150)] to-[oklch(0.82_0.1_180)] bg-clip-text text-transparent"
            >{{ t('cta.titleHighlight') }}</span
          ></Title
        >
        <Paragraph
          variant="website-highlight"
          class="max-w-2xl text-white/85 drop-shadow-[0_1px_12px_rgba(0,0,0,0.5)]"
          >{{ t('cta.description') }}</Paragraph
        >
        <div class="mt-2 flex flex-wrap items-center justify-center gap-3">
          <RouterLink :to="{ name: ROUTE_APPLICATIONS.name }">
            <Button
              size="lg"
              data-umami-event="mission-cta-applications"
              :label="t('cta.primary')"
              class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.35)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.5)]"
            >
              <template #icon><IconArrowRight class="size-4" /></template>
            </Button>
          </RouterLink>
          <RouterLink :to="{ name: ROUTE_PARTNERSHIPS.name }">
            <Button
              size="lg"
              variant="ghost"
              data-umami-event="mission-cta-partnerships"
              :label="t('cta.secondary')"
              class="border border-white/30 bg-white/10 text-white backdrop-blur-md transition-all duration-300 hover:bg-white/20 hover:text-white"
            />
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script lang="ts" setup>
import heroValleyUrl from './assets/hero-valley.webp';
import convictionLandscapeUrl from './assets/conviction-landscape.webp';
import convictionHandsUrl from './assets/conviction-hands.webp';
import pillarFoodUrl from './assets/pillar-food.webp';
import pillarBiodiversityUrl from './assets/pillar-biodiversity.webp';
import pillarSocialUrl from './assets/pillar-social.webp';
import approachFieldsUrl from './assets/approach-fields.webp';
import bandMistyUrl from './assets/band-misty.webp';
import statusLichenUrl from './assets/status-lichen.webp';
import ctaSunsetUrl from './assets/cta-sunset.webp';

import { defineAsyncComponent, ref, type Ref } from 'vue';
import { useHead } from '@unhead/vue';
import { useIntersectionObserver } from '@vueuse/core';

import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useExtendedHead } from '@lychen/vue-unhead-composables/useExtendedHead';
import { useWebPageSchema } from '@lychen/vue-unhead-composables/useWebPageSchema';
import { CONFIG } from './i18n';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_CHARTER } from '@/views/charter';

import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import IconSprout from '@lychen/vue-icons/IconSprout.vue';
import IconEarth from '@lychen/vue-icons/IconEarth.vue';
import IconHeartHandshake from '@lychen/vue-icons/IconHeartHandshake.vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';
import IconFileCode from '@lychen/vue-icons/IconFileCode.vue';
import IconUsers from '@lychen/vue-icons/IconUsers.vue';
import IconMountain from '@lychen/vue-icons/IconMountain.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import IconChevronDown from '@lychen/vue-icons/IconChevronDown.vue';

const DivScalingDown = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-scaling-down/DivScalingDown.vue'),
);
const Hero = defineAsyncComponent(() => import('@lychen/vue-components-website/hero/Hero.vue'));

const CONVICTION_TAGS = ['nonprofit', 'openSource', 'openData'] as const;

const PILLARS = [
  { key: 'food', icon: IconSprout, image: pillarFoodUrl, highlight: false, dot: 'bg-primary' },
  {
    key: 'biodiversity',
    icon: IconEarth,
    image: pillarBiodiversityUrl,
    highlight: true,
    dot: 'bg-positive',
  },
  {
    key: 'social',
    icon: IconHeartHandshake,
    image: pillarSocialUrl,
    highlight: false,
    dot: 'bg-tertiary',
  },
] as const;

const STEPS = [
  { key: 'observe', icon: IconSearch },
  { key: 'equip', icon: IconFileCode },
  { key: 'cooperate', icon: IconUsers },
  { key: 'regenerate', icon: IconMountain },
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
    { threshold: 0.2 },
  );
  return revealed;
}

const convictionRef = ref<HTMLElement | null>(null);
const convictionVisible = useReveal(convictionRef);
const pillarsRef = ref<HTMLElement | null>(null);
const pillarsVisible = useReveal(pillarsRef);
const approachRef = ref<HTMLElement | null>(null);
const approachVisible = useReveal(approachRef);
const statusRef = ref<HTMLElement | null>(null);
const statusVisible = useReveal(statusRef);

const { t } = usePrefixedI18n(CONFIG);
useExtendedHead(t, { ogImage: heroValleyUrl });
useWebPageSchema(t, { siteName: 'lychen' });
useHead({
  link: [{ rel: 'preload', as: 'image', href: heroValleyUrl }],
});
</script>

<style scoped>
.mission-blob {
  width: clamp(280px, 38vw, 560px);
  height: clamp(280px, 38vw, 560px);
  border-radius: 9999px;
  filter: blur(80px);
  pointer-events: none;
  z-index: 0;
}

.mission-blob-primary {
  background: var(--color-primary);
  opacity: 0.12;
}

.mission-blob-tertiary {
  background: var(--color-tertiary);
  opacity: 0.1;
}

.mission-photo-grade {
  background: linear-gradient(180deg, transparent 42%, rgb(0 0 0 / 0.32) 100%);
}

/* Highlighted pillar card — deep green, light type. */
.mission-card-accent {
  background: linear-gradient(160deg, oklch(0.37 0.06 150) 0%, oklch(0.26 0.05 150) 100%);
  color: oklch(0.96 0.02 130);
  border-color: oklch(1 0 0 / 0.12);
}

.mission-status-panel {
  background:
    radial-gradient(140% 120% at 0% 0%, oklch(0.88 0.05 150 / 0.12), transparent 55%),
    var(--color-surface-container-low);
}

.mission-step-num {
  font-family: var(--font-lexend);
  font-weight: 800;
  font-size: 1.5rem;
  line-height: 1;
  background: linear-gradient(180deg, var(--color-primary), oklch(0.55 0.12 180));
  -webkit-background-clip: text;
  background-clip: text;
  color: transparent;
}

/* Giant faded wordmark behind the CTA copy. */
.mission-wordmark {
  position: absolute;
  bottom: -0.14em;
  left: 50%;
  translate: -50% 0;
  font-family: var(--font-lexend);
  font-weight: 800;
  letter-spacing: -0.04em;
  font-size: clamp(6rem, 22vw, 18rem);
  line-height: 0.8;
  color: rgb(255 255 255 / 0.08);
  white-space: nowrap;
  user-select: none;
  pointer-events: none;
}

/* Scroll-reveal. */
.reveal-group > *,
.reveal-card {
  opacity: 0;
  translate: 0 28px;
  transition:
    opacity 0.7s ease,
    translate 0.7s ease;
}

.reveal-card {
  transition-delay: var(--reveal-delay, 0s);
}

.reveal-group > *:nth-child(2) {
  transition-delay: 0.08s;
}

.reveal-group > *:nth-child(3) {
  transition-delay: 0.16s;
}

.reveal-group > *:nth-child(4) {
  transition-delay: 0.24s;
}

.reveal-group > *:nth-child(5) {
  transition-delay: 0.32s;
}

.reveal-group.is-visible > *,
.reveal-card.is-visible {
  opacity: 1;
  translate: 0 0;
}

.mission-scroll-hint span {
  animation: mission-bounce 2.4s ease-in-out infinite;
}

@keyframes mission-bounce {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(8px);
  }
}

@media (prefers-reduced-motion: reduce) {
  .reveal-group > *,
  .reveal-card {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }

  .mission-scroll-hint span {
    animation: none;
  }
}
</style>
