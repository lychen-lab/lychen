<template>
  <RouterLink
    :to="link"
    class="flex flex-col items-center gap-1 rounded-xl"
  >
    <img
      :src="`/logos/${id}/logo.png`"
      class="h-12 w-12 rounded-xl"
    />
    <span class="text-xs font-bold">{{ name }}</span>
  </RouterLink>
</template>

<script setup lang="ts">
defineProps<{ id: string; name: string; link: string }>();
</script>
