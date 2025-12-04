<template>
  <footer class="container p-4">
    <div class="bg-surface-container-low flex flex-col rounded-xl p-6">
      <slot></slot>
    </div>
    <SelectLanguage />
  </footer>
</template>

<script setup lang="ts">
import SelectLanguage from '@lychen/vue-i18n/components/select-language/SelectLanguage.vue';
</script>
