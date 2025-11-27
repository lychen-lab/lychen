<template>
  <div class="flex flex-row gap-4">
    <CardLandProposal
      v-for="item in fakeLandProposals"
      :key="item.id"
      v-bind="item"
    />
  </div>
  <div class="flex flex-row gap-4">
    <CardLandRequest
      v-for="item in fakeLandRequests"
      :key="item.id"
      v-bind="item"
      class="bg-green-800 text-green-200"
    />
  </div>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { Card as CardLandProposal } from '@lychen/vue-components-business/land-proposal/card';
import { Card as CardLandRequest } from '@lychen/vue-components-business/land-request/card';
import { MESSAGES, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({ messages: MESSAGES, rootKey: TRANSLATION_KEY, prefixed: true });

const fakeLandProposals = [
  {
    id: '1',
  },
  {
    id: '2',
  },
  {
    id: '3',
  },
];

const fakeLandRequests = [
  {
    id: '1',
  },
  {
    id: '2',
  },
  {
    id: '3',
  },
];
</script>

<style lang="css" scoped></style>
