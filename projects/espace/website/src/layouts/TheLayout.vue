<template>
  <LayoutMain> </LayoutMain>
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

const LayoutMain = defineAsyncComponent(
  () => import('@lychen/vue-layouts/website-main/LayoutWebsiteMain.vue'),
);
</script>
