<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandRole
      :is-field-dirty="isFieldDirty('landRoles')"
      :land="land"
      :initial-values="landMember.landRoles"
      @update:model-value="setFieldValue('landRoles', $event)"
    />
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
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-member';
import FormFieldTeraLandRole from '../../land-role/forms/fields/FormFieldTeraLandRole.vue';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';

import { landMemberPatchSucceededEvent } from '@lychen/vue-tera/events/LandMemberEvents';
import { extractValuesByKey } from '@lychen/typescript-utils/transformers/extractValuesByKey';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { landMember } = defineProps<{
  landMember: Omit<components['schemas']['LandMember.jsonld'], 'landRoles'> & {
    landRoles?: Pick<components['schemas']['LandRole.jsonld'], '@id' | 'name'>[];
  };
  land: components['schemas']['Land.jsonld'];
}>();

interface FormType {
  landRoles: Pick<components['schemas']['LandRole.jsonld'], '@id' | 'name'>[];
}

const { handleSubmit, meta, setFieldValue, isFieldDirty } = useForm<FormType>({
  initialValues: {},
});

const { emit } = useEventBus(landMemberPatchSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (values: FormType) => {
    if (!landMember.ulid) {
      throw new Error('missing.ulid');
    }
    const landRoleIds = extractValuesByKey(values.landRoles, '@id');
    const response = await api.PATCH('/api/land_members/{ulid}', {
      params: { path: { ulid: landMember.ulid } },
      body: {
        landRoles: landRoleIds,
      },
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
