<template>
  <div class="bg-surface-container rounded-lg p-4">Card LandProposal {{ id }}</div>
</template>

<script setup lang="ts">
interface Props {
  id: string;
}

defineProps<Props>();
</script>
