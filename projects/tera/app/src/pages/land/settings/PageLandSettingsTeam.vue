<template>
  <SectionSetting
    :title="t('tabs.team.members.title')"
    :description="t('tabs.team.members.description')"
  >
    <div
      v-if="landMembers?.member && land"
      class="flex flex-col gap-4"
    >
      <CardTeraLandMember
        v-if="owner"
        :land-member="owner"
        :hoverable="false"
      />
      <DialogTeraLandMemberUpdate
        v-for="(item, index) in landMembers.member.filter((item) => !item.owner)"
        :key="index"
        :land-member="item"
        :land="land"
      >
        <CardTeraLandMember :land-member="item" />
      </DialogTeraLandMemberUpdate>
    </div>
  </SectionSetting>
  <SectionSetting
    :title="t('tabs.team.invitations.title')"
    :description="t('tabs.team.invitations.description')"
  >
    <template #subTitle>
      <DialogTeraLandMemberInvitationCreate
        v-if="land"
        :land="land"
      >
        <Button
          variant="outline"
          class="self-start"
          :label="tLandMemberInvitation('action.create.label')"
        >
          <template #icon><IconPlus /></template
        ></Button>
      </DialogTeraLandMemberInvitationCreate>
    </template>
    <div
      v-if="landMemberInvitations?.member && landMemberInvitations.member.length !== 0 && land"
      class="flex flex-col gap-4"
    >
      <DialogTeraLandMemberInvitationUpdate
        v-for="(item, index) in landMemberInvitations.member"
        :key="index"
        :land-member-invitation="item"
        :land="land"
      >
        <CardTeraLandMemberInvitation
          :land-member-invitation="item"
          :land-roles="item.landRoles"
        />
      </DialogTeraLandMemberInvitationUpdate>
    </div>
    <div
      v-else
      class="flex h-full flex-row items-center justify-center opacity-70"
    >
      {{ t('tabs.team.invitations.none') }}
    </div>
  </SectionSetting>
  <SectionSetting
    :title="t('tabs.team.roles.title')"
    :description="t('tabs.team.roles.description')"
  >
    <template #subTitle>
      <DialogTeraLandRoleCreate
        v-if="land"
        :land="land"
      >
        <Button
          variant="outline"
          class="self-start"
          :label="tLandRole('action.create.label')"
        >
          <template #icon><IconPlus /></template
        ></Button>
      </DialogTeraLandRoleCreate>
    </template>
    <div
      v-if="landRoles?.member"
      class="flex flex-col gap-4"
    >
      <DialogTeraLandRoleUpdate
        v-for="(item, index) in landRoles.member"
        :key="index"
        :land-role="item"
      >
        <CardTeraLandRole :land-role="item" />
      </DialogTeraLandRoleUpdate>
    </div>
  </SectionSetting>
</template>

<script setup lang="ts">
import { SectionSetting } from '@lychen/vue-components-app/section-setting';

import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { messages, TRANSLATION_KEY } from './i18n';
import {
  messages as landRoleMessages,
  TRANSLATION_KEY as LAND_ROLE_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-role';
import {
  messages as landMemberInvitationMessages,
  TRANSLATION_KEY as LAND_MEMBER_INVITATION_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-member-invitation';
import { computed, inject, onUnmounted } from 'vue';
import { INJECTION_KEY_LAND } from '@/layouts/land-layout';
import { useQuery } from '@tanstack/vue-query';
import IconPlus from '@lychen/vue-icons/IconPlus.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import CardTeraLandRole from '@lychen/vue-tera/components/land-role/card/CardTeraLandRole.vue';
import DialogTeraLandRoleCreate from '@lychen/vue-tera/components/land-role/dialogs/create/DialogTeraLandRoleCreate.vue';
import DialogTeraLandMemberInvitationCreate from '@lychen/vue-tera/components/land-member-invitation/dialogs/create/DialogTeraLandMemberInvitationCreate.vue';
import DialogTeraLandRoleUpdate from '@lychen/vue-tera/components/land-role/dialogs/update/DialogTeraLandRoleUpdate.vue';
import DialogTeraLandMemberInvitationUpdate from '@lychen/vue-tera/components/land-member-invitation/dialogs/update/DialogTeraLandMemberInvitationUpdate.vue';

import {
  landRolePostSucceededEvent,
  landRolePatchSucceededEvent,
  landRoleDeleteSucceededEvent,
} from '@lychen/vue-tera/events/LandRoleEvents';
import { useEventBus } from '@vueuse/core';
import CardTeraLandMember from '@lychen/vue-tera/components/land-member/card/CardTeraLandMember.vue';
import DialogTeraLandMemberUpdate from '@lychen/vue-tera/components/land-member/dialogs/update/DialogTeraLandMemberUpdate.vue';
import {
  landMemberDeleteSucceededEvent,
  landMemberPatchSucceededEvent,
} from '@lychen/vue-tera/events/LandMemberEvents';
import {
  landMemberInvitationDeleteSucceededEvent,
  landMemberInvitationPatchSucceededEvent,
  landMemberInvitationPostSucceededEvent,
} from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import CardTeraLandMemberInvitation from '@lychen/vue-tera/components/land-member-invitation/card/CardTeraLandMemberInvitation.vue';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';

const land = inject(INJECTION_KEY_LAND);
const landUlid = computed(() => land?.value?.ulid);
const enabled = computed(() => !!landUlid.value);

const { t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { t: tLandRole } = useI18nExtended({
  messages: landRoleMessages,
  rootKey: LAND_ROLE_TRANSLATION_KEY,
  prefixed: true,
});

const { t: tLandMemberInvitation } = useI18nExtended({
  messages: landMemberInvitationMessages,
  rootKey: LAND_MEMBER_INVITATION_TRANSLATION_KEY,
  prefixed: true,
});

const { api } = useTeraApi();

const { data: landRoles, refetch: refetchLandRoles } = useQuery({
  queryKey: ['landRoles', landUlid],
  queryFn: async () => {
    if (!landUlid.value) {
      throw new Error('missing.land_id');
    }

    const response = await api.GET('/api/land_roles', {
      params: {
        query: {
          land: landUlid.value,
        },
      },
    });

    return response.data;
  },
  enabled,
});

const { on: onLandRolePost } = useEventBus(landRolePostSucceededEvent);
const { on: onLandRolePatch } = useEventBus(landRolePatchSucceededEvent);
const { on: onLandRoleDelete } = useEventBus(landRoleDeleteSucceededEvent);

const unsubscribeOnLandRolePost = onLandRolePost(() => {
  refetchLandRoles();
});

const unsubscribeOnLandRolePatch = onLandRolePatch(() => {
  refetchLandRoles();
});

const unsubscribeOnLandRoleDelete = onLandRoleDelete(() => {
  refetchLandRoles();
});

const { data: landMemberInvitations, refetch: refetchLandMemberInvitations } = useQuery({
  queryKey: ['landMemberInvitations', landUlid],
  queryFn: async () => {
    if (!landUlid.value) {
      throw new Error('missing.land_id');
    }

    const response = await api.GET('/api/land_member_invitations', {
      params: {
        query: {
          land: landUlid.value,
        },
      },
    });

    return response.data;
  },
  enabled,
});

const { on: onLandMemberInvitationPost } = useEventBus(landMemberInvitationPostSucceededEvent);
const { on: onLandMemberInvitationPatch } = useEventBus(landMemberInvitationPatchSucceededEvent);
const { on: onLandMemberInvitationDelete } = useEventBus(landMemberInvitationDeleteSucceededEvent);

const unsubscribeOnLandMemberInvitationPost = onLandMemberInvitationPost(() => {
  refetchLandMemberInvitations();
});

const unsubscribeOnLandMemberInvitationPatch = onLandMemberInvitationPatch(() => {
  refetchLandMemberInvitations();
});

const unsubscribeOnLandMemberInvitationDelete = onLandMemberInvitationDelete(() => {
  refetchLandMemberInvitations();
});

const { data: landMembers, refetch: refetchLandMembers } = useQuery({
  queryKey: ['landMembers', landUlid],
  queryFn: async () => {
    if (!landUlid.value) {
      throw new Error('missing.land_id');
    }

    const response = await api.GET('/api/land_members', {
      params: {
        query: {
          land: landUlid.value,
        },
      },
    });

    return response.data;
  },
  enabled,
});

const owner = computed(() => {
  if (landMembers.value) {
    return landMembers.value.member.find((item) => item.owner);
  }
  return null;
});

const { on: onLandMemberPatch } = useEventBus(landMemberPatchSucceededEvent);
const { on: onLandMemberDelete } = useEventBus(landMemberDeleteSucceededEvent);

const unsubscribeOnLandMemberPatch = onLandMemberPatch(() => {
  refetchLandMembers();
});

const unsubscribeOnLandMemberDelete = onLandMemberDelete(() => {
  refetchLandMembers();
});

onUnmounted(() => {
  unsubscribeOnLandMemberPatch();
  unsubscribeOnLandMemberDelete();
  unsubscribeOnLandMemberInvitationPost();
  unsubscribeOnLandMemberInvitationPatch();
  unsubscribeOnLandMemberInvitationDelete();
  unsubscribeOnLandRolePost();
  unsubscribeOnLandRolePatch();
  unsubscribeOnLandRoleDelete();
});
</script>
