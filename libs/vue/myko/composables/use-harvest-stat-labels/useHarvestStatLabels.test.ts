import { createSSRApp, defineComponent } from 'vue';
import { renderToString } from 'vue/server-renderer';
import { createI18n } from 'vue-i18n';
import { describe, expect, it } from 'vitest';

import {
  HARVEST_PERIOD,
  type HarvestPeriod,
} from '@lychen/typescript-front-model-business/mushroom/HarvestPeriod';
import {
  HARVEST_STAT_GROUP_BY,
  type HarvestStatGroupBy,
} from '@lychen/typescript-front-model-business/mushroom/HarvestStat';

import { useHarvestStatLabels, type UseHarvestStatLabels } from './useHarvestStatLabels';

// `useI18n` needs a component's setup: render a bare one, in Node, with the app's i18n options.
async function labelsFor(locale: string): Promise<UseHarvestStatLabels> {
  let labels: UseHarvestStatLabels | undefined;
  const app = createSSRApp(
    defineComponent({
      setup() {
        labels = useHarvestStatLabels();
        return () => null;
      },
    }),
  );
  app.use(createI18n({ legacy: false, locale, fallbackLocale: 'en-US' }));
  await renderToString(app);

  if (!labels) {
    throw new Error('useHarvestStatLabels was not called');
  }

  return labels;
}

describe('useHarvestStatLabels', () => {
  it('names what the statistics are grouped by, as the spec does', async () => {
    const { harvestStatGroupByLabel } = await labelsFor('fr-FR');

    expect(harvestStatGroupByLabel(HARVEST_STAT_GROUP_BY.MushroomLog)).toBe('Bûche');
    expect(harvestStatGroupByLabel(HARVEST_STAT_GROUP_BY.Strain)).toBe('Souche');
    expect(harvestStatGroupByLabel(HARVEST_STAT_GROUP_BY.FungusSpecies)).toBe('Espèce');
    expect(harvestStatGroupByLabel(HARVEST_STAT_GROUP_BY.WoodSpecies)).toBe('Essence');
    expect(harvestStatGroupByLabel(HARVEST_STAT_GROUP_BY.Zone)).toBe('Zone');
    expect(harvestStatGroupByLabel(HARVEST_STAT_GROUP_BY.Month)).toBe('Mois');
  });

  it('names the periods in French and in English', async () => {
    const fr = await labelsFor('fr-FR');
    const en = await labelsFor('en-US');

    expect(fr.harvestPeriodLabel(HARVEST_PERIOD.Season)).toBe('Saison en cours');
    expect(fr.harvestPeriodLabel(HARVEST_PERIOD.Custom)).toBe('Personnalisée');
    expect(en.harvestPeriodLabel(HARVEST_PERIOD.Season)).toBe('Current season');
    expect(en.harvestPeriodLabel(HARVEST_PERIOD.All)).toBe('All time');
  });

  it('gives every grouping and period a name, in every language', async () => {
    const groupings: HarvestStatGroupBy[] = Object.values(HARVEST_STAT_GROUP_BY);
    const periods: HarvestPeriod[] = Object.values(HARVEST_PERIOD);

    for (const locale of ['fr-FR', 'en-US', 'en-GB']) {
      const { harvestStatGroupByLabel, harvestPeriodLabel } = await labelsFor(locale);
      for (const groupBy of groupings) {
        expect(harvestStatGroupByLabel(groupBy), `${locale} ${groupBy}`).not.toBe('');
        expect(harvestStatGroupByLabel(groupBy), `${locale} ${groupBy}`).not.toContain('myko');
      }
      for (const period of periods) {
        expect(harvestPeriodLabel(period), `${locale} ${period}`).not.toBe('');
        expect(harvestPeriodLabel(period), `${locale} ${period}`).not.toContain('myko');
      }
    }
  });

  it('gives nothing for a value the front does not know', async () => {
    const { harvestStatGroupByLabel, harvestPeriodLabel } = await labelsFor('fr-FR');

    expect(harvestStatGroupByLabel(undefined)).toBe('');
    expect(harvestPeriodLabel(undefined)).toBe('');
  });
});
