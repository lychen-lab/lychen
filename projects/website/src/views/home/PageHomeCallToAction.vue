<template>
  <!-- Photo: Pexels #17024755 — free to use, no attribution required. -->
  <section
    ref="root"
    class="cta relative overflow-hidden"
  >
    <img
      :src="ctaBackgroundUrl"
      alt=""
      aria-hidden="true"
      loading="lazy"
      class="cta-photo absolute inset-0 size-full object-cover"
    />
    <div
      aria-hidden="true"
      class="cta-grade absolute inset-0"
    />
    <div class="relative z-10 container">
      <div class="flex flex-col items-center gap-5 py-28 text-center md:py-40">
        <span class="text-xs font-bold tracking-[0.2em] text-white/70 uppercase">{{
          t('cta.kicker')
        }}</span>
        <Title
          variant="h2"
          class="font-lexend max-w-3xl text-4xl font-extrabold tracking-tight text-white drop-shadow-[0_2px_20px_rgba(0,0,0,0.5)] md:text-5xl lg:text-6xl"
          >{{ t('cta.title') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="max-w-2xl text-white/85"
          >{{ t('cta.description') }}</Paragraph
        >
        <div class="mt-3 flex flex-wrap items-center justify-center gap-3">
          <RouterLink :to="{ name: ROUTE_PARTNERSHIPS.name }">
            <Button
              size="lg"
              data-umami-event="cta-partnership-button"
              :label="t('cta.partner')"
              class="border-0 bg-[oklch(0.87_0.12_131)] text-[oklch(0.22_0.06_135)] shadow-[0_8px_30px_oklch(0.87_0.12_131_/_0.35)] transition-all duration-300 hover:-translate-y-0.5 hover:bg-[oklch(0.91_0.12_131)] hover:shadow-[0_12px_40px_oklch(0.87_0.12_131_/_0.5)]"
            />
          </RouterLink>
          <RouterLink :to="{ name: ROUTE_APPLICATIONS.name }">
            <Button
              size="lg"
              variant="ghost"
              data-umami-event="cta-applications-button"
              :label="t('cta.applications')"
              class="border border-white/30 bg-white/10 text-white backdrop-blur-md transition-all duration-300 hover:bg-white/20 hover:text-white"
            />
          </RouterLink>
        </div>
      </div>
    </div>
  </section>
</template>

<script setup lang="ts">
import ctaBackgroundUrl from './assets/parallax-cta.webp';
import { ref } from 'vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useParallax } from '@/composables/useParallax';
import { ROUTE_PARTNERSHIPS } from '@/views/partnerships';
import { ROUTE_APPLICATIONS } from '@/views/applications';
import Button from '@lychen/vue-components-core/button/Button.vue';
import Title from '@lychen/vue-components-website/title/Title.vue';
import Paragraph from '@lychen/vue-components-website/paragraph/Paragraph.vue';

const root = ref<HTMLElement | null>(null);
useParallax(root);

const { t } = usePrefixedI18n(CONFIG);
</script>

<style scoped>
.cta {
  --scroll-y: 0;
  --center-delta: 0;
}

.cta-photo {
  transform: translate3d(0, calc(var(--center-delta) * -56px), 0) scale(1.18);
  will-change: transform;
}

.cta-grade {
  background:
    linear-gradient(180deg, rgb(0 0 0 / 0.55) 0%, rgb(0 0 0 / 0.35) 45%, rgb(0 0 0 / 0.6) 100%),
    linear-gradient(180deg, oklch(0.45 0.09 150 / 0.28), oklch(0.3 0.08 140 / 0.36));
}
</style>
