<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <Label :for="labelId">{{ t('label_label') }}</Label>
      <Input
        :id="labelId"
        :model-value="form.label.value"
        type="text"
        autocomplete="off"
        autocapitalize="characters"
        :placeholder="t('label_placeholder')"
        :aria-invalid="labelIssue !== undefined"
        :aria-describedby="labelIssue ? labelErrorId : undefined"
        @update:model-value="form.label.value = String($event)"
      />
      <p
        v-if="labelIssue"
        :id="labelErrorId"
        class="text-sm text-negative"
      >
        {{ t('label_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="zoneId">{{ t('zone_label') }}</Label>
      <select
        :id="zoneId"
        v-model="form.zoneId.value"
        :class="SELECT_CLASS"
        :aria-invalid="zoneIssue !== undefined"
        :aria-describedby="zoneIssue ? zoneErrorId : undefined"
      >
        <option
          value=""
          disabled
        >
          {{ t('zone_placeholder') }}
        </option>
        <option
          v-for="zone in zones"
          :key="zone.id"
          :value="zone.id"
        >
          {{ zone.name }}
        </option>
      </select>
      <p
        v-if="zones.length === 0"
        class="text-sm text-on-surface/70"
      >
        {{ t('zone_none') }}
      </p>
      <p
        v-if="zoneIssue"
        :id="zoneErrorId"
        class="text-sm text-negative"
      >
        {{ t('zone_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="woodId">{{ t('wood_label') }}</Label>
      <select
        :id="woodId"
        v-model="form.woodSpeciesId.value"
        :class="SELECT_CLASS"
      >
        <option value="">{{ t('wood_none') }}</option>
        <option
          v-for="option in woodSpeciesOptions"
          :key="option.id"
          :value="option.id"
        >
          {{ option.label }}
        </option>
      </select>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="cutId">{{ t('cut_label') }}</Label>
      <Input
        :id="cutId"
        :model-value="form.cutAt.value"
        type="date"
        @update:model-value="form.cutAt.value = String($event)"
      />
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div class="flex flex-col gap-1.5">
        <Label :for="diameterId">{{ t('diameter_label') }}</Label>
        <Input
          :id="diameterId"
          :model-value="form.diameterCm.value"
          type="text"
          inputmode="numeric"
          autocomplete="off"
          :aria-invalid="diameterIssue !== undefined"
          :aria-describedby="diameterIssue || lengthIssue ? sizeErrorId : undefined"
          @update:model-value="form.diameterCm.value = String($event)"
        />
      </div>
      <div class="flex flex-col gap-1.5">
        <Label :for="lengthId">{{ t('length_label') }}</Label>
        <Input
          :id="lengthId"
          :model-value="form.lengthCm.value"
          type="text"
          inputmode="numeric"
          autocomplete="off"
          :aria-invalid="lengthIssue !== undefined"
          :aria-describedby="diameterIssue || lengthIssue ? sizeErrorId : undefined"
          @update:model-value="form.lengthCm.value = String($event)"
        />
      </div>
      <p
        v-if="diameterIssue || lengthIssue"
        :id="sizeErrorId"
        class="col-span-2 text-sm text-negative"
      >
        {{ t('size_invalid') }}
      </p>
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

import type { MushroomLogInput } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';
import type { WoodSpecies } from '@lychen/typescript-front-model-business/mushroom/WoodSpecies';
import type { Zone } from '@lychen/typescript-front-model-business/mushroom/Zone';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { PRESETS } from '@lychen/vue-components-core/utils/Preset';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import {
  useMushroomLogForm,
  type MushroomLogFormInitialValues,
} from '../../composables/use-mushroom-log-form/useMushroomLogForm';
import { useSpeciesLabels } from '../../composables/use-species-labels/useSpeciesLabels';
import { TRANSLATION_KEY, messages } from './i18n';

/**
 * The form to create or edit a log: the label (`B-012`) and the zone, required, then the wood
 * species, the cutting day, the diameter, the length and notes. The zones and the wood species are
 * given (`zones`, `woodSpecies`); the species are named in the current language and sorted. It
 * checks what the API checks and emits the `MushroomLogInput` to send: the screen owns the request
 * (`submitting`, `errorMessage`, such as a label already used). To edit, give the log's
 * `initialValues` once it is loaded and `mode="edit"`; for a new log they carry the suggested label
 * and the zone last used.
 */
const props = defineProps<{
  zones: WithRequired<Zone, 'id' | 'name'>[];
  woodSpecies: WithRequired<WoodSpecies, 'id'>[];
  /** `create` unless it says `edit`. */
  mode?: 'create' | 'edit';
  /** The log being edited, or what a new one starts with. */
  initialValues?: MushroomLogFormInitialValues;
  /** A request is running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: MushroomLogInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { woodSpeciesLabel } = useSpeciesLabels();

const form = useMushroomLogForm({ initialValues: props.initialValues });

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
const labelId = `${id}-label`;
const labelErrorId = `${id}-label-error`;
const zoneId = `${id}-zone`;
const zoneErrorId = `${id}-zone-error`;
const woodId = `${id}-wood`;
const cutId = `${id}-cut`;
const diameterId = `${id}-diameter`;
const lengthId = `${id}-length`;
const sizeErrorId = `${id}-size-error`;
const notesId = `${id}-notes`;

const formElement = ref<HTMLFormElement>();

const labelIssue = computed(() => form.issue('label'));
const zoneIssue = computed(() => form.issue('zone'));
const diameterIssue = computed(() => form.issue('diameterCm'));
const lengthIssue = computed(() => form.issue('lengthCm'));

// The wood species by their name in the current language, alphabetically.
const woodSpeciesOptions = computed(() =>
  props.woodSpecies
    .map((species) => ({ id: species.id, label: woodSpeciesLabel(species) }))
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
