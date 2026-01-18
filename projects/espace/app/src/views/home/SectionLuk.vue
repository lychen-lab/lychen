<template>
  <div class="flex flex-row items-center gap-4">
    <div class="bg-surface-container rounded-full p-2 text-emerald-700">
      <slot name="icon" />
    </div>
    <div>
      <h3 class="text-md font-bold">{{ title }}</h3>
      <p class="w-4/5 text-sm text-emerald-800 opacity-80">{{ description }}</p>
    </div>
  </div>
</template>

<script setup lang="ts">
interface Props {
  title: string;
  description: string;
}

defineProps<Props>();
</script>
