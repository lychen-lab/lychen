<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandName :is-field-dirty="isFieldDirty('name')" />
    <div class="gap-4 md:grid md:grid-cols-2">
      <FormFieldTeraLandSurface
        :is-field-dirty="isFieldDirty('surface')"
        @update:model-value="setFieldValue('surface', $event)"
      />
      <FormFieldTeraLandAltitude
        :is-field-dirty="isFieldDirty('altitude')"
        @update:model-value="setFieldValue('altitude', $event)"
      />
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
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import FormFieldTeraLandName from './fields/FormFieldTeraLandName.vue';
import FormFieldTeraLandAltitude from './fields/FormFieldTeraLandAltitude.vue';
import FormFieldTeraLandSurface from './fields/FormFieldTeraLandSurface.vue';
import { landPatchSucceededEvent } from '@lychen/vue-tera/events/LandEvents';
import type { components, paths } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { land } = defineProps<{ land: components['schemas']['Land.jsonld'] }>(); //TODO check if necessary to link to generated code

type LandPatchRequest =
  paths['/api/lands/{ulid}']['patch']['requestBody']['content']['application/merge-patch+json'];

const { isFieldDirty, handleSubmit, meta, setFieldValue } = useForm<LandPatchRequest>({
  initialValues: {
    altitude: land.altitude || 0,
    surface: land.surface || 1,
    name: land.name,
  },
});

const { emit } = useEventBus(landPatchSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (data: LandPatchRequest) => {
    if (!land.ulid) {
      throw new Error('missing.land_ulid');
    }
    const response = await api.PATCH('/api/lands/{ulid}', {
      params: { path: { ulid: land.ulid } },
      body: data,
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
