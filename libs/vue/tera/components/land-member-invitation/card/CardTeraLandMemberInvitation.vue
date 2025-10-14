<template>
  <Card
    class="flex flex-row items-center justify-between gap-2 p-4"
    hoverable
  >
    <div class="flex flex-col gap-1">
      <p
        v-if="landMemberInvitation.email"
        class="font-medium"
      >
        {{ landMemberInvitation.email }}
      </p>
      <template v-if="variant === VARIANT.ForUser">
        <small class="opacity-80">{{ t('invite_sentence.prefix') }}</small>
        <BaseHeading
          v-if="land"
          variant="h3"
          >{{ land.name }}</BaseHeading
        >
        <small
          v-if="variant === VARIANT.ForUser"
          class="opacity-80"
          >{{ t('invite_sentence.suffix') }}
        </small>
      </template>

      <div
        v-if="landRoles"
        class="flex flex-row gap-2"
      >
        <BadgeTeraLandRole
          v-for="(item, index) in landRoles"
          :key="index"
          :land-role="item"
        />
      </div>
    </div>
    <BadgeTeraLandMemberInvitation
      v-if="landMemberInvitation.state"
      :state="landMemberInvitation.state"
    />
    <div class="flex flex-row gap-2">
      <DialogTeraLandMemberInvitationDelete
        v-if="variant === VARIANT.Settings && landMemberInvitation"
        :land-member-invitation="landMemberInvitation"
      >
        <Button
          variant="negative"
          size="sm"
          @click.stop
        >
          <template #icon>
            <IconTrash />
          </template>
        </Button>
      </DialogTeraLandMemberInvitationDelete>
      <template v-if="variant === VARIANT.ForUser">
        <Button
          variant="positive"
          size="sm"
          @click="accept"
        >
          <template #icon>
            <IconCheck />
          </template>
        </Button>
        <Button
          variant="negative"
          size="sm"
          @click="refuse"
        >
          <template #icon>
            <IconTimes />
          </template> </Button
      ></template>
    </div>
  </Card>
</template>

<script setup lang="ts">
import Card from '@lychen/vue-components-core/card/Card.vue';
import BadgeTeraLandRole from '../../land-role/badge/BadgeTeraLandRole.vue';
import DialogTeraLandMemberInvitationDelete from '../dialogs/delete/DialogTeraLandMemberInvitationDelete.vue';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { VARIANT, type Variant } from '.';
import BadgeTeraLandMemberInvitation from '../badge/BadgeTeraLandMemberInvitation.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { defineAsyncComponent } from 'vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { TRANSLATION_KEY, messages } from './i18n';
import {
  TRANSLATION_KEY as LAND_MEMBER_INVITATION_TRANSLATION_KEY,
  messages as messagesLandMemberInvitations,
} from '@lychen/i18n-tera/land-member-invitation';
import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { toast } from '@lychen/vue-components-core/toast';
import {
  landMemberInvitationAcceptSucceededEvent,
  landMemberInvitationRefuseSucceededEvent,
} from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import { useEventBus } from '@vueuse/core';
import IconTimes from '@lychen/vue-icons/IconTimes.vue';
import IconTrash from '@lychen/vue-icons/IconTrash.vue';
import IconCheck from '@lychen/vue-icons/IconCheck.vue';

const BaseHeading = defineAsyncComponent(
  () => import('@lychen/vue-components-app/base-heading/BaseHeading.vue'),
);

const {
  variant = VARIANT.Settings,
  landMemberInvitation,
  landRoles = undefined,
  land = undefined,
} = defineProps<{
  variant?: Variant;
  landMemberInvitation: Partial<
    Pick<components['schemas']['LandMemberInvitation.jsonld'], 'email' | 'state' | 'ulid'>
  >;
  landRoles?: Pick<components['schemas']['LandRole.jsonld'], 'name'>[];
  land?: Pick<components['schemas']['Land.jsonld'], 'name'>;
}>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { t: tLandMemberInvitation } = useI18nExtended({
  messages: messagesLandMemberInvitations,
  rootKey: LAND_MEMBER_INVITATION_TRANSLATION_KEY,
  prefixed: true,
});

const { api } = useTeraApi();
const { emit: emitAccept } = useEventBus(landMemberInvitationAcceptSucceededEvent);

const { mutate: accept } = useMutation({
  mutationFn: async () => {
    if (!landMemberInvitation.ulid) {
      throw new Error('missing.ulid');
    }
    const response = await api.PATCH('/api/land_member_invitations/{ulid}/accept', {
      params: { path: { ulid: landMemberInvitation.ulid } },
      body: {},
    });

    return response.data;
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLandMemberInvitation('action.accept.success.message'),
      variant: 'positive',
    });
    emitAccept(data);
  },
  onError: (error, variables, context) => {
    toast({
      title: tLandMemberInvitation('action.accept.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});

const { emit: emitRefuse } = useEventBus(landMemberInvitationRefuseSucceededEvent);
const { mutate: refuse } = useMutation({
  mutationFn: async () => {
    if (!landMemberInvitation.ulid) {
      throw new Error('missing.ulid');
    }
    const response = await api.PATCH('/api/land_member_invitations/{ulid}/refuse', {
      params: {
        path: { ulid: landMemberInvitation.ulid },
      },
      body: {},
    });

    return response.data;
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLandMemberInvitation('action.refuse.success.message'),
      variant: 'positive',
    });
    emitRefuse(data);
  },
  onError: (error, variables, context) => {
    toast({
      title: tLandMemberInvitation('action.refuse.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});
</script>
