<template>
  <DivScalingDown class="flex flex-col justify-start">
    <Hero
      class="text-surface dark:text-on-surface flex min-h-dvh flex-row items-center justify-start gap-4"
      :background-image="heroUrl"
      overlay
      overlay-class="bg-on-surface dark:bg-surface opacity-40"
    >
      <div class="flex flex-col items-start gap-4 md:w-3/5">
        <Title
          variant="h1"
          class="z-20 text-balance"
          >{{ t('hero.title.prepend') }}
          {{ t('hero.title.key_word') }}
          {{ t('hero.title.append') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="z-20"
          >{{ t('hero.description') }}
        </Paragraph>
        <div class="flex flex-row items-center gap-4">
          <RouterLink
            to="#discover"
            class="z-20"
          >
            <Button
              class="flex gap-2"
              data-umami-event="discover-button"
              :label="t('hero.discover')"
            />
          </RouterLink>
        </div>
      </div>
    </Hero>
  </DivScalingDown>
</template>

<script setup lang="ts">
import heroUrl from './assets/hero-2.webp';
import { defineAsyncComponent } from 'vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';

const DivScalingDown = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-scaling-down/DivScalingDown.vue'),
);

const Hero = defineAsyncComponent(() => import('@lychen/vue-components-website/hero/Hero.vue'));
const Button = defineAsyncComponent(() => import('@lychen/vue-components-core/button/Button.vue'));
const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));
const Paragraph = defineAsyncComponent(
  () => import('@lychen/vue-components-website/paragraph/Paragraph.vue'),
);

const { t } = usePrefixedI18n(CONFIG);
</script>

<style scoped>
:deep(.blobed) {
  border-radius: 60% 40% 30% 70% / 60% 30% 70% 40%;
}
</style>
