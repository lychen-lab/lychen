<template>
  <div class="website-main-layout">
    <LayoutWebsiteMainNavigation class="fixed p-2">
      <slot name="navigation"></slot>
      <template #mobile>
        <slot name="mobile"></slot>
      </template>
      <template #header>
        <slot name="header"></slot>
      </template>
    </LayoutWebsiteMainNavigation>
    <slot>
      <main class="flex min-h-dvh flex-col">
        <RouterView />
      </main>
    </slot>
    <LayoutWebsiteMainFooter class="footer">
      <slot name="footer"></slot>
    </LayoutWebsiteMainFooter>
  </div>
</template>

<script setup lang="ts">
import LayoutWebsiteMainFooter from './LayoutWebsiteMainFooter.vue';
import LayoutWebsiteMainNavigation from './LayoutWebsiteMainNavigation.vue';
</script>

<style lang="css" scoped>
.website-main-layout {
  display: flex;
  flex-direction: column;
}
</style>
