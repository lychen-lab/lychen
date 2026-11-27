import { createSSRApp, defineComponent } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { createI18n } from 'vue-i18n';
import { describe, expect, it } from 'vitest';

import { INOCULATION_METHOD } from '@lychen/typescript-front-model-business/mushroom/Inoculation';
import { JOURNAL_ENTRY_KIND } from '@lychen/typescript-front-model-business/mushroom/JournalEntry';
import { MUSHROOM_LOG_STATE } from '@lychen/typescript-front-model-business/mushroom/MushroomLog';

import { useMushroomLabels, type UseMushroomLabels } from './useMushroomLabels';

// `useI18n` needs a component's setup: render a bare one, in Node, with the app's i18n options.
async function labelsFor(locale: string): Promise<UseMushroomLabels> {
  let labels: UseMushroomLabels | undefined;
  const app = createSSRApp(
    defineComponent({
      setup() {
        labels = useMushroomLabels();
        return () => null;
      },
    }),
  );
  app.use(createI18n({ legacy: false, locale, fallbackLocale: 'en-US' }));
  await renderToString(app);

  if (!labels) {
    throw new Error('useMushroomLabels was not called');
  }

  return labels;
}

describe('useMushroomLabels', () => {
  it('names the states of a log as the spec does', async () => {
    const { mushroomLogStateLabel } = await labelsFor('fr-FR');

    expect(mushroomLogStateLabel(MUSHROOM_LOG_STATE.Prepared)).toBe('Préparée');
    expect(mushroomLogStateLabel(MUSHROOM_LOG_STATE.Incubating)).toBe('En incubation');
    expect(mushroomLogStateLabel(MUSHROOM_LOG_STATE.Fruiting)).toBe('En production');
    expect(mushroomLogStateLabel(MUSHROOM_LOG_STATE.Retired)).toBe('Retirée');
  });

  it('names the journal kinds and the inoculation methods in French', async () => {
    const { journalEntryKindLabel, inoculationMethodLabel } = await labelsFor('fr-FR');

    expect(journalEntryKindLabel(JOURNAL_ENTRY_KIND.Soaking)).toBe('Trempage');
    expect(journalEntryKindLabel(JOURNAL_ENTRY_KIND.StateChange)).toBe('Changement d’état');
    expect(inoculationMethodLabel(INOCULATION_METHOD.Dowels)).toBe('Chevilles');
  });

  it('speaks English too, for en-US and en-GB', async () => {
    for (const locale of ['en-US', 'en-GB']) {
      const labels = await labelsFor(locale);

      expect(labels.mushroomLogStateLabel(MUSHROOM_LOG_STATE.Fruiting)).toBe('Fruiting');
      expect(labels.journalEntryKindLabel(JOURNAL_ENTRY_KIND.Watering)).toBe('Watering');
      expect(labels.inoculationMethodLabel(INOCULATION_METHOD.Sawdust)).toBe('Sawdust');
    }
  });

  it('translates every state, kind and method of the model, in both languages', async () => {
    for (const locale of ['fr-FR', 'en-US']) {
      const labels = await labelsFor(locale);

      for (const state of Object.values(MUSHROOM_LOG_STATE)) {
        expect(labels.mushroomLogStateLabel(state)).not.toContain('mushroom');
      }
      for (const kind of Object.values(JOURNAL_ENTRY_KIND)) {
        expect(labels.journalEntryKindLabel(kind)).not.toContain('mushroom');
      }
      for (const method of Object.values(INOCULATION_METHOD)) {
        expect(labels.inoculationMethodLabel(method)).not.toContain('mushroom');
      }
    }
  });

  it('answers an empty string for a value the front does not know', async () => {
    const labels = await labelsFor('fr-FR');

    expect(labels.mushroomLogStateLabel()).toBe('');
    expect(labels.journalEntryKindLabel()).toBe('');
    expect(labels.inoculationMethodLabel()).toBe('');
  });
});
