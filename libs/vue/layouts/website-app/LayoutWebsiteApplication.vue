<template>
  <div class="website-application-layout">
    <LayoutWebsiteApplicationNavigation
      class="fixed p-2"
      :application-name
      :application-state
      :route-home
    >
      <slot name="navigation" />
      <template #mobile>
        <slot name="mobile" />
      </template>
      <template #header>
        <slot name="header" />
      </template>
    </LayoutWebsiteApplicationNavigation>
    <slot>
      <main class="flex min-h-dvh flex-col">
        <RouterView />
      </main>
    </slot>
    <LayoutWebsiteApplicationFooter :application-name />
  </div>
</template>

<script setup lang="ts">
import LayoutWebsiteApplicationFooter from './LayoutWebsiteApplicationFooter.vue';
import { type LayoutWebsiteApplicationNavigationProps } from '.';
import LayoutWebsiteApplicationNavigation from './LayoutWebsiteApplicationNavigation.vue';

defineProps<LayoutWebsiteApplicationNavigationProps>();
</script>

<style lang="css" scoped>
.website-application-layout {
  display: flex;
  flex-direction: column;
}
</style>
