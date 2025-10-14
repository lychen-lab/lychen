<template>
  <Card
    hoverable
    class="grid items-center gap-4 md:grid-cols-[auto_1fr_auto_auto]"
  >
    <div class="bg-surface-container-high flex size-12 items-center justify-center rounded-full">
      <IconUser class="p-2" />
    </div>
    <div class="flex flex-col">
      <BaseHeading variant="h3">{{ title }}</BaseHeading>
      <div class="flex flex-row gap-2 opacity-70">
        <Badge
          v-if="city"
          size="sm"
          variant="outline"
        >
          <IconMapPin />
          {{ city }}
        </Badge>
        <Badge
          v-if="expirationDate"
          size="sm"
          :variant="isCloseToExpire ? 'warning' : 'default'"
        >
          <IconClock />
          {{ d(expirationDate, 'short') }}
        </Badge>
      </div>
    </div>

    <TeraLandProposalSharingConditions
      v-if="sharingConditions"
      :sharing-conditions="sharingConditions"
    />

    <div>
      <Tooltip>
        <TooltipTrigger>
          <Icon
            v-if="preferredInteractionMode"
            :icon="LAND_INTERACTION_MODE_ICON[preferredInteractionMode]"
            class="p-2"
          />
        </TooltipTrigger>
        <TooltipContent>
          {{ t(`property.preferred_interaction_mode.options.${preferredInteractionMode}`) }}
        </TooltipContent>
      </Tooltip>
    </div>
  </Card>
</template>

<script lang="ts" setup>
import { defineAsyncComponent } from 'vue';

import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-proposal';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { Tooltip, TooltipContent, TooltipTrigger } from '@lychen/vue-components-core/tooltip';
import IconClock from '@lychen/vue-icons/IconClock.vue';
import IconUser from '@lychen/vue-icons/IconUser.vue';
import IconMapPin from '@lychen/vue-icons/IconMapPin.vue';
import TeraLandProposalSharingConditions from '../../common/sharing-conditions-icons/TeraLandProposalSharingConditions.vue';
import { type LandInteractionMode } from '@lychen/typescript-tera-core/constants/LandInteractionMode';
import type { LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';
import { LAND_INTERACTION_MODE_ICON } from '@lychen/vue-tera/components/icons/IconLandInteractionMode';
import { useCloseToExpire } from '@lychen/vue-tera/composables/use-close-to-expire';

const Card = defineAsyncComponent(() => import('@lychen/vue-components-core/card/Card.vue'));

const BaseHeading = defineAsyncComponent(
  () => import('@lychen/vue-components-app/base-heading/BaseHeading.vue'),
);

const Badge = defineAsyncComponent(() => import('@lychen/vue-components-core/badge/Badge.vue'));

const props = defineProps<{
  /**
   * The main title of the land request card.
   */
  title?: string;
  /**
   * The city where the land is requested.
   */
  city?: string | null;
  /**
   * The expiration date of the request in ISO format.
   */
  expirationDate?: string | null;
  /**
   * The preferred mode of interaction for the request.
   */
  preferredInteractionMode?: LandInteractionMode;
  /**
   * An array of sharing conditions applicable to the land request.
   */
  sharingConditions?: LandSharingCondition[] | null;
}>();

const { t, d } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { isCloseToExpire } = useCloseToExpire(props.expirationDate);
</script>
