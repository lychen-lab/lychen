<template>
  <div class="">
    <CardLandProposal
      v-for="item in fakeLandProposals"
      :key="item.id"
      v-bind="item"
    />
  </div>
</template>

<script lang="ts" setup>
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { Card as CardLandProposal } from '@lychen/vue-components-business/land-proposal/card';
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
</script>

<style lang="css" scoped></style>
