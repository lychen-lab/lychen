<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <Label :for="nameId">{{ t('name_label') }}</Label>
      <Input
        :id="nameId"
        :model-value="form.name.value"
        type="text"
        autocomplete="off"
        :aria-invalid="nameIssue !== undefined"
        :aria-describedby="nameIssue ? nameErrorId : undefined"
        @update:model-value="form.name.value = String($event)"
      />
      <p
        v-if="nameIssue"
        :id="nameErrorId"
        class="text-sm text-negative"
      >
        {{
          nameIssue === INPUT_ISSUE_CODE.TooLong
            ? t('name_too_long', { max: ZONE_NAME_MAX_LENGTH })
            : t('name_required')
        }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="descriptionId">{{ t('description_label') }}</Label>
      <textarea
        :id="descriptionId"
        v-model="form.description.value"
        rows="3"
        :class="TEXTAREA_CLASS"
      />
    </div>

    <p
      v-if="errorMessage"
      role="alert"
      class="text-sm text-negative"
    >
      {{ errorMessage }}
    </p>

    <Button
      type="submit"
      class="w-full"
      :label="mode === 'edit' ? t('submit_edit') : t('submit_create')"
      :loading="submitting"
    />
  </form>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref, useId } from 'vue';

import {
  INPUT_ISSUE_CODE,
  ZONE_NAME_MAX_LENGTH,
} from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type { Zone, ZoneInput } from '@lychen/typescript-front-model-business/mushroom/Zone';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { PRESETS } from '@lychen/vue-components-core/utils/Preset';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { useZoneForm } from '../../composables/use-zone-form/useZoneForm';
import { TRANSLATION_KEY, messages } from './i18n';

/**
 * The form to create or edit a zone: a name (required) and an optional description. It checks what
 * the API checks and emits the `ZoneInput` to send: the screen owns the request (`submitting`,
 * `errorMessage` say how it goes, such as a name the API refuses). To edit, give the zone's
 * `initialValues` once it is loaded and `mode="edit"`.
 */
const props = defineProps<{
  /** `create` unless it says `edit`. */
  mode?: 'create' | 'edit';
  /** The zone being edited: the form starts on its values. */
  initialValues?: Pick<Zone, 'name' | 'description'>;
  /** A request is running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: ZoneInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const form = useZoneForm({ initialValues: props.initialValues });

// The look of the other fields (`Input`), for a field that holds several lines.
const TEXTAREA_CLASS = [
  'flex min-h-24 w-full px-3 py-2 text-sm placeholder:text-on-surface/40',
  PRESETS.FocusOutline,
  PRESETS.InputField,
];

const id = useId();
const nameId = `${id}-name`;
const nameErrorId = `${id}-name-error`;
const descriptionId = `${id}-description`;

const formElement = ref<HTMLFormElement>();

const nameIssue = computed(() => form.issue('name'));

function onSubmit(): void {
  const input = form.submit();

  if (input) {
    emit('submit', input);
    return;
  }

  // The first field that is wrong gets the focus, so that the keyboard stays where it is needed.
  void nextTick(() =>
    formElement.value?.querySelector<HTMLElement>('[aria-invalid="true"]')?.focus(),
  );
}

defineExpose({ reset: form.reset });
</script>
