<template>
  <div
    v-if="queryResult"
    class="flex flex-col gap-4"
  >
    <div
      v-for="landProposal in queryResult.member"
      :key="landProposal.ulid"
    >
      <DialogTeraLandProposalView
        :key="landProposal.ulid"
        :title="landProposal.title"
        :description="landProposal.description"
        :sharing-conditions="
          (() => landProposal.sharingConditions as unknown as LandSharingCondition[])()
        "
      >
        <CardTeraLandProposal
          :title="landProposal.title"
          :land-name="landProposal.land?.name"
          :land-altitude="landProposal.land?.altitude"
          :land-surface="landProposal.land?.surface"
          :land-city="landProposal.land?.address?.city"
          :expiration-date="landProposal.expirationDate"
          :sharing-conditions="
            (() => landProposal.sharingConditions as unknown as LandSharingCondition[])()
          "
          :preferred-interaction-mode="landProposal.preferredInteractionMode"
        />
      </DialogTeraLandProposalView>
    </div>
  </div>

  <div v-else>
    <div
      v-if="status === 'pending'"
      class="flex flex-row items-center gap-4"
    >
      <IconLoaderCircle />
      Recherche en cours
    </div>
    <div v-else>No land proposals found.</div>
  </div>
</template>

<script setup lang="ts">
import CardTeraLandProposal from '@lychen/vue-tera/components/land-proposal/card/CardTeraLandProposal.vue';
import DialogTeraLandProposalView from '@lychen/vue-tera/components/land-proposal/dialogs/view/DialogTeraLandProposalView.vue';

import type { LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';
import type { operations } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { QueryStatus } from '@tanstack/vue-query';
import IconLoaderCircle from '@lychen/vue-icons/IconLoaderCircle.vue';

defineProps<{
  queryResult?: operations['land-proposal_collection-public']['responses']['200']['content']['application/ld+json'];
  status: QueryStatus;
}>();
</script>
