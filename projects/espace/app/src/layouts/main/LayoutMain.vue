<template>
  <div class="flex h-dvh flex-col">
    <main class="flex flex-col gap-8 p-4 pb-[80px]">
      <RouterView />
    </main>
    <LayoutNavigationMain class="fixed bottom-0" />
  </div>
</template>

<script setup lang="ts">
import LayoutNavigationMain from '@/layouts/main/LayoutNavigationMain.vue';
</script>
