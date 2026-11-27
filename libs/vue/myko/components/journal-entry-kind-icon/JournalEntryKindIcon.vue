<template>
  <component
    :is="icon"
    v-if="icon"
    aria-hidden="true"
  />
</template>

<script lang="ts" setup>
import { computed } from 'vue';

import type { JournalEntryKind } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';

import { journalEntryKindIcon } from './journalEntryKindIcon';

/**
 * The icon of a journal entry's kind. Decorative (the kind's label sits next to it), so hidden
 * from screen readers. Renders nothing for a kind this front does not know (`undefined`).
 */
const props = defineProps<{ kind?: JournalEntryKind }>();

const icon = computed(() => (props.kind ? journalEntryKindIcon(props.kind) : undefined));
</script>
