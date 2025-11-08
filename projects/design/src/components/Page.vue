<template>
  <div class="flex flex-col gap-4">
    <slot name="header">
      <header class="bg-surface-container flex flex-col gap-2 rounded-t-2xl px-10 py-14">
        <slot name="title">
          <BaseHeading v-if="title">{{ title }}</BaseHeading>
        </slot>
        <slot name="description">
          <span
            v-if="description"
            class="opacity-80"
            >{{ description }}</span
          >
        </slot>
      </header>
    </slot>
    <slot />
  </div>
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

const BaseHeading = defineAsyncComponent(
  () => import('@lychen/vue-components-app/base-heading/BaseHeading.vue'),
);

defineProps<{ title?: string; description?: string }>();
</script>
