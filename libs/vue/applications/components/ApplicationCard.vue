<template>
  <DivWithBackgroundImg
    :class="cn('flex flex-col gap-2 p-6 rounded-xl group justify-between', $props.class)"
    :background-image="`${application.alias}/covers/${application.alias}-cover-1.webp`"
    overlay
    data-theme="light"
    overlay-class="bg-on-surface opacity-30 group-hover:opacity-15 transition duration-300 ease-in-out"
  >
    <div
      class="bg-surface/80 text-on-surface z-10 flex flex-col gap-2 rounded-3xl p-4 backdrop-blur-lg"
    >
      <div class="flex flex-row justify-between self-stretch">
        <ApplicationTitle :value="application.title" /><Badge v-if="displayState">{{
          application.state
        }}</Badge>
      </div>
      <p class="text-balance opacity-80">{{ application.description }}</p>
    </div>
    <slot name="footer" />
  </DivWithBackgroundImg>
</template>

<script setup lang="ts">
import { type Application } from '@lychen/typescript-applications/model/Application';
import { defineAsyncComponent, type HTMLAttributes } from 'vue';
import ApplicationTitle from './ApplicationTitle.vue';
import { cn } from '@lychen/typescript-utils/tailwind/Cn';

const DivWithBackgroundImg = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-with-background-img/DivWithBackgroundImg.vue'),
);

const Badge = defineAsyncComponent(() => import('@lychen/vue-components-core/badge/Badge.vue'));

const { displayState = false } = defineProps<{
  class?: HTMLAttributes['class'];
  application: Application;
  displayState?: boolean;
}>();
</script>
