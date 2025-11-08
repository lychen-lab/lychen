<template>
  <img
    v-if="src"
    class="h-[1.7lh] rounded-xl"
    :src
  />
</template>

<script setup lang="ts">
defineProps<{ src?: string }>();
</script>
