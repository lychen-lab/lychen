<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandTaskTitle :is-field-dirty="isFieldDirty('title')" />
    <Button
      :disabled="!meta.valid || isPending"
      :loading="isPending"
      type="submit"
      class="self-end"
      :label="t('action.create.label')"
    />
  </form>
</template>

<script lang="ts" setup>
import { toast } from '@lychen/vue-components-core/toast/use-toast';
import Button from '@lychen/vue-components-core/button/Button.vue';
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-task';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import FormFieldTeraLandTaskTitle from './fields/FormFieldTeraLandTaskTitle.vue';
import type { components, paths } from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import { EVENT_landTaskPostSucceeded } from '@lychen/vue-tera/events/LandTaskEvents';

const { land } = defineProps<{ land: components['schemas']['Land.jsonld'] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

type LandTaskPostRequest =
  paths['/api/land_tasks']['post']['requestBody']['content']['application/ld+json'];

const { isFieldDirty, handleSubmit, meta } = useForm<LandTaskPostRequest>({
  initialValues: {
    land: land['@id'],
  },
});

const { emit } = useEventBus(EVENT_landTaskPostSucceeded);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (data: LandTaskPostRequest) => {
    const response = await api.POST('/api/land_tasks', {
      body: data,
    });
    return response.data;
  },
  onSuccess: (data, variables, context) => {
    toast({
      title: t('action.create.success.message'),
      variant: 'positive',
    });
    emit(data);
  },
  onError: (error, variables, context) => {
    toast({
      title: t('action.create.error.message'),
      description: error.message,
      variant: 'negative',
    });
  },
});

const onSubmit = handleSubmit((values) => {
  mutate(values);
});
</script>
