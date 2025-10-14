<template>
  <DivWithBackgroundImg
    :background-image="backgroundImage"
    class="bg-surface-container text-on-surface flex min-h-[250px] flex-col justify-end gap-6 rounded-3xl bg-cover bg-center p-6"
    overlay
    overlay-class="bg-gradient-to-tr from-surface/80 to-surface/10"
  >
    <Title
      variant="h3"
      class="z-2"
      >{{ title }}</Title
    >
  </DivWithBackgroundImg>
</template>

<script setup lang="ts">
import Title from '@lychen/vue-components-website/title/Title.vue';
import { defineAsyncComponent } from 'vue';

const DivWithBackgroundImg = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-with-background-img/DivWithBackgroundImg.vue'),
);

withDefaults(defineProps<{ backgroundImage?: string; title: string }>(), {
  backgroundImage: undefined,
});
</script>
