<template>
  <section>
    <BaseHeading variant="h1">
      {{ t('title') }}
    </BaseHeading>
    <div
      v-if="landRequests"
      class="flex flex-col gap-4"
    >
      <DialogTeraLandRequestView
        v-for="landRequest in landRequests.member"
        :key="landRequest.ulid"
        :title="landRequest.title"
        :description="landRequest.message"
        :sharing-conditions="
          (() => landRequest.sharingConditions as unknown as LandSharingCondition[])()
        "
      >
        <CardTeraLandRequest
          :title="landRequest.title"
          :expiration-date="landRequest.expirationDate"
          :sharing-conditions="
            (() => landRequest.sharingConditions as unknown as LandSharingCondition[])()
          "
          :preferred-interaction-mode="landRequest.preferredInteractionMode"
        />
      </DialogTeraLandRequestView>
    </div>
  </section>
</template>

<script lang="ts" setup>
import { BaseHeading } from '@lychen/vue-components-app/base-heading';

import { useQuery } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import CardTeraLandRequest from '@lychen/vue-tera/components/land-request/card/CardTeraLandRequest.vue';
import DialogTeraLandRequestView from '@lychen/vue-tera/components/land-request/dialogs/view/DialogTeraLandRequestView.vue';
import type { LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';

const { t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { api } = useTeraApi();

const { data: landRequests } = useQuery({
  queryKey: ['landRequestsPublic'],
  queryFn: async () => {
    const response = await api.GET('/api/land_requests/public', {});
    return response.data;
  },
});
</script>

<style lang="css" scoped></style>
