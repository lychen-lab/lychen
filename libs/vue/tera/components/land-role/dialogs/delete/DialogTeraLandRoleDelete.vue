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
        @click="deleteLandRole()"
      >
        {{ tLandRole('action.delete.label') }}
      </Button>
    </template>
  </DialogWithCancelButton>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  messages as landRoleMessages,
  TRANSLATION_KEY as LAND_ROLE_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-role';
import { messages, TRANSLATION_KEY } from './i18n';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useMutation } from '@tanstack/vue-query';
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import { useEventBus } from '@vueuse/core';
import { landRoleDeleteSucceededEvent } from '@lychen/vue-tera/events/LandRoleEvents';
import DialogWithCancelButton from '@lychen/vue-components-app/dialogs/with-cancel-button/DialogWithCancelButton.vue';
import type { LandRoleJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t: tLandRole } = useI18nExtended({
  messages: landRoleMessages,
  rootKey: LAND_ROLE_TRANSLATION_KEY,
  prefixed: true,
});

const { t: t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { emit } = useEventBus(landRoleDeleteSucceededEvent);

const { landRole } = defineProps<{ landRole: LandRoleJsonld }>();

const { api } = useTeraApi();

const { mutate: deleteLandRole, isPending } = useMutation({
  mutationFn: () => {
    if (!landRole.ulid) {
      throw new Error('missing.ulid');
    }
    return api.apiLandRolesUlidDelete({ path: { ulid: landRole.ulid } });
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLandRole('action.delete.success.message'),
      variant: 'positive',
    });
    emit(landRole);
  },
  onError: (error, variables, context) => {
    toast({
      title: tLandRole('action.delete.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});
</script>
