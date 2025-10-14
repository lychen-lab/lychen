<template>
  <SectionTwoThird :title="t('title')">
    <BannerTeraShareYourLand />
    <div class="flex flex-col gap-4">
      <div class="flex flex-row items-center justify-between gap-4">
        <BaseHeading variant="h2">Ces espaces de culture recherches des co-jardineurs</BaseHeading>
        <RouterLink :to="RoutePageCoGardeningProposals">
          <Button
            size="sm"
            variant="ghost"
          >
            <template #icon><IconList /></template
          ></Button>
        </RouterLink>
      </div>
      <div class="flex flex-row items-center justify-between gap-4">
        <ToggleGroupTeraLandSharingConditions v-model="sharingConditionFilter" />
        <ToggleGroupTeraLandInteractionMode v-model="interactionModeFilter" />
      </div>
      <GridTeraLandProposal
        :status="status"
        :query-result="landProposals"
      />
    </div>
    <div class="flex flex-col gap-2">
      <BaseHeading variant="h2">Rechercher sur la carte</BaseHeading>
      <SectionDevelopmentInProgress />
    </div>
    <template #side>
      <div>
        <BaseHeading>Vous recherchez un terrain ?</BaseHeading>
        <p>Dîte nous ce que vous recherchez on s'occupe de diffuser votre demande.</p>
      </div>
    </template>
  </SectionTwoThird>
</template>

<script lang="ts" setup>
import SectionTwoThird from '@lychen/vue-components-app/section-two-third/SectionTwoThird.vue';
import SectionDevelopmentInProgress from '@lychen/vue-components-app/section-development-in-progress/SectionDevelopmentInProgress.vue';
import { BaseHeading } from '@lychen/vue-components-app/base-heading';
import Button from '@lychen/vue-components-core/button/Button.vue';

import IconList from '@lychen/vue-icons/IconList.vue';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import BannerTeraShareYourLand from '@/components/banners/BannerTeraShareYourLand.vue';
import { RoutePageCoGardeningProposals } from '../proposals';
import GridTeraLandProposal from '@lychen/vue-tera/components/land-proposal/grid/GridTeraLandProposal.vue';
import ToggleGroupTeraLandSharingConditions from '@lychen/vue-tera/components/land-sharing-condition/ToggleGroupTeraLandSharingConditions.vue';
import ToggleGroupTeraLandInteractionMode from '@lychen/vue-tera/components/land-interaction-mode/ToggleGroupTeraLandInteractionMode.vue';
import { type LandInteractionMode } from '@lychen/typescript-tera-core/constants/LandInteractionMode';
import { type LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useQuery } from '@tanstack/vue-query';
import { ref } from 'vue';

const { t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { api } = useTeraApi();

const interactionModeFilter = ref<LandInteractionMode[]>([]);
const sharingConditionFilter = ref<LandSharingCondition[]>([]);

const { data: landProposals, status } = useQuery({
  queryKey: ['landProposalsPublic', interactionModeFilter, sharingConditionFilter],
  queryFn: async () => {
    const response = await api.GET('/api/land_proposals/public', {
      params: {
        query: {
          'preferredInteractionMode[]': interactionModeFilter.value,
          'sharingConditions[]': sharingConditionFilter.value,
        },
      },
    });
    return response.data;
  },
});
</script>

<style lang="css" scoped></style>
