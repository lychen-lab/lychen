<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandTaskTitle :is-field-dirty="isFieldDirty('title')" />
    <div class="grid grid-cols-2">
      <ul>
        <li>Status</li>
        <li>Date de début</li>
        <li>Date de fin</li>
      </ul>
    </div>
    <div>Content</div>
    <div>
      <BaseHeading variant="h3">Land Area</BaseHeading>
    </div>
    <Button
      :disabled="!meta.valid || isPending || !meta.dirty"
      :loading="isPending"
      size="sm"
      type="submit"
      class="self-end"
      :label="t('action.update.label')"
    />
  </form>
</template>

<script lang="ts" setup>
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import Button from '@lychen/vue-components-core/button/Button.vue';
import BaseHeading from '@lychen/vue-components-app/base-heading/BaseHeading.vue';
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-task';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';

import { EVENT_landTaskPatchSucceeded } from '@lychen/vue-tera/events/LandTaskEvents';
import FormFieldTeraLandTaskTitle from './fields/FormFieldTeraLandTaskTitle.vue';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { landTask } = defineProps<{
  landTask: components['schemas']['LandTask.jsonld'];
}>();

interface FormType {
  title: components['schemas']['LandTask.jsonld']['title'];
}

const { handleSubmit, meta, isFieldDirty } = useForm<FormType>({
  initialValues: {
    title: landTask.title || '',
  },
});

const { emit } = useEventBus(EVENT_landTaskPatchSucceeded);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (values: FormType) => {
    if (!landTask.ulid) {
      throw new Error('missing.ulid');
    }
    const response = await api.PATCH('/api/land_tasks/{ulid}', {
      params: { path: { ulid: landTask.ulid } },
      body: values,
    });
    return response.data;
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: t('action.update.success.message'),
      variant: 'positive',
    });
    emit(data);
  },
  onError: (error, variables, context) => {
    toast({
      title: t('action.update.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});

const onSubmit = handleSubmit((values) => {
  mutate(values);
});
</script>
