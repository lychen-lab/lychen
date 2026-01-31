<template>
  <DivScalingDown class="flex flex-col justify-start">
    <Container class="flex min-h-[80dvh] flex-col items-center justify-center gap-4">
      <div class="flex flex-col items-center justify-center gap-4 text-center md:w-3/5">
        <Title
          variant="h1"
          class="font-poppins text-balanc z-20"
          >{{ t('hero.title.prepend') }}
          {{ t('hero.title.key_word') }}
          {{ t('hero.title.append') }}</Title
        >
        <Paragraph
          variant="website-highlight"
          class="z-20 opacity-80"
          >{{ t('hero.description') }}
        </Paragraph>

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
    </Container>
  </DivScalingDown>
</template>

<script setup lang="ts">
import { defineAsyncComponent } from 'vue';

import { CONFIG } from './i18n';
import { usePrefixedI18n } from '@lychen/vue-i18n/composables/useI18nExtended';
import Container from '@lychen/vue-components-website/container/Container.vue';
import TextHighlight from '@lychen/vue-components-extra/text-highlight/TextHighlight.vue';

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
