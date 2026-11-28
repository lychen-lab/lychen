<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <Label :for="speciesId">{{ t('species_label') }}</Label>
      <select
        :id="speciesId"
        v-model="form.fungusSpeciesId.value"
        :class="SELECT_CLASS"
        :aria-invalid="speciesIssue !== undefined"
        :aria-describedby="speciesIssue ? speciesErrorId : undefined"
      >
        <option
          value=""
          disabled
        >
          {{ t('species_placeholder') }}
        </option>
        <option
          v-for="option in speciesOptions"
          :key="option.id"
          :value="option.id"
        >
          {{ option.label }}
        </option>
      </select>
      <p
        v-if="speciesIssue"
        :id="speciesErrorId"
        class="text-sm text-negative"
      >
        {{ t('species_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="nameId">{{ t('name_label') }}</Label>
      <Input
        :id="nameId"
        :model-value="form.name.value"
        type="text"
        autocomplete="off"
        :placeholder="t('name_placeholder')"
        :aria-invalid="nameIssue !== undefined"
        :aria-describedby="nameIssue ? nameErrorId : undefined"
        @update:model-value="form.name.value = String($event)"
      />
      <p
        v-if="nameIssue"
        :id="nameErrorId"
        class="text-sm text-negative"
      >
        {{ t('name_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="supplierId">{{ t('supplier_label') }}</Label>
      <Input
        :id="supplierId"
        :model-value="form.supplier.value"
        type="text"
        autocomplete="off"
        @update:model-value="form.supplier.value = String($event)"
      />
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="notesId">{{ t('notes_label') }}</Label>
      <textarea
        :id="notesId"
        v-model="form.notes.value"
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

import type { FungusSpecies } from '@lychen/typescript-front-model-business/mushroom/FungusSpecies';
import type { Strain, StrainInput } from '@lychen/typescript-front-model-business/mushroom/Strain';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { PRESETS } from '@lychen/vue-components-core/utils/Preset';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { useSpeciesLabels } from '../../composables/use-species-labels/useSpeciesLabels';
import { useStrainForm } from '../../composables/use-strain-form/useStrainForm';
import { TRANSLATION_KEY, messages } from './i18n';

/**
 * The form to create or edit a strain: the species (from the reference data, given as
 * `fungusSpecies` and named in the current language), the name or reference (`M3782`), an optional
 * supplier and notes. It checks what the API checks and emits the `StrainInput` to send: the screen
 * owns the request (`submitting`, `errorMessage`). To edit, give the strain's `initialValues` once it
 * is loaded and `mode="edit"`.
 */
const props = defineProps<{
  fungusSpecies: WithRequired<FungusSpecies, 'id'>[];
  /** `create` unless it says `edit`. */
  mode?: 'create' | 'edit';
  /** The strain being edited: the form starts on its values. */
  initialValues?: Pick<Strain, 'name' | 'supplier' | 'notes'> & { fungusSpeciesId?: string };
  /** A request is running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: StrainInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { fungusSpeciesLabel } = useSpeciesLabels();

const form = useStrainForm({ initialValues: props.initialValues });

// The look of the other fields (`Input`), for a list of choices and for several lines.
const SELECT_CLASS = [
  'flex min-h-10 w-full px-3 py-2 text-sm',
  PRESETS.FocusOutline,
  PRESETS.InputField,
];
const TEXTAREA_CLASS = [
  'flex min-h-24 w-full px-3 py-2 text-sm placeholder:text-on-surface/40',
  PRESETS.FocusOutline,
  PRESETS.InputField,
];

const id = useId();
const speciesId = `${id}-species`;
const speciesErrorId = `${id}-species-error`;
const nameId = `${id}-name`;
const nameErrorId = `${id}-name-error`;
const supplierId = `${id}-supplier`;
const notesId = `${id}-notes`;

const formElement = ref<HTMLFormElement>();

const speciesIssue = computed(() => form.issue('fungusSpecies'));
const nameIssue = computed(() => form.issue('name'));

// The species by their name in the current language: the list is long, and alphabetical is how
// people look for one.
const speciesOptions = computed(() =>
  props.fungusSpecies
    .map((species) => ({ id: species.id, label: fungusSpeciesLabel(species) }))
    .sort((first, second) => first.label.localeCompare(second.label)),
);

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
