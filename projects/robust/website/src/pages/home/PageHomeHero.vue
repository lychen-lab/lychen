<template>
  <DivScalingDown class="flex flex-col justify-start">
    <Hero
      class="text-surface dark:text-on-surface flex min-h-dvh flex-row items-center justify-start gap-4"
      :overlay="true"
      :background-image="heroImg"
      overlay-class="bg-on-surface dark:bg-surface opacity-40"
    >
      <div class="flex flex-col items-start gap-4 md:w-3/5">
        <Title
          variant="h1"
          class="z-20 text-balance"
          >{{ t('hero.title') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="z-20"
          >{{ t('hero.description') }}
        </Paragraph>

        <RouterLink
          to="#discover"
          class="z-20"
        >
          <Button
            class="flex gap-2"
            data-umami-event="features-button"
            >{{ t('hero.button.features.label') }}
            <IconChevronDown />
          </Button>
        </RouterLink>
      </div>
    </Hero>
  </DivScalingDown>
</template>

<script setup lang="ts">
import heroImg from './assets/HeroImage.webp';
import { defineAsyncComponent } from 'vue';
import IconChevronDown from '@lychen/vue-icons/IconChevronDown.vue';
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import Button from '@lychen/vue-components-core/button/Button.vue';

const DivScalingDown = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-scaling-down/DivScalingDown.vue'),
);

const Hero = defineAsyncComponent(() => import('@lychen/vue-components-website/hero/Hero.vue'));
const Title = defineAsyncComponent(() => import('@lychen/vue-components-website/title/Title.vue'));
const Paragraph = defineAsyncComponent(
  () => import('@lychen/vue-components-website/paragraph/Paragraph.vue'),
);
const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
</script>
