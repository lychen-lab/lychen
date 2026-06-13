<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandName :is-field-dirty="isFieldDirty('name')" />
    <FormFieldTeraLandSurface
      :is-field-dirty="isFieldDirty('surface')"
      @update:model-value="setFieldValue('surface', $event)"
    />
    <FormFieldTeraLandAltitude
      :is-field-dirty="isFieldDirty('altitude')"
      @update:model-value="setFieldValue('altitude', $event)"
    />
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
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import { landPostSucceededEvent } from '@lychen/vue-tera/events/LandEvents';
import FormFieldTeraLandName from './fields/FormFieldTeraLandName.vue';
import FormFieldTeraLandAltitude from './fields/FormFieldTeraLandAltitude.vue';
import FormFieldTeraLandSurface from './fields/FormFieldTeraLandSurface.vue';
import type { paths } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

type LandPostRequest = paths['/api/lands']['post']['requestBody']['content']['application/ld+json'];

const { isFieldDirty, handleSubmit, meta, setFieldValue } = useForm<LandPostRequest>({
  initialValues: {
    altitude: 0,
    surface: 1,
  },
});

const { emit } = useEventBus(landPostSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (data: LandPostRequest) => {
    const response = await api.POST('/api/lands', { body: data });
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
