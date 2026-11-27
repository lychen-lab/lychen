import type { InoculationMethod } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import type { JournalEntryKind } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import type { MushroomLogState } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';

// Typed on the model's constants: a state, kind or method without its translation is a type error.
const state: Record<MushroomLogState, string> = {
  prepared: 'Prepared',
  incubating: 'Incubating',
  fruiting: 'Fruiting',
  retired: 'Retired',
};

const journalEntryKind: Record<JournalEntryKind, string> = {
  note: 'Note',
  observation: 'Observation',
  soaking: 'Soaking',
  watering: 'Watering',
  moving: 'Moving',
  treatment: 'Treatment',
  other: 'Other',
  state_change: 'State change',
};

const inoculationMethod: Record<InoculationMethod, string> = {
  dowels: 'Dowels',
  sawdust: 'Sawdust',
  other: 'Other',
};

export default {
  state,
  journal_entry_kind: journalEntryKind,
  inoculation_method: inoculationMethod,
};
