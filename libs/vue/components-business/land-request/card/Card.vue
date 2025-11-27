<template>
  <div>Card LandRequest {{ id }}</div>
</template>

<script setup lang="ts">
interface Props {
  id: string;
}

defineProps<Props>();
</script>
