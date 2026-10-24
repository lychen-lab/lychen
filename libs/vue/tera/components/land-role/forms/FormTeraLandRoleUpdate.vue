<template>
  <form
    class="flex flex-col gap-6"
    @submit.prevent="onSubmit"
  >
    <FormFieldTeraLandRoleName :is-field-dirty="isFieldDirty('name')" />
    <FormFieldTeraPermissions
      :is-field-dirty="isFieldDirty('permissions')"
      :model-value="values.permissions"
      @update:model-value="handlePermissionsUpdate"
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
import { messages, TRANSLATION_KEY } from '@lychen/i18n-tera/land-role';

import { useForm } from 'vee-validate';

import { useMutation } from '@tanstack/vue-query';
import { useTeraApi } from '@lychen/vue-tera/composables/use-tera-api/useTeraApi';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';
import { useEventBus } from '@vueuse/core';
import FormFieldTeraLandRoleName from './fields/FormFieldTeraLandRoleName.vue';
import FormFieldTeraPermissions from '../../permission/form/field/FormFieldTeraPermissions.vue';

import { landRolePatchSucceededEvent } from '@lychen/vue-tera/events/LandRoleEvents';
import type {
  ApiLandRolesUlidPatchData,
  LandRoleJsonld,
} from '@lychen/typescript-tera-api-sdk/generated/tera-api';
import type { LandRolePermission } from '@lychen/typescript-tera-api-sdk/enums';

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { landRole } = defineProps<{ landRole: LandRoleJsonld }>();

type PatchInput = ApiLandRolesUlidPatchData['body'];
// The generated SDK types `permissions` as a scalar enum union (the spec declares the enum on the
// array itself, without `items`), but the API accepts/returns an array of those permission strings. Model it as an array
// here so the form and FormFieldTeraPermissions (which manages a string list) line up.
type FormType = Omit<PatchInput, 'permissions'> & { permissions?: string[] };

const { isFieldDirty, handleSubmit, meta, setFieldValue, values } = useForm<FormType>({
  initialValues: {
    name: landRole.name,
    // `landRole.permissions` is an array of permission strings at runtime despite its scalar enum type.
    permissions: landRole.permissions as unknown as string[] | undefined,
  },
});

const { emit } = useEventBus(landRolePatchSucceededEvent);

const { api } = useTeraApi();

const { mutate, isPending } = useMutation({
  mutationFn: async (data: FormType) => {
    if (!landRole.ulid) {
      throw new Error('missing.ulid');
    }
    const response = await api.apiLandRolesUlidPatch({
      path: { ulid: landRole.ulid },
      body: {
        ...data,
        // Send the permission list using the generated enum element type (see FormType note above).
        permissions: data.permissions as unknown as LandRolePermission | undefined,
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

function handlePermissionsUpdate(newPermissions: string[]) {
  setFieldValue('permissions', newPermissions);
}
</script>
