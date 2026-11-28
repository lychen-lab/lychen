<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <Label :for="weightId">{{ t('weight_label') }}</Label>
      <Input
        :id="weightId"
        :model-value="weight"
        type="text"
        inputmode="numeric"
        enterkeyhint="done"
        autocomplete="off"
        :aria-invalid="weightIssue !== undefined"
        :aria-describedby="weightIssue ? weightErrorId : undefined"
        @update:model-value="weight = String($event)"
      />
      <p
        v-if="weightIssue"
        :id="weightErrorId"
        class="text-sm text-negative"
      >
        {{ weightIssue === INPUT_ISSUE_CODE.Required ? t('weight_required') : t('weight_invalid') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="dayId">{{ t('day_label') }}</Label>
      <Input
        :id="dayId"
        :model-value="harvestedAt"
        type="date"
        :max="maxDay"
        :aria-invalid="dayIssue !== undefined"
        :aria-describedby="dayIssue ? dayErrorId : undefined"
        @update:model-value="harvestedAt = String($event)"
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
      <Label :for="notesId">{{ t('notes_label') }}</Label>
      <Input
        :id="notesId"
        :model-value="notes"
        type="text"
        autocomplete="off"
        @update:model-value="notes = String($event)"
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

import type { HarvestInput } from '@lychen/typescript-front-model-business/mushroom/Harvest';
import { INPUT_ISSUE_CODE } from '@lychen/typescript-front-model-business/mushroom/MushroomInputValidation';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { useHarvestForm } from '../../composables/use-harvest-form/useHarvestForm';
import { TRANSLATION_KEY, messages } from './i18n';

/**
 * The form to weigh a harvest on a log: the weight in grams (numeric keypad), the day (today by
 * default, never after today) and optional notes. It checks what the API checks and emits the
 * `HarvestInput` to send: the screen owns the request (`submitting`, `errorMessage` say how it goes)
 * and calls `reset()` once the harvest is saved, so that the sheet opens empty the next time.
 */
const props = defineProps<{
  mushroomLogId: string;
  /** A request is running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: HarvestInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

const { weight, harvestedAt, notes, maxDay, issue, submit, reset } = useHarvestForm({
  mushroomLogId: () => props.mushroomLogId,
});

const id = useId();
const weightId = `${id}-weight`;
const weightErrorId = `${id}-weight-error`;
const dayId = `${id}-day`;
const dayErrorId = `${id}-day-error`;
const notesId = `${id}-notes`;

const formElement = ref<HTMLFormElement>();

const weightIssue = computed(() => issue('weightGrams'));
const dayIssue = computed(() => issue('harvestedAt'));

function onSubmit(): void {
  const input = submit();

  if (input) {
    emit('submit', input);
    return;
  }

  // The first field that is wrong gets the focus, so that the keyboard stays where it is needed.
  void nextTick(() =>
    formElement.value?.querySelector<HTMLElement>('[aria-invalid="true"]')?.focus(),
  );
}

defineExpose({ reset });
</script>
