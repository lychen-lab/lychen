<template>
  <Card
    hoverable
    class="grid items-center gap-4 md:grid-cols-[auto_1fr_auto_auto]"
  >
    <div class="bg-surface-container-high flex size-12 items-center justify-center rounded-full">
      <IconFence class="p-2" />
    </div>

    <div class="flex flex-col">
      <div class="flex flex-row gap-2">
        <Badge
          v-if="landCity"
          size="sm"
          variant="outline"
        >
          <IconMapPin />
          {{ landCity }}
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
      <BaseHeading variant="h3">{{ title }}</BaseHeading>
      <div class="flex flex-row gap-2 opacity-70">
        <p>{{ landName }}</p>
        <p v-if="landSurface">• {{ tLand('property.surface.default', landSurface) }}</p>
        <p v-if="landAltitude">
          •
          <IconMountain />
          {{ tLand('property.altitude.default', landAltitude) }}
        </p>
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
import {
  messages as landMessages,
  TRANSLATION_KEY as LAND_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { Tooltip, TooltipContent, TooltipTrigger } from '@lychen/vue-components-core/tooltip';
import IconClock from '@lychen/vue-icons/IconClock.vue';
import IconMountain from '@lychen/vue-icons/IconMountain.vue';
import IconMapPin from '@lychen/vue-icons/IconMapPin.vue';
import IconFence from '@lychen/vue-icons/IconFence.vue';
import TeraLandProposalSharingConditions from '../../common/sharing-conditions-icons/TeraLandProposalSharingConditions.vue';
import { LAND_INTERACTION_MODE_ICON } from '../../icons/IconLandInteractionMode';
import type { LandInteractionMode } from '@lychen/typescript-tera-core/constants/LandInteractionMode';
import type { LandSharingCondition } from '@lychen/typescript-tera-core/constants/LandSharingCondition';
import { useCloseToExpire } from '@lychen/vue-tera/composables/use-close-to-expire';

const Card = defineAsyncComponent(() => import('@lychen/vue-components-core/card/Card.vue'));

const BaseHeading = defineAsyncComponent(
  () => import('@lychen/vue-components-app/base-heading/BaseHeading.vue'),
);

const Badge = defineAsyncComponent(() => import('@lychen/vue-components-core/badge/Badge.vue'));

const props = defineProps<{
  /**
   * The main title of the land proposal card.
   */
  title?: string;
  /**
   * The name of the associated land.
   */
  landName?: string;
  /**
   * The surface area of the land (e.g., in square meters).
   */
  landSurface?: number | null;
  /**
   * The altitude of the land (e.g., in meters).
   */
  landAltitude?: number | null;
  /**
   * The city where the land is located.
   */
  landCity?: string | null;
  /**
   * The expiration date of the proposal in ISO format.
   */
  expirationDate?: string | null;
  /**
   * The preferred mode of interaction.
   */
  preferredInteractionMode?: LandInteractionMode;
  /**
   * An array of sharing conditions applicable to the land proposal.
   */
  sharingConditions?: LandSharingCondition[] | null;
}>();

const { t, d } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { t: tLand } = useI18nExtended({
  messages: landMessages,
  rootKey: LAND_TRANSLATION_KEY,
  prefixed: true,
});

const { isCloseToExpire } = useCloseToExpire(props.expirationDate);
</script>
