<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <Label :for="strainId">{{ t('strain_label') }}</Label>
      <select
        :id="strainId"
        v-model="form.strainId.value"
        :class="SELECT_CLASS"
        :aria-invalid="strainIssue !== undefined"
        :aria-describedby="strainIssue ? strainErrorId : undefined"
      >
        <option
          value=""
          disabled
        >
          {{ t('strain_placeholder') }}
        </option>
        <option
          v-for="strain in strains"
          :key="strain.id"
          :value="strain.id"
        >
          {{ strainOptionLabel(strain) }}
        </option>
      </select>
      <p
        v-if="strains.length === 0"
        class="text-sm text-on-surface/70"
      >
        {{ t('strain_none') }}
      </p>
      <p
        v-if="strainIssue"
        :id="strainErrorId"
        class="text-sm text-negative"
      >
        {{ t('strain_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <span
        :id="methodLabelId"
        class="text-sm leading-none font-medium"
      >
        {{ t('method_label') }}
      </span>
      <ToggleGroup
        type="single"
        variant="outline"
        class="flex-wrap justify-start gap-2"
        :model-value="form.method.value"
        :aria-labelledby="methodLabelId"
        @update:model-value="onMethodChange"
      >
        <ToggleGroupItem
          v-for="candidate in INOCULATION_FORM_METHODS"
          :key="candidate"
          :value="candidate"
        >
          {{ inoculationMethodLabel(candidate) }}
        </ToggleGroupItem>
      </ToggleGroup>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="dayId">{{ t('day_label') }}</Label>
      <Input
        :id="dayId"
        :model-value="form.inoculatedAt.value"
        type="date"
        :max="form.maxDay.value"
        :aria-invalid="dayIssue !== undefined"
        :aria-describedby="dayIssue ? dayErrorId : undefined"
        @update:model-value="form.inoculatedAt.value = String($event)"
      />
      <p
        v-if="dayIssue"
        :id="dayErrorId"
        class="text-sm text-negative"
      >
        {{ dayIssue === INPUT_ISSUE_CODE.InFuture ? t('day_in_future') : t('day_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="quantityId">{{ t('quantity_label') }}</Label>
      <Input
        :id="quantityId"
        :model-value="form.quantity.value"
        type="text"
        inputmode="numeric"
        autocomplete="off"
        :aria-invalid="quantityIssue !== undefined"
        :aria-describedby="quantityIssue ? quantityErrorId : undefined"
        @update:model-value="form.quantity.value = String($event)"
      />
      <p
        v-if="quantityIssue"
        :id="quantityErrorId"
        class="text-sm text-negative"
      >
        {{ t('quantity_invalid') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="notesId">{{ t('notes_label') }}</Label>
      <Input
        :id="notesId"
        :model-value="form.notes.value"
        type="text"
        autocomplete="off"
        @update:model-value="form.notes.value = String($event)"
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
      :label="t('submit')"
      :loading="submitting"
    />
  </form>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref, useId } from 'vue';

import {
  type InoculationInput,
  type InoculationMethod,
} from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type { Strain } from '@lychen/typescript-front-model-business/mushroom/Strain';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { ToggleGroup, ToggleGroupItem } from '@lychen/vue-components-core/toggle-group';
import { PRESETS } from '@lychen/vue-components-core/utils/Preset';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import {
  INOCULATION_FORM_METHODS,
  useInoculationForm,
} from '../../composables/use-inoculation-form/useInoculationForm';
import { useMushroomLabels } from '../../composables/use-mushroom-labels/useMushroomLabels';
import { useSpeciesLabels } from '../../composables/use-species-labels/useSpeciesLabels';
import { TRANSLATION_KEY, messages } from './i18n';

type StrainOption = WithRequired<Strain, 'id' | 'name'>;

/**
 * The form to inoculate a `prepared` log: the strain (from the person's own catalogue, given as
 * `strains`), the method as chips (dowels by default), the day (today by default, an earlier day is
 * fine), an optional quantity and notes. It checks what the API checks and emits the
 * `InoculationInput` to send: the screen owns the request (`submitting`, `errorMessage`) and calls
 * `reset()` once the inoculation is saved. Shown only for a log the API will accept it on.
 */
const props = defineProps<{
  mushroomLogId: string;
  strains: StrainOption[];
  /** A strain already chosen when the form opens, by its id. */
  initialStrainId?: string;
  /** A request is running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: InoculationInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { inoculationMethodLabel } = useMushroomLabels();
const { fungusSpeciesLabel } = useSpeciesLabels();

const form = useInoculationForm({
  mushroomLogId: () => props.mushroomLogId,
  initialStrainId: props.initialStrainId,
});

// The look of the other fields (`Input`), for a list of choices.
const SELECT_CLASS = [
  'flex min-h-10 w-full px-3 py-2 text-sm',
  PRESETS.FocusOutline,
  PRESETS.InputField,
];

const id = useId();
const strainId = `${id}-strain`;
const strainErrorId = `${id}-strain-error`;
const methodLabelId = `${id}-method-label`;
const dayId = `${id}-day`;
const dayErrorId = `${id}-day-error`;
const quantityId = `${id}-quantity`;
const quantityErrorId = `${id}-quantity-error`;
const notesId = `${id}-notes`;

const formElement = ref<HTMLFormElement>();

const strainIssue = computed(() => form.issue('strain'));
const dayIssue = computed(() => form.issue('inoculatedAt'));
const quantityIssue = computed(() => form.issue('quantity'));

/** `M3782 · Shiitake`: the name, then the species when the catalogue gives it. */
function strainOptionLabel(strain: StrainOption): string {
  const species = fungusSpeciesLabel(strain.fungusSpecies);

  return species === '' ? strain.name : `${strain.name} · ${species}`;
}

// The method is always one: a chip tapped again (`undefined`) changes nothing.
function onMethodChange(value: unknown): void {
  const method: InoculationMethod | undefined = INOCULATION_FORM_METHODS.find(
    (candidate) => candidate === value,
  );

  if (method) {
    form.method.value = method;
  }
}

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
