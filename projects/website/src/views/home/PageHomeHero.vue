<template>
  <!-- Photo: Pexels #1271619 — free to use, no attribution required. -->
  <!-- Scales down & rounds its corners on scroll (DivScalingDown). -->
  <DivScalingDown class="flex flex-col justify-start">
    <Hero
      class="relative flex min-h-svh items-center justify-center overflow-hidden"
      overlay
      :background-image="heroBackgroundUrl"
      overlay-class="bg-gradient-to-b from-black/55 via-black/20 to-black/65"
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
          >{{ t('hero.title.prepend')
          }}<span
            class="bg-gradient-to-r from-[oklch(0.88_0.13_131)] via-[oklch(0.85_0.11_150)] to-[oklch(0.82_0.1_180)] bg-clip-text text-transparent"
            >{{ t('hero.title.key_word') }}</span
          >{{ t('hero.title.append') }}</Title
        >

        <Paragraph
          variant="website-highlight"
          class="max-w-2xl text-white/85 drop-shadow-[0_1px_12px_rgba(0,0,0,0.5)]"
          >{{ t('hero.description') }}</Paragraph
        >

        <div class="mt-2 flex flex-wrap items-center justify-center gap-3">
          <RouterLink to="#discover">
            <Button
              size="lg"
              data-umami-event="discover-button"
              :label="t('hero.discover')"
              class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.35)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.5)]"
            >
              <template #icon><IconArrowRight class="size-4" /></template>
            </Button>
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

      <RouterLink
        to="#discover"
        :aria-label="t('hero.scroll_hint')"
        class="hero-scroll-hint absolute bottom-6 left-1/2 z-10 -translate-x-1/2"
      >
        <span
          class="flex size-11 items-center justify-center rounded-full border border-white/25 bg-white/10 backdrop-blur-md"
        >
          <IconChevronDown class="size-5 text-white/90" />
        </span>
      </RouterLink>
    </Hero>
  </DivScalingDown>
</template>

<script setup lang="ts">
import heroBackgroundUrl from './assets/hero-territory.webp';
import { defineAsyncComponent } from 'vue';
import { useHead } from '@unhead/vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { ROUTE_MISSION } from '@/views/mission';
import Button from '@lychen/vue-components-core/button/Button.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';
import IconChevronDown from '@lychen/vue-icons/IconChevronDown.vue';
import IconArrowRight from '@lychen/vue-icons/IconArrowRight.vue';

const DivScalingDown = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-scaling-down/DivScalingDown.vue'),
);
const Hero = defineAsyncComponent(() => import('@lychen/vue-components-website/hero/Hero.vue'));

const { t } = usePrefixedI18n(CONFIG);

useHead({
  link: [{ rel: 'preload', as: 'image', href: heroBackgroundUrl }],
});
</script>

<style scoped>
.hero-scroll-hint span {
  animation: hero-bounce 2.4s ease-in-out infinite;
}

@keyframes hero-bounce {
  0%,
  100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(8px);
  }
}

@media (prefers-reduced-motion: reduce) {
  .hero-scroll-hint span {
    animation: none;
  }
}
</style>
