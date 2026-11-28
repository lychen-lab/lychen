<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <Label :for="prefixId">{{ t('prefix_label') }}</Label>
      <Input
        :id="prefixId"
        :model-value="form.prefix.value"
        type="text"
        autocomplete="off"
        autocapitalize="characters"
        :placeholder="t('prefix_placeholder')"
        @update:model-value="form.prefix.value = String($event)"
      />
    </div>

    <div class="grid grid-cols-3 gap-3">
      <div class="flex flex-col gap-1.5">
        <Label :for="startId">{{ t('start_label') }}</Label>
        <Input
          :id="startId"
          :model-value="form.startNumber.value"
          type="text"
          inputmode="numeric"
          autocomplete="off"
          :aria-invalid="startIssue !== undefined"
          :aria-describedby="startIssue ? startErrorId : undefined"
          @update:model-value="form.startNumber.value = String($event)"
        />
      </div>
      <div class="flex flex-col gap-1.5">
        <Label :for="countId">{{ t('count_label') }}</Label>
        <Input
          :id="countId"
          :model-value="form.count.value"
          type="text"
          inputmode="numeric"
          autocomplete="off"
          :aria-invalid="countIssue !== undefined"
          :aria-describedby="countIssue ? countErrorId : undefined"
          @update:model-value="form.count.value = String($event)"
        />
      </div>
      <div class="flex flex-col gap-1.5">
        <Label :for="digitsId">{{ t('digits_label') }}</Label>
        <Input
          :id="digitsId"
          :model-value="form.digits.value"
          type="text"
          inputmode="numeric"
          autocomplete="off"
          :aria-invalid="digitsIssue !== undefined"
          :aria-describedby="digitsIssue ? digitsErrorId : undefined"
          @update:model-value="form.digits.value = String($event)"
        />
      </div>
      <p
        v-if="startIssue"
        :id="startErrorId"
        class="col-span-3 text-sm text-negative"
      >
        {{ t('start_invalid') }}
      </p>
      <p
        v-if="countIssue"
        :id="countErrorId"
        class="col-span-3 text-sm text-negative"
      >
        {{
          countIssue === INPUT_ISSUE_CODE.Required
            ? t('count_required')
            : t('count_invalid', { max: MUSHROOM_LOG_BATCH_MAX_COUNT })
        }}
      </p>
      <p
        v-if="digitsIssue"
        :id="digitsErrorId"
        class="col-span-3 text-sm text-negative"
      >
        {{ t('digits_invalid', { max: MUSHROOM_LOG_BATCH_MAX_DIGITS }) }}
      </p>
    </div>

    <!-- What the creation will make, from the same function that makes it. -->
    <p
      class="rounded-md bg-surface-container px-3 py-2 text-sm"
      aria-live="polite"
    >
      <template v-if="firstLabel !== undefined">
        <span class="font-medium">{{ firstLabel }}</span>
        <template v-if="lastLabel !== firstLabel">
          <span aria-hidden="true"> → </span>
          <span class="sr-only">{{ t('preview_to') }}</span>
          <span class="font-medium">{{ lastLabel }}</span>
        </template>
        <span class="text-on-surface/70">
          · {{ t('preview_count', { count: form.labels.value.length }, form.labels.value.length) }}
        </span>
      </template>
      <span
        v-else
        class="text-on-surface/70"
      >
        {{ t('preview_empty') }}
      </span>
    </p>

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

    <div class="flex items-center justify-between gap-3">
      <Label :for="inoculateId">{{ t('inoculate_label') }}</Label>
      <Switch
        :id="inoculateId"
        :model-value="form.inoculate.value"
        @update:model-value="form.inoculate.value = $event === true"
      />
    </div>

    <template v-if="form.inoculate.value">
      <div class="flex flex-col gap-1.5">
        <Label :for="strainId">{{ t('strain_label') }}</Label>
        <select
          :id="strainId"
          v-model="form.inoculation.strainId.value"
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
          :model-value="form.inoculation.method.value"
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
          :model-value="form.inoculation.inoculatedAt.value"
          type="date"
          :max="form.inoculation.maxDay.value"
          :aria-invalid="dayIssue !== undefined"
          :aria-describedby="dayIssue ? dayErrorId : undefined"
          @update:model-value="form.inoculation.inoculatedAt.value = String($event)"
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
          :model-value="form.inoculation.quantity.value"
          type="text"
          inputmode="numeric"
          autocomplete="off"
          :aria-invalid="quantityIssue !== undefined"
          :aria-describedby="quantityIssue ? quantityErrorId : undefined"
          @update:model-value="form.inoculation.quantity.value = String($event)"
        />
        <p
          v-if="quantityIssue"
          :id="quantityErrorId"
          class="text-sm text-negative"
        >
          {{ t('quantity_invalid') }}
        </p>
      </div>
    </template>

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
      :label="
        form.labels.value.length > 0
          ? t('submit_count', { count: form.labels.value.length }, form.labels.value.length)
          : t('submit')
      "
      :loading="submitting"
    />
  </form>
</template>

<script lang="ts" setup>
import { computed, nextTick, ref, useId } from 'vue';

import type { InoculationMethod } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import type { MushroomLogBatchInput } from '@lychen/typescript-front-model-business/mushroom/MushroomLogBatchInput';
import {
  MUSHROOM_LOG_BATCH_MAX_COUNT,
  MUSHROOM_LOG_BATCH_MAX_DIGITS,
} from '@lychen/typescript-front-model-business/mushroom/MushroomLogBatchLabels';
import type { Strain } from '@lychen/typescript-front-model-business/mushroom/Strain';
import type { WoodSpecies } from '@lychen/typescript-front-model-business/mushroom/WoodSpecies';
import type { Zone } from '@lychen/typescript-front-model-business/mushroom/Zone';
import type { WithRequired } from '@lychen/typescript-front-model-business/WithRequired';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { Switch } from '@lychen/vue-components-core/switch';
import { ToggleGroup, ToggleGroupItem } from '@lychen/vue-components-core/toggle-group';
import { PRESETS } from '@lychen/vue-components-core/utils/Preset';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { INOCULATION_FORM_METHODS } from '../../composables/use-inoculation-form/useInoculationForm';
import {
  useMushroomLogBatchForm,
  type MushroomLogBatchFormInitialValues,
} from '../../composables/use-mushroom-log-batch-form/useMushroomLogBatchForm';
import { useMushroomLabels } from '../../composables/use-mushroom-labels/useMushroomLabels';
import { useSpeciesLabels } from '../../composables/use-species-labels/useSpeciesLabels';
import { TRANSLATION_KEY, messages } from './i18n';

type StrainOption = WithRequired<Strain, 'id' | 'name'>;

/**
 * The form to create logs in a series ("créer B-001 à B-040 dans la zone Sous-bois"): the prefix,
 * the first number, the count and the digits that make the labels, with the labels it will make
 * shown as it is typed; the zone, required; the wood species and an inoculation for the whole
 * series, optional. The zones, wood species and strains are given. It checks what it can check
 * before the request (a count from 1 to `MUSHROOM_LOG_BATCH_MAX_COUNT`) and emits the
 * `MushroomLogBatchInput`: the screen owns the requests (`submitting`, `errorMessage`, such as a
 * label already used) and calls `reset()` once the series is created. `initialValues` carry the
 * prefix and the first number the screen suggests, and the zone last used.
 */
const props = defineProps<{
  zones: WithRequired<Zone, 'id' | 'name'>[];
  woodSpecies: WithRequired<WoodSpecies, 'id'>[];
  strains: StrainOption[];
  initialValues?: MushroomLogBatchFormInitialValues;
  /** A strain already chosen when the inoculation is turned on, by its id. */
  initialStrainId?: string;
  /** The requests are running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: MushroomLogBatchInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { inoculationMethodLabel } = useMushroomLabels();
const { fungusSpeciesLabel, woodSpeciesLabel } = useSpeciesLabels();

const form = useMushroomLogBatchForm({
  initialValues: props.initialValues,
  initialStrainId: props.initialStrainId,
});

// The look of the other fields (`Input`), for a list of choices.
const SELECT_CLASS = [
  'flex min-h-10 w-full px-3 py-2 text-sm',
  PRESETS.FocusOutline,
  PRESETS.InputField,
];

const id = useId();
const prefixId = `${id}-prefix`;
const startId = `${id}-start`;
const startErrorId = `${id}-start-error`;
const countId = `${id}-count`;
const countErrorId = `${id}-count-error`;
const digitsId = `${id}-digits`;
const digitsErrorId = `${id}-digits-error`;
const zoneId = `${id}-zone`;
const zoneErrorId = `${id}-zone-error`;
const woodId = `${id}-wood`;
const inoculateId = `${id}-inoculate`;
const strainId = `${id}-strain`;
const strainErrorId = `${id}-strain-error`;
const methodLabelId = `${id}-method-label`;
const dayId = `${id}-day`;
const dayErrorId = `${id}-day-error`;
const quantityId = `${id}-quantity`;
const quantityErrorId = `${id}-quantity-error`;

const formElement = ref<HTMLFormElement>();

const startIssue = computed(() => form.issue('startNumber'));
const countIssue = computed(() => form.issue('count'));
const digitsIssue = computed(() => form.issue('digits'));
const zoneIssue = computed(() => form.issue('zone'));
const strainIssue = computed(() => form.inoculationIssue('strain'));
const dayIssue = computed(() => form.inoculationIssue('inoculatedAt'));
const quantityIssue = computed(() => form.inoculationIssue('quantity'));

const firstLabel = computed(() => form.labels.value[0]);
const lastLabel = computed(() => form.labels.value.at(-1));

// The wood species by their name in the current language, alphabetically.
const woodSpeciesOptions = computed(() =>
  props.woodSpecies
    .map((species) => ({ id: species.id, label: woodSpeciesLabel(species) }))
    .sort((first, second) => first.label.localeCompare(second.label)),
);

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
    form.inoculation.method.value = method;
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
