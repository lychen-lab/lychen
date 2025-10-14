<template>
  <div class="layout-in-app-root grid-cols-[auto_1fr] grid-rows-[60px_1fr]">
    <slot> </slot>
  </div>
</template>

<script setup lang="ts"></script>

<style lang="css" scoped>
.layout-in-app-root {
  display: grid;
  height: 100dvh;
  max-height: 100dvh;
  grid-template-areas:
    'side-navigation header'
    'side-navigation content';

  &:deep(.layout-in-app-side-navigation) {
    grid-area: side-navigation;
  }

  &:deep(.layout-in-app-header) {
    grid-area: header;
  }

  &:deep(.layout-in-app-content) {
    grid-area: content;
    overflow: auto;
  }
}
</style>
