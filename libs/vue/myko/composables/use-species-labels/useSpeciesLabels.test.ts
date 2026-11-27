import { createSSRApp, defineComponent } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { createI18n } from 'vue-i18n';
import { describe, expect, it } from 'vitest';

import { FUNGUS_SPECIES_LIST } from '@lychen/typescript-myko/constants/FungusSpecies';
import { WOOD_SPECIES_LIST } from '@lychen/typescript-myko/constants/WoodSpecies';

import { useSpeciesLabels, type UseSpeciesLabels } from './useSpeciesLabels';

// `useI18n` needs a component's setup: render a bare one, in Node, with the app's i18n options.
async function labelsFor(locale: string): Promise<UseSpeciesLabels> {
  let labels: UseSpeciesLabels | undefined;
  const app = createSSRApp(
    defineComponent({
      setup() {
        labels = useSpeciesLabels();
        return () => null;
      },
    }),
  );
  app.use(createI18n({ legacy: false, locale, fallbackLocale: 'en-US' }));
  await renderToString(app);

  if (!labels) {
    throw new Error('useSpeciesLabels was not called');
  }

  return labels;
}

describe('useSpeciesLabels', () => {
  it('translates a fungus species code in French', async () => {
    const { fungusSpeciesLabel } = await labelsFor('fr-FR');

    expect(fungusSpeciesLabel({ code: 'oyster' })).toBe('Pleurote en huître');
    expect(
      fungusSpeciesLabel({ code: 'wine-cap', scientificName: 'Stropharia rugosoannulata' }),
    ).toBe('Strophaire à anneau rugueux');
  });

  it('translates a wood species code in French and in English', async () => {
    const fr = await labelsFor('fr-FR');
    const en = await labelsFor('en-US');

    expect(fr.woodSpeciesLabel({ code: 'wild-cherry' })).toBe('Merisier');
    expect(en.woodSpeciesLabel({ code: 'wild-cherry' })).toBe('Wild cherry');
  });

  it('uses the English names for the other English locales', async () => {
    const { fungusSpeciesLabel } = await labelsFor('en-GB');

    expect(fungusSpeciesLabel({ code: 'lions-mane' })).toBe('Lion’s mane');
  });

  it('falls back to English for a locale with no messages', async () => {
    const { woodSpeciesLabel } = await labelsFor('de-DE');

    expect(woodSpeciesLabel({ code: 'oak' })).toBe('Oak');
  });

  it('translates every seeded species, in both languages', async () => {
    const fr = await labelsFor('fr-FR');
    const en = await labelsFor('en-US');

    for (const species of FUNGUS_SPECIES_LIST) {
      expect(fr.fungusSpeciesLabel(species)).not.toBe(species.scientificName);
      expect(en.fungusSpeciesLabel(species)).not.toBe(species.scientificName);
    }
    for (const species of WOOD_SPECIES_LIST) {
      expect(fr.woodSpeciesLabel(species)).not.toBe(species.scientificName);
      expect(en.woodSpeciesLabel(species)).not.toBe(species.scientificName);
    }
  });

  it('falls back to the scientific name for a code with no translation', async () => {
    const { fungusSpeciesLabel, woodSpeciesLabel } = await labelsFor('fr-FR');

    expect(fungusSpeciesLabel({ code: 'morel', scientificName: 'Morchella esculenta' })).toBe(
      'Morchella esculenta',
    );
    expect(woodSpeciesLabel({ code: 'walnut', scientificName: 'Juglans' })).toBe('Juglans');
  });

  it('falls back to the code when there is no scientific name either', async () => {
    const { fungusSpeciesLabel } = await labelsFor('fr-FR');

    expect(fungusSpeciesLabel({ code: 'morel' })).toBe('morel');
  });

  it('does not take a species of one group for the other', async () => {
    const { fungusSpeciesLabel, woodSpeciesLabel } = await labelsFor('fr-FR');

    expect(fungusSpeciesLabel({ code: 'oak', scientificName: 'Quercus' })).toBe('Quercus');
    expect(woodSpeciesLabel({ code: 'shiitake' })).toBe('shiitake');
  });

  it('answers an empty string for a missing species', async () => {
    const { fungusSpeciesLabel, woodSpeciesLabel } = await labelsFor('fr-FR');

    expect(fungusSpeciesLabel()).toBe('');
    expect(fungusSpeciesLabel(null)).toBe('');
    expect(woodSpeciesLabel({})).toBe('');
  });
});
