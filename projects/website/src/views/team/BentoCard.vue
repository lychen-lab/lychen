<template>
  <div class="w-full overflow-hidden rounded-[100px]">
    <img
      :src="image"
      class="h-full w-full object-cover"
    />
  </div>
</template>

<script setup lang="ts">
defineProps<{
  image: string;
}>();
</script>
