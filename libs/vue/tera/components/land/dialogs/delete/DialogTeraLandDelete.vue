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
        @click="deleteLand()"
      >
        {{ tLand('action.delete.label') }}
      </Button>
    </template>
  </DialogWithCancelButton>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  messages as landMessages,
  TRANSLATION_KEY as LAND_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land';
import { messages, TRANSLATION_KEY } from './i18n';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useMutation } from '@tanstack/vue-query';
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import { useEventBus } from '@vueuse/core';
import { landDeleteSucceededEvent } from '@lychen/vue-tera/events/LandEvents';
import DialogWithCancelButton from '@lychen/vue-components-app/dialogs/with-cancel-button/DialogWithCancelButton.vue';
import type { LandJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t: tLand } = useI18nExtended({
  messages: landMessages,
  rootKey: LAND_TRANSLATION_KEY,
  prefixed: true,
});

const { t: t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { emit } = useEventBus(landDeleteSucceededEvent);

const { land } = defineProps<{ land: LandJsonld }>();

const { api } = useTeraApi();

const { mutate: deleteLand, isPending } = useMutation({
  mutationFn: () => {
    if (!land.ulid) {
      throw new Error('error.missing_ulid');
    }
    return api.apiLandsUlidDelete({ path: { ulid: land.ulid } });
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLand('action.delete.success.message'),
      variant: 'positive',
    });
    emit(land);
  },
  onError: (error, variables, context) => {
    toast({
      title: tLand('action.delete.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});
</script>
