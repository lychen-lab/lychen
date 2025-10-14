<template>
  <SectionWithTitle>
    <template #title>
      <DivWithBackgroundImg
        :background-image="bannerImg"
        overlay
        overlay-class="bg-gradient-to-tr from-secondary-container to-secondary-container/30"
        class="flex flex-col justify-between gap-4 rounded-xl px-10 py-20 md:flex-row md:py-30"
      >
        <div class="z-10 flex flex-col gap-8 md:flex-row md:items-center md:justify-between">
          <div class="flex flex-col gap-1">
            <BaseHeading class="text-on-secondary-container z-10">{{ t('title') }} </BaseHeading>
            <p
              v-if="lands?.totalItems"
              class="text-on-secondary-container font-medium opacity-80"
            >
              {{ t('sub_title', lands.totalItems) }}
            </p>
          </div>
        </div>
        <div class="z-10 flex flex-col gap-4 md:flex-row md:items-center">
          <div>
            <DialogTeraLandCreate v-model:open="open">
              <Button
                :label="t('add_land')"
                size="sm"
              >
                <template #icon>
                  <IconPlus />
                </template>
              </Button>
            </DialogTeraLandCreate>
          </div>
          <div>
            <RouterLink :to="RoutePageCoGardening">
              <Button
                :label="t('search_land')"
                size="sm"
              >
                <template #icon>
                  <IconSearch />
                </template>
              </Button>
            </RouterLink>
          </div>
        </div>
      </DivWithBackgroundImg>
    </template>
    <div class="flex flex-col gap-4">
      <div class="flex flex-row items-center justify-between">
        <div class="flex flex-row items-center gap-2"></div>
      </div>

      <div
        v-if="lands?.member || landMemberInvitations?.member"
        class="grid auto-rows-(--grid-rows-fluid) grid-cols-(--grid-fluid) gap-8"
      >
        <template v-if="landMemberInvitations?.member">
          <CardTeraLandMemberInvitation
            v-for="landMemberInvitation in landMemberInvitations.member"
            :key="landMemberInvitation.ulid"
            :land-member-invitation="landMemberInvitation"
            :land-roles="landMemberInvitation.landRoles"
            :land="landMemberInvitation.land"
            :variant="VARIANT.ForUser"
            :hoverable="false"
            class="outline-secondary-container/40 from-surface-container to-secondary-container border-0 bg-gradient-to-tr outline outline-offset-4"
          />
        </template>
        <template v-if="lands?.member">
          <RouterLink
            v-for="land in lands.member"
            :key="land.ulid"
            :to="{ name: RoutePageLandDashboard.name, params: { landUlid: land.ulid } }"
          >
            <CardTeraLand
              :name="land.name"
              :altitude="land.altitude"
              :surface="land.surface"
              :number-of-area="land.landAreas?.length"
              :number-of-member="land.landMembers?.length"
            />
          </RouterLink>
        </template>
      </div>
    </div>
  </SectionWithTitle>
</template>

<script lang="ts" setup>
import bannerImg from './assets/banner.webp';
import CardTeraLand from '@lychen/vue-tera/components/land/card/CardTeraLand.vue';
import { RoutePageLandDashboard } from '@/pages/land/dashboard';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useQuery } from '@tanstack/vue-query';
import SectionWithTitle from '@lychen/vue-components-app/section-with-title/SectionWithTitle.vue';
import { useEventBus } from '@vueuse/core';
import { landPostSucceededEvent } from '@lychen/vue-tera/events/LandEvents';
import { ref, defineAsyncComponent } from 'vue';
import DialogTeraLandCreate from '@lychen/vue-tera/components/land/dialogs/create/DialogTeraLandCreate.vue';
import BaseHeading from '@lychen/vue-components-app/base-heading/BaseHeading.vue';
import { messages, TRANSLATION_KEY } from './i18n';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import CardTeraLandMemberInvitation from '@lychen/vue-tera/components/land-member-invitation/card/CardTeraLandMemberInvitation.vue';
import zitadelAuth from '@lychen/typescript-zitadel/ZitadelAuth';
import { VARIANT } from '@lychen/vue-tera/components/land-member-invitation/card';
import {
  landMemberInvitationAcceptSucceededEvent,
  landMemberInvitationRefuseSucceededEvent,
} from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import { LandMemberInvitationJsonldState as LandMemberInvitationState } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { RoutePageCoGardening } from '../co-gardening/dashboard';
import IconPlus from '@lychen/vue-icons/IconPlus.vue';
import IconSearch from '@lychen/vue-icons/IconSearch.vue';

const DivWithBackgroundImg = defineAsyncComponent(
  () => import('@lychen/vue-components-extra/div-with-background-img/DivWithBackgroundImg.vue'),
);

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const open = ref(false);

const { api } = useTeraApi();

const { data: lands, refetch } = useQuery({
  queryKey: ['lands'],
  queryFn: async () => {
    const response = await api.GET('/api/lands');
    return response.data;
  },
});

const email = zitadelAuth.oidcAuth.userProfile.email;

const { data: landMemberInvitations, refetch: refetchLandMemberInvitations } = useQuery({
  queryKey: ['landMemberInvitations'],
  queryFn: async () => {
    if (!email) {
      throw new Error('missing.email');
    }
    const response = await api.GET('/api/land_member_invitations/by_email', {
      params: {
        query: {
          email,
          state: LandMemberInvitationState.pending,
        },
      },
    });
    return response.data;
  },
});

const { on } = useEventBus(landPostSucceededEvent);
on(() => {
  refetch();
  open.value = false;
});

const { on: onLandMemberInvitationAccept } = useEventBus(landMemberInvitationAcceptSucceededEvent);
const { on: onLandMemberInvitationRefuse } = useEventBus(landMemberInvitationRefuseSucceededEvent);

onLandMemberInvitationAccept(() => {
  refetch();
  refetchLandMemberInvitations();
});

onLandMemberInvitationRefuse(() => {
  refetch();
  refetchLandMemberInvitations();
});
</script>

<style lang="css" scoped></style>
