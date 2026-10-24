<template>
  <DialogWithCancelButton
    :title="t(`${leave ? 'leave.' : ''}title`)"
    :description="t(`${leave ? 'leave.' : ''}description`)"
  >
    <slot />
    <template #content></template>
    <template #action>
      <Button
        variant="negative"
        :disabled="isPending"
        :loading="isPending"
        @click="deleteLandMember()"
      >
        {{ tLandMember(`action.${leave ? 'leave' : 'delete'}.label`) }}
      </Button>
    </template>
  </DialogWithCancelButton>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  messages as landMemberMessages,
  TRANSLATION_KEY as LAND_MEMBER_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-member';
import { messages, TRANSLATION_KEY } from './i18n';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useMutation } from '@tanstack/vue-query';
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import { useEventBus } from '@vueuse/core';
import {
  landMemberDeleteSucceededEvent,
  landMemberLeaveSucceededEvent,
} from '@lychen/vue-tera/events/LandMemberEvents';
import DialogWithCancelButton from '@lychen/vue-components-app/dialogs/with-cancel-button/DialogWithCancelButton.vue';
import type { LandMemberJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t: tLandMember } = useI18nExtended({
  messages: landMemberMessages,
  rootKey: LAND_MEMBER_TRANSLATION_KEY,
  prefixed: true,
});

const { t: t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { emit } = useEventBus(landMemberDeleteSucceededEvent);
const { emit: emitLeave } = useEventBus(landMemberLeaveSucceededEvent);

const { landMember, leave = false } = defineProps<{
  landMember: Omit<LandMemberJsonld, 'landRoles'>;
  leave?: boolean;
}>();

const { api } = useTeraApi();

const { mutate: deleteLandMember, isPending } = useMutation({
  mutationFn: () => {
    if (!landMember.ulid) {
      throw new Error('missing.ulid');
    }
    return api.apiLandMembersUlidDelete({
      path: { ulid: landMember.ulid },
    });
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLandMember('action.delete.success.message'),
      variant: 'positive',
    });

    if (leave) {
      emitLeave(landMember);
    } else {
      emit(landMember);
    }
  },
  onError: (error, variables, context) => {
    toast({
      title: tLandMember('action.delete.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});
</script>
