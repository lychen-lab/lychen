<template>
  <section class="flex flex-col gap-4">
    <h2 class="text-xl font-bold">Favoris</h2>
  </section>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });
</script>

<style lang="css" scoped></style>
