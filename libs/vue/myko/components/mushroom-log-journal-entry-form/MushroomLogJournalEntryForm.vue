<template>
  <form
    ref="formElement"
    class="flex flex-col gap-4"
    novalidate
    @submit.prevent="onSubmit"
  >
    <div class="flex flex-col gap-1.5">
      <span
        :id="kindLabelId"
        class="text-sm leading-none font-medium"
      >
        {{ t('kind_label') }}
      </span>
      <ToggleGroup
        type="single"
        variant="outline"
        class="flex-wrap justify-start gap-2"
        :model-value="kind"
        :aria-labelledby="kindLabelId"
        :aria-describedby="kindIssue ? kindErrorId : undefined"
        @update:model-value="onKindChange"
      >
        <ToggleGroupItem
          v-for="candidate in JOURNAL_ENTRY_FORM_KINDS"
          :key="candidate"
          :value="candidate"
        >
          <JournalEntryKindIcon :kind="candidate" />
          {{ journalEntryKindLabel(candidate) }}
        </ToggleGroupItem>
      </ToggleGroup>
      <p
        v-if="kindIssue"
        :id="kindErrorId"
        class="text-sm text-negative"
      >
        {{ t('kind_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="textId">{{ isTextRequired ? t('text_label') : t('text_label_optional') }}</Label>
      <textarea
        :id="textId"
        v-model="text"
        rows="3"
        :class="TEXTAREA_CLASS"
        :aria-invalid="textIssue !== undefined"
        :aria-describedby="textIssue ? textErrorId : undefined"
      />
      <p
        v-if="textIssue"
        :id="textErrorId"
        class="text-sm text-negative"
      >
        {{ t('text_required') }}
      </p>
    </div>

    <div class="flex flex-col gap-1.5">
      <Label :for="occurredAtId">{{ t('occurred_at_label') }}</Label>
      <Input
        :id="occurredAtId"
        :model-value="occurredAt"
        type="datetime-local"
        @update:model-value="occurredAt = String($event)"
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

import type { JournalEntryInput } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import { Button } from '@lychen/vue-components-core/button';
import { Input } from '@lychen/vue-components-core/input';
import { Label } from '@lychen/vue-components-core/label';
import { ToggleGroup, ToggleGroupItem } from '@lychen/vue-components-core/toggle-group';
import { PRESETS } from '@lychen/vue-components-core/utils/Preset';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import {
  JOURNAL_ENTRY_FORM_KINDS,
  useJournalEntryForm,
  type JournalEntryFormKind,
} from '../../composables/use-journal-entry-form/useJournalEntryForm';
import { useMushroomLabels } from '../../composables/use-mushroom-labels/useMushroomLabels';
import JournalEntryKindIcon from '../journal-entry-kind-icon/JournalEntryKindIcon.vue';
import { TRANSLATION_KEY, messages } from './i18n';

/**
 * The form to write a journal entry on a log: the kind as chips (a soaking is two taps: the chip,
 * then the button), a text (required for a note and an observation), the moment (now by default).
 * It checks what the API checks and emits the `JournalEntryInput` to send: the screen owns the
 * request (`submitting`, `errorMessage` say how it goes) and calls `reset()` once the entry is saved.
 */
const props = defineProps<{
  mushroomLogId: string;
  /** A kind already chosen when the form opens. */
  initialKind?: JournalEntryFormKind;
  /** A request is running: the button waits. */
  submitting?: boolean;
  /** What the API answered when it refused, already worded for the person. */
  errorMessage?: string;
}>();

const emit = defineEmits<{ submit: [input: JournalEntryInput] }>();

const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });
const { journalEntryKindLabel } = useMushroomLabels();

const { kind, text, occurredAt, isTextRequired, issue, submit, reset } = useJournalEntryForm({
  mushroomLogId: () => props.mushroomLogId,
  initialKind: props.initialKind,
});

// The look of the other fields (`Input`), for a field that holds several lines.
const TEXTAREA_CLASS = [
  'flex min-h-24 w-full px-3 py-2 text-sm placeholder:text-on-surface/40',
  PRESETS.FocusOutline,
  PRESETS.InputField,
];

const id = useId();
const kindLabelId = `${id}-kind-label`;
const kindErrorId = `${id}-kind-error`;
const textId = `${id}-text`;
const textErrorId = `${id}-text-error`;
const occurredAtId = `${id}-occurred-at`;

const formElement = ref<HTMLFormElement>();

const kindIssue = computed(() => issue('kind'));
const textIssue = computed(() => issue('text'));

// A chip tapped again is unselected (`undefined`): only the kinds of the form count.
function onKindChange(value: unknown): void {
  kind.value = JOURNAL_ENTRY_FORM_KINDS.find((candidate) => candidate === value);
}

function onSubmit(): void {
  const input = submit();

  if (input) {
    emit('submit', input);
    return;
  }

  // The first thing that is wrong gets the focus: a chip when no kind is chosen, else the text.
  void nextTick(() =>
    formElement.value
      ?.querySelector<HTMLElement>(kindIssue.value ? 'button' : '[aria-invalid="true"]')
      ?.focus(),
  );
}

defineExpose({ reset });
</script>
