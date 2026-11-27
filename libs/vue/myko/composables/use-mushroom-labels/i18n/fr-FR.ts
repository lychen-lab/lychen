import type { InoculationMethod } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import type { JournalEntryKind } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import type { MushroomLogState } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';

// Typed on the model's constants: a state, kind or method without its translation is a type error.
const state: Record<MushroomLogState, string> = {
  prepared: 'Préparée',
  incubating: 'En incubation',
  fruiting: 'En production',
  retired: 'Retirée',
};

const journalEntryKind: Record<JournalEntryKind, string> = {
  note: 'Note',
  observation: 'Observation',
  soaking: 'Trempage',
  watering: 'Arrosage',
  moving: 'Déplacement',
  treatment: 'Traitement',
  other: 'Autre',
  state_change: 'Changement d’état',
};

const inoculationMethod: Record<InoculationMethod, string> = {
  dowels: 'Chevilles',
  sawdust: 'Sciure',
  other: 'Autre',
};

export default {
  state,
  journal_entry_kind: journalEntryKind,
  inoculation_method: inoculationMethod,
};
