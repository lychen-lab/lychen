<template>
  <DialogWithCancelButton
    :title="t('title')"
    :description="t('description')"
  >
    <slot />
    <template #content></template>
    <template #action>
      <Button
        variant="negative"
        :disabled="isPending"
        :loading="isPending"
        @click="deleteLandMemberInvitation()"
      >
        {{ tLandMemberInvitation('action.delete.label') }}
      </Button>
    </template>
  </DialogWithCancelButton>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  messages as landMemberInvitationMessages,
  TRANSLATION_KEY as LAND_MEMBER_INVITATION_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-member-invitation';
import { messages, TRANSLATION_KEY } from './i18n';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useMutation } from '@tanstack/vue-query';
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import { useEventBus } from '@vueuse/core';
import { landMemberInvitationDeleteSucceededEvent } from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import DialogWithCancelButton from '@lychen/vue-components-app/dialogs/with-cancel-button/DialogWithCancelButton.vue';
import type { LandMemberInvitationJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t: tLandMemberInvitation } = useI18nExtended({
  messages: landMemberInvitationMessages,
  rootKey: LAND_MEMBER_INVITATION_TRANSLATION_KEY,
  prefixed: true,
});

const { t: t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { emit } = useEventBus(landMemberInvitationDeleteSucceededEvent);

const { landMemberInvitation } = defineProps<{
  landMemberInvitation: Pick<LandMemberInvitationJsonld, 'ulid'>;
}>();

const { api } = useTeraApi();

const { mutate: deleteLandMemberInvitation, isPending } = useMutation({
  mutationFn: () => {
    if (!landMemberInvitation.ulid) {
      throw new Error('missing.ulid');
    }
    return api.apiLandMemberInvitationsUlidDelete({
      path: { ulid: landMemberInvitation.ulid },
    });
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLandMemberInvitation('action.delete.success.message'),
      variant: 'positive',
    });
    emit();
  },
  onError: (error, variables, context) => {
    toast({
      title: tLandMemberInvitation('action.delete.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});
</script>
