import type { HarvestPeriod } from '@lychen/typescript-front-model-business/mushroom/HarvestPeriod';
import type { HarvestStatGroupBy } from '@lychen/typescript-front-model-business/mushroom/HarvestStat';
import { useI18nExtended } from '@lychen/vue-i18n/composables/useI18nExtended';

import { TRANSLATION_KEY, messages } from './i18n';

export interface UseHarvestStatLabels {
  /** "Souche"… An empty string for a grouping this front does not know (undefined). */
  harvestStatGroupByLabel: (groupBy?: HarvestStatGroupBy) => string;
  /** "Saison en cours"… An empty string for a period this front does not know (undefined). */
  harvestPeriodLabel: (period?: HarvestPeriod) => string;
}

/** The names of what the harvest statistics are grouped by, and of their periods. */
export function useHarvestStatLabels(): UseHarvestStatLabels {
  const { t } = useI18nExtended({ messages, rootKey: TRANSLATION_KEY, prefixed: true });

  return {
    harvestStatGroupByLabel: (groupBy) => (groupBy ? t(`group_by.${groupBy}`) : ''),
    harvestPeriodLabel: (period) => (period ? t(`period.${period}`) : ''),
  };
}
