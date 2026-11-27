<template>
  <div class="flex min-h-dvh flex-col">
    <main class="flex flex-1 flex-col gap-8 p-4 pb-24">
      <RouterView />
    </main>
    <LayoutNavigationMain class="fixed bottom-0 left-0 z-10" />
  </div>
</template>

<script setup lang="ts">
import LayoutNavigationMain from './LayoutNavigationMain.vue';
</script>
