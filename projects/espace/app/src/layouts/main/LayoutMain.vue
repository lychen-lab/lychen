<template>
  <div class="flex h-dvh flex-col">
    <header class="flex flex-row items-center justify-between p-4">
      <div class="font-lexend flex flex-row items-center gap-0">
        <span class="text-on-surface font-medium tracking-normal">espace</span>
        <span class="text-on-surface/40">.lychen</span>
      </div>
      <div class="bg-primary size-8 rounded-full"></div>
    </header>
    <main class="flex flex-col gap-8 px-4 pt-0 pb-[80px]">
      <RouterView />
    </main>
    <LayoutNavigationMain class="fixed bottom-0" />
  </div>
</template>

<script setup lang="ts">
import LayoutNavigationMain from '@/layouts/main/LayoutNavigationMain.vue';
</script>
