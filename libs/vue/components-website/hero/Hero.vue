<template>
  <DivWithBackgroundImg
    is="header"
    v-bind="forwarded"
  >
    <div class="z-10 container flex flex-col items-stretch py-8">
      <slot />
    </div>
  </DivWithBackgroundImg>
</template>

<script lang="ts" setup>
import { type DivWithBackgroundImgProps } from '@lychen/vue-components-extra/div-with-background-img';
import { useForwardProps } from 'reka-ui';
import { defineAsyncComponent } from 'vue';

const DivWithBackgroundImg = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-with-background-img/DivWithBackgroundImg.vue'),
);

const props = defineProps<Omit<DivWithBackgroundImgProps, 'is'>>();
const forwarded = useForwardProps(props);
</script>

<style scoped>
.overlay {
  border-radius: inherit;
}
</style>
