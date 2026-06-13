<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandRole
      :is-field-dirty="isFieldDirty('landRoles')"
      :land="land"
      :initial-values="landMemberInvitation.landRoles"
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
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-member-invitation';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import FormFieldTeraLandRole from '../../land-role/forms/fields/FormFieldTeraLandRole.vue';

import { landMemberInvitationPatchSucceededEvent } from '@lychen/vue-tera/events/LandMemberInvitationEvents';
import { extractValuesByKey } from '@lychen/typescript-utils/transformers/extractValuesByKey';
import type { components } from '@lychen/typescript-tera-api-sdk/generated/tera-api';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { landMemberInvitation } = defineProps<{
  landMemberInvitation: Omit<components['schemas']['LandMemberInvitation.jsonld'], 'landRoles'> & {
    landRoles?: Pick<components['schemas']['LandRole.jsonld'], '@id' | 'name'>[];
  };
  land: components['schemas']['Land.jsonld'];
}>();

type FormType = { landRoles: Pick<components['schemas']['LandRole.jsonld'], '@id' | 'name'>[] };

const { isFieldDirty, handleSubmit, meta, setFieldValue } = useForm<FormType>({});

const { emit } = useEventBus(landMemberInvitationPatchSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (data: FormType) => {
    if (!landMemberInvitation.ulid) {
      throw new Error('missing.ulid');
    }
    let landRoleIds;
    if (data.landRoles) {
      landRoleIds = extractValuesByKey(data.landRoles, '@id');
    }
    const response = await api.PATCH('/api/land_member_invitations/{ulid}', {
      params: {
        path: { ulid: landMemberInvitation.ulid },
      },
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
