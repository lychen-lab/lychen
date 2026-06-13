<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldEmail
      :is-field-dirty="isFieldDirty('email')"
      :rules="emailFieldSchema"
    />
    <FormFieldTeraLandRole
      :is-field-dirty="isFieldDirty('landRoles')"
      :land="land"
      @update:model-value="setFieldValue('landRoles', $event)"
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
import FormFieldEmail from '@lychen/vue-components-app/fields/email/FormFieldEmail.vue';
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-member-invitation';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import { landMemberInvitationPostSucceededEvent } from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import FormFieldTeraLandRole from '../../land-role/forms/fields/FormFieldTeraLandRole.vue';
import { extractValuesByKey } from '@lychen/typescript-utils/transformers/extractValuesByKey';
import { z } from 'zod';
import { toTypedSchema } from '@vee-validate/zod';
import type { components, paths } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { land } = defineProps<{ land: components['schemas']['Land.jsonld'] }>();

type FormType = Omit<
  paths['/api/land_member_invitations']['post']['requestBody']['content']['application/ld+json'],
  'landRoles'
> & { landRoles: Pick<components['schemas']['LandRole.jsonld'], '@id' | 'name'>[] };

const emailFieldSchema = toTypedSchema(
  z
    .string()
    .email()
    .refine(
      async (email) => {
        if (!land.ulid) {
          throw new Error('missing.ulid');
        }
        if (!email) {
          return true;
        }
        const response = await api.GET('/api/land_member_invitations/check_email_unicity', {
          params: { query: { email, land: land.ulid } },
        });
        return response.data?.isUnique;
      },
      {
        message: 'This email is already invited',
      },
    ),
);

const { isFieldDirty, handleSubmit, meta, setFieldValue } = useForm<FormType>({
  initialValues: {
    landRoles: undefined,
  },
});

const { emit } = useEventBus(landMemberInvitationPostSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (values: FormType) => {
    if (!land['@id']) {
      throw new Error('missing.land_id');
    }
    const landRoleIds = extractValuesByKey(values.landRoles, '@id');
    const response = await api.POST('/api/land_member_invitations', {
      body: {
        email: values.email,
        landRoles: landRoleIds,
        land: land['@id'],
      },
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
