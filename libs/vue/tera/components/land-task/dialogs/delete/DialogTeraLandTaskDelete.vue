<template>
  <DialogWithCancelButton
    :title="t(`title`)"
    :description="t(`description`)"
  >
    <slot />
    <template #content></template>
    <template #action>
      <Button
        variant="negative"
        :disabled="isPending"
        :loading="isPending"
        @click="deleteLandTask()"
      >
        {{ tLandTask(`action.delete.label`) }}
      </Button>
    </template>
  </DialogWithCancelButton>
</template>

<script lang="ts" setup>
import Button from '@lychen/vue-components-core/button/Button.vue';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import {
  messages as landTaskMessages,
  TRANSLATION_KEY as LAND_TASK_TRANSLATION_KEY,
} from '@lychen/i18n-tera/land-task';
import { messages, TRANSLATION_KEY } from './i18n';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useMutation } from '@tanstack/vue-query';
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import { useEventBus } from '@vueuse/core';
import { EVENT_landTaskDeleteSucceeded } from '@lychen/vue-tera/events/LandTaskEvents';
import DialogWithCancelButton from '@lychen/vue-components-app/dialogs/with-cancel-button/DialogWithCancelButton.vue';
import type { LandTaskJsonld } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t: tLandTask } = useI18nExtended({
  messages: landTaskMessages,
  rootKey: LAND_TASK_TRANSLATION_KEY,
  prefixed: true,
});

const { t: t } = useI18nExtended({
  messages,
  rootKey: TRANSLATION_KEY,
  prefixed: true,
});

const { emit } = useEventBus(EVENT_landTaskDeleteSucceeded);

const { landTask } = defineProps<{
  landTask: LandTaskJsonld;
}>();

const { api } = useTeraApi();

const { mutate: deleteLandTask, isPending } = useMutation({
  mutationFn: () => {
    if (!landTask.ulid) {
      throw new Error('missing.ulid');
    }
    return api.apiLandTasksUlidDelete({
      path: { ulid: landTask.ulid },
    });
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: tLandTask('action.delete.success.message'),
      variant: 'positive',
    });

    emit(landTask);
  },
  onError: (error, variables, context) => {
    toast({
      title: tLandTask('action.delete.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});
</script>
