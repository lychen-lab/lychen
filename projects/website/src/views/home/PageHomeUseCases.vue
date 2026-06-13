<template>
  <!-- Photo: Pexels #974314 — free to use, no attribution required. -->
  <section class="ecosystem relative scroll-mt-24 overflow-hidden">
    <div
      aria-hidden="true"
      class="ecosystem-blob absolute top-[-6%] right-[-12%]"
    />
    <div class="relative container flex flex-col items-center gap-12 py-20 md:py-28">
      <!-- Header -->
      <div
        ref="headerRef"
        class="reveal-group flex max-w-2xl flex-col items-center gap-4 text-center"
        :class="{ 'is-visible': headerVisible }"
      >
        <span class="text-primary text-xs font-bold tracking-[0.2em] uppercase">{{
          t('ecosystem.kicker')
        }}</span>
        <Title
          variant="h2"
          class="font-lexend text-4xl font-extrabold tracking-tight md:text-5xl"
          >{{ t('ecosystem.title.prepend')
          }}<span
            class="from-primary bg-gradient-to-r via-[oklch(0.65_0.1_150)] to-[oklch(0.55_0.12_180)] bg-clip-text text-transparent"
            >{{ t('ecosystem.title.key_word') }}</span
          >{{ t('ecosystem.title.append') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="opacity-80"
          >{{ t('ecosystem.description') }}</Paragraph
        >
      </div>

      <!-- Interconnection band — hub & spokes constellation -->
      <div
        ref="bandRef"
        class="reveal-card ecosystem-band ring-on-surface/10 relative w-full overflow-hidden rounded-3xl shadow-xl ring-1"
        :class="{ 'is-visible': bandVisible }"
      >
        <img
          :src="fieldsUrl"
          alt=""
          aria-hidden="true"
          loading="lazy"
          class="absolute inset-0 size-full object-cover"
        />
        <div
          aria-hidden="true"
          class="ecosystem-band-grade absolute inset-0"
        />

        <div
          class="relative z-10 grid items-center gap-10 p-8 md:grid-cols-[1.1fr_0.9fr] md:p-12 lg:p-14"
        >
          <div class="flex flex-col items-start gap-5 text-white">
            <Title
              variant="h3"
              class="font-lexend text-2xl font-extrabold tracking-tight md:text-3xl"
              >{{ t('ecosystem.hub.title') }}</Title
            >
            <p class="max-w-md text-white/85">{{ t('ecosystem.hub.description') }}</p>
            <ul class="flex flex-wrap gap-2.5">
              <li
                v-for="feature in FEATURES"
                :key="feature.key"
                class="flex items-center gap-2 rounded-full border border-white/20 bg-white/10 px-3.5 py-1.5 text-sm font-semibold text-white backdrop-blur-md"
              >
                <component
                  :is="feature.icon"
                  class="size-4 text-[oklch(0.88_0.13_131)]"
                />
                {{ t(`ecosystem.hub.features.${feature.key}`) }}
              </li>
            </ul>
          </div>

          <div class="ecosystem-constellation relative mx-auto aspect-square w-full max-w-xs">
            <svg
              aria-hidden="true"
              class="absolute inset-0 size-full"
              viewBox="0 0 400 400"
              fill="none"
              xmlns="http://www.w3.org/2000/svg"
            >
              <g class="ecosystem-orbit">
                <line
                  v-for="node in NODES"
                  :key="`spoke-${node.i}`"
                  x1="200"
                  y1="200"
                  :x2="node.x"
                  :y2="node.y"
                  stroke="oklch(0.95 0.05 140 / 0.28)"
                  stroke-width="1"
                />
                <line
                  v-for="node in NODES"
                  :key="`chord-${node.i}`"
                  :x1="node.x"
                  :y1="node.y"
                  :x2="node.nextX"
                  :y2="node.nextY"
                  stroke="oklch(0.95 0.05 140 / 0.16)"
                  stroke-width="1"
                />
                <circle
                  v-for="node in NODES"
                  :key="`node-${node.i}`"
                  :cx="node.x"
                  :cy="node.y"
                  r="7"
                  fill="oklch(0.92 0.1 131)"
                  class="ecosystem-node"
                  :style="{ '--node-delay': `${node.i * 0.32}s` }"
                />
              </g>
            </svg>

            <span
              class="ecosystem-hub absolute top-1/2 left-1/2 flex size-20 -translate-x-1/2 -translate-y-1/2 items-center justify-center rounded-full bg-white/95 shadow-[0_8px_30px_rgba(0,0,0,0.35)] backdrop-blur-md"
            >
              <LogoLychenIconOnly class="size-10 text-[oklch(0.4_0.09_150)]" />
            </span>
          </div>
        </div>
      </div>

      <!-- Applications grid -->
      <div
        ref="gridRef"
        class="grid w-full grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3"
      >
        <article
          v-for="(application, index) in applications"
          :key="application.alias"
          class="ecosystem-app group flex flex-col gap-3 rounded-3xl border p-6 transition-all duration-300 hover:-translate-y-1 hover:shadow-xl"
          :class="[
            isFlagship(application.alias)
              ? 'ecosystem-app-accent'
              : 'border-on-surface/10 bg-surface-container-low/60',
            { 'is-visible': gridVisible },
          ]"
          :style="{ '--reveal-delay': `${0.04 * index}s` }"
        >
          <div class="flex items-center justify-between gap-2">
            <div class="flex items-center gap-2.5">
              <span
                class="size-2.5 rounded-full"
                :class="isFlagship(application.alias) ? 'bg-white' : APP_DOT[application.alias]"
              />
              <Title
                variant="h3"
                class="font-lexend lowercase"
                >{{ application.title }}</Title
              >
            </div>
            <span
              class="rounded-full px-2.5 py-0.5 text-[11px] font-semibold whitespace-nowrap"
              :class="
                isFlagship(application.alias)
                  ? 'bg-white/15 text-white'
                  : 'bg-surface-container text-on-surface/60'
              "
              >{{ application.state }}</span
            >
          </div>
          <p
            class="line-clamp-3 text-sm"
            :class="isFlagship(application.alias) ? 'text-white/85' : 'opacity-70'"
          >
            {{ application.description }}
          </p>
        </article>
      </div>

      <RouterLink :to="{ name: ROUTE_APPLICATIONS.name }">
        <Button
          size="lg"
          data-umami-event="ecosystem-applications-button"
          :label="t('ecosystem.explore')"
          class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.3)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.45)]"
        >
          <template #icon><IconArrowRight class="size-4" /></template>
        </Button>
      </RouterLink>
    </div>
  </section>
</template>

<script setup lang="ts">
import fieldsUrl from './assets/ecosystem-fields.webp';
import { computed, ref, type Ref } from 'vue';
import { useIntersectionObserver } from '@vueuse/core';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useApplicationsCatalog } from '@lychen/vue-applications/composables/useApplicationsCatalog';
import { type ApplicationAlias } from '@lychen/typescript-applications/constants/ApplicationAlias';
import { ROUTE_APPLICATIONS } from '@/views/applications';

import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import LogoLychenIconOnly from '@lychen/vue-components-extra/logo-lychen/LogoLychenIconOnly.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';
import IconBadgeCheck from '@lychen/vue-icons/IconBadgeCheck.vue';
import IconShare2 from '@lychen/vue-icons/IconShare2.vue';
import IconGithub from '@lychen/vue-icons/IconGithub.vue';
import IconLink from '@lychen/vue-icons/IconLink.vue';

const { t } = usePrefixedI18n(CONFIG);
const { opiniatedApplicationsList: applications } = useApplicationsCatalog();

const FEATURES = [
  { key: 'identity', icon: IconBadgeCheck },
  { key: 'data', icon: IconShare2 },
  { key: 'opensource', icon: IconGithub },
  { key: 'interop', icon: IconLink },
] as const;

const FLAGSHIP_ALIASES = new Set<string>(['espace', 'tera']);
function isFlagship(alias: ApplicationAlias) {
  return FLAGSHIP_ALIASES.has(alias);
}

const APP_DOT: Record<string, string> = {
  espace: 'bg-positive',
  tera: 'bg-primary',
  myko: 'bg-warning',
  meli: 'bg-warning',
  kiro: 'bg-tertiary',
  humu: 'bg-secondary',
  novi: 'bg-tertiary',
  vara: 'bg-positive',
  kolo: 'bg-primary',
  robust: 'bg-secondary',
};

// Hub & spokes: nodes evenly spaced on a ring, each linked to the hub and to its neighbour.
const NODE_COUNT = 10;
const RADIUS = 150;
const CENTER = 200;
const NODES = computed(() =>
  Array.from({ length: NODE_COUNT }, (_, i) => {
    const angle = (i / NODE_COUNT) * Math.PI * 2 - Math.PI / 2;
    const next = ((i + 1) % NODE_COUNT) / NODE_COUNT;
    const nextAngle = next * Math.PI * 2 - Math.PI / 2;
    return {
      i,
      x: CENTER + Math.cos(angle) * RADIUS,
      y: CENTER + Math.sin(angle) * RADIUS,
      nextX: CENTER + Math.cos(nextAngle) * RADIUS,
      nextY: CENTER + Math.sin(nextAngle) * RADIUS,
    };
  }),
);

function useReveal(target: Ref<HTMLElement | null>) {
  const revealed = ref(false);
  useIntersectionObserver(
    target,
    (entries) => {
      if (entries.some((entry) => entry.isIntersecting)) {
        revealed.value = true;
      }
    },
    { threshold: 0.15 },
  );
  return revealed;
}

const headerRef = ref<HTMLElement | null>(null);
const headerVisible = useReveal(headerRef);
const bandRef = ref<HTMLElement | null>(null);
const bandVisible = useReveal(bandRef);
const gridRef = ref<HTMLElement | null>(null);
const gridVisible = useReveal(gridRef);
</script>

<style scoped>
.ecosystem-blob {
  width: clamp(280px, 38vw, 540px);
  height: clamp(280px, 38vw, 540px);
  border-radius: 9999px;
  filter: blur(80px);
  pointer-events: none;
  z-index: 0;
  background: var(--color-primary);
  opacity: 0.1;
}

.ecosystem-band-grade {
  background:
    linear-gradient(120deg, oklch(0.33 0.06 150 / 0.92) 0%, oklch(0.26 0.05 150 / 0.82) 100%),
    linear-gradient(180deg, oklch(0.2 0.04 145 / 0.35), oklch(0.2 0.04 145 / 0.55));
}

/* Highlighted (in-development) application cards. */
.ecosystem-app-accent {
  background: linear-gradient(160deg, oklch(0.37 0.06 150) 0%, oklch(0.26 0.05 150) 100%);
  color: oklch(0.96 0.02 130);
  border-color: oklch(1 0 0 / 0.12);
}

.ecosystem-hub {
  animation: ecosystem-hub-pulse 4s ease-in-out infinite;
}

@media (prefers-reduced-motion: no-preference) {
  .ecosystem-orbit {
    transform-box: fill-box;
    transform-origin: center;
    animation: ecosystem-spin 60s linear infinite;
  }

  .ecosystem-node {
    animation: ecosystem-node-glow 3.2s ease-in-out infinite;
    animation-delay: var(--node-delay, 0s);
  }
}

@keyframes ecosystem-spin {
  to {
    transform: rotate(360deg);
  }
}

@keyframes ecosystem-node-glow {
  0%,
  100% {
    opacity: 0.55;
  }
  50% {
    opacity: 1;
  }
}

@keyframes ecosystem-hub-pulse {
  0%,
  100% {
    box-shadow: 0 8px 30px rgba(0, 0, 0, 0.35);
  }
  50% {
    box-shadow:
      0 8px 30px rgba(0, 0, 0, 0.35),
      0 0 0 10px oklch(0.92 0.1 131 / 0.12);
  }
}

/* Scroll reveal. */
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

.reveal-group.is-visible > *,
.reveal-card.is-visible {
  opacity: 1;
  translate: 0 0;
}

/* Stagger the application cards via their per-card delay. */
.ecosystem-app {
  opacity: 0;
  translate: 0 24px;
  transition:
    opacity 0.6s ease,
    translate 0.6s ease,
    box-shadow 0.3s ease,
    transform 0.3s ease;
  transition-delay: var(--reveal-delay, 0s);
}

.ecosystem-app.is-visible {
  opacity: 1;
  translate: 0 0;
}

@media (prefers-reduced-motion: reduce) {
  .reveal-group > *,
  .reveal-card,
  .ecosystem-app {
    opacity: 1;
    translate: 0 0;
    transition: none;
  }
}
</style>
