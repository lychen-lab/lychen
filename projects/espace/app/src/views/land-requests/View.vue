<template>
  <section class="flex flex-col gap-4">
    <h2 class="text-xl font-bold">Espaces recherchés</h2>
  </section>
  <section class="flex flex-col gap-4">
    <LandRequestCard
      v-for="request in fakeLandRequests"
      :key="request.uuid"
      v-bind="request"
    />
    <p v-if="fakeLandRequests.length === 0">Aucune demande d'espace.</p>
  </section>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const fakeLandRequests = [];
</script>

<style lang="css" scoped></style>
