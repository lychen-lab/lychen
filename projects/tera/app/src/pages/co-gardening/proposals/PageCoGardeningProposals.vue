<template>
  <section>
    <BaseHeading variant="h1">
      {{ t('title') }}
    </BaseHeading>
    <GridTeraLandProposal
      :status="status"
      :query-result="landProposals"
    />
  </section>
</template>

<script lang="ts" setup>
import { BaseHeading } from '@lychen/vue-components-app/base-heading';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import GridTeraLandProposal from '@lychen/vue-tera/components/land-proposal/grid/GridTeraLandProposal.vue';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useQuery } from '@tanstack/vue-query';

const { t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { api } = useTeraApi();

const { data: landProposals, status } = useQuery({
  queryKey: ['landProposalsPublic'],
  queryFn: async () => {
    const response = await api.GET('/api/land_proposals/public', {});
    return response.data;
  },
});
</script>

<style lang="css" scoped></style>
