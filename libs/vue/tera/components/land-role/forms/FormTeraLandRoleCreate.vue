<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandRoleName :is-field-dirty="isFieldDirty('name')" />
    <FormFieldTeraPermissions
      :is-field-dirty="isFieldDirty('permissions')"
      @update:model-value="setFieldValue('permissions', $event)"
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
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-role';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import { landRolePostSucceededEvent } from '@lychen/vue-tera/events/LandRoleEvents';
import FormFieldTeraLandRoleName from './fields/FormFieldTeraLandRoleName.vue';
import FormFieldTeraPermissions from '../../permission/form/field/FormFieldTeraPermissions.vue';
import type { components, paths } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { land } = defineProps<{ land: components['schemas']['Land.jsonld'] }>();

type FormType = paths['/api/land_roles']['post']['requestBody']['content']['application/ld+json'];

const { isFieldDirty, handleSubmit, meta, setFieldValue } = useForm<FormType>({
  initialValues: {
    permissions: undefined,
  },
});

const { emit } = useEventBus(landRolePostSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (data: FormType) => {
    if (!land['@id']) {
      throw new Error('missing.@id');
    }
    const response = await api.POST('/api/land_roles', { body: { ...data, land: land['@id']! } });
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
